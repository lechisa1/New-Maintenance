{{-- Quick Actions Card --}}
<div class="rounded-2xl border border-gray-200 bg-white p-6 dark:border-gray-800 dark:bg-white/[0.03]">
    <h3 class="mb-4 text-sm font-semibold text-gray-800 dark:text-white/90">
        <i class="bi bi-lightning me-2"></i>Quick Actions
    </h3>

    <div class="space-y-3">
        {{-- ASSIGN TO TECHNICIAN BUTTON --}}
        @can('maintenance_requests.assign')
            @php
                // Get all active technician assignments for this request
                $activeAssignments = $maintenanceRequest
                    ->assignedTechnicians()
                    ->whereIn('status', ['assigned', 'in_progress'])
                    ->count();

                // Check if there are any items not assigned to any technician
                $assignedItemIds = $maintenanceRequest
                    ->assignedTechnicians()
                    ->whereIn('status', ['assigned', 'in_progress'])
                    ->get()
                    ->flatMap(function ($assignment) {
                        return $assignment->item_ids ?? [];
                    })
                    ->unique()
                    ->toArray();

                $totalItems = $maintenanceRequest->items->count();
                $allItemsAssigned = count($assignedItemIds) === $totalItems && $totalItems > 0;

                // Show assign button if:
                // 1. No technicians are assigned yet, OR
                // 2. Request is marked as 'not_fixed' (needs reassignment), OR
                // 3. Not all items are assigned to technicians
                $showAssignButton =
                    $activeAssignments === 0 || $maintenanceRequest->status === 'not_fixed' || !$allItemsAssigned;
            @endphp

            @if ($showAssignButton)
                <!-- Assign Technician Modal -->
                <div x-data="{
                    showAssignModal: false,
                    selectedTechnicians: [],
                    selectedItems: [],
                    items: {{ json_encode(
                        $maintenanceRequest->items->map(function ($item) {
                            return [
                                'id' => $item->item_id,
                                'name' => $item->item->name,
                                'issue_type' => $item->issueType->name,
                            ];
                        }),
                    ) }},
                    technicians: {{ json_encode($technicians) }},
                    assignedItemIds: {{ json_encode($assignedItemIds) }},
                
                    // Get items that are already assigned (for visual indication)
                    get alreadyAssignedItems() {
                        return this.items.filter(item => this.assignedItemIds.includes(item.id));
                    },
                
                    // All items are available for selection (including already assigned ones)
                    get availableItems() {
                        return this.items;
                    },
                
                    selectAllAvailable() {
                        this.selectedItems = this.availableItems.map(item => item.id);
                    },
                
                    selectAllTechnicians() {
                        this.selectedTechnicians = Object.keys(this.technicians).map(key => parseInt(key));
                    }
                }">
                    <button @click="showAssignModal = true; selectedItems = []; selectedTechnicians = []"
                        class="flex w-full items-center rounded-lg border border-gray-200 bg-white px-4 py-3 text-sm font-medium text-gray-700 shadow-theme-xs hover:bg-gray-50 dark:border-gray-700 dark:bg-gray-800 dark:text-gray-400">
                        <i class="bi bi-people me-3"></i>
                        @if ($activeAssignments === 0)
                            Assign Technician(s)
                        @elseif($maintenanceRequest->status === 'not_fixed')
                            Re-Assign Technician(s)
                        @else
                            Add More Technicians
                        @endif
                    </button>

                    <!-- Modal content (same as before) -->
                    <div x-show="showAssignModal" x-cloak @click.away="showAssignModal = false"
                        class="fixed inset-0 z-50 flex items-center justify-center bg-black bg-opacity-50">
                        <!-- ... rest of modal content remains exactly the same ... -->
                        <div class="bg-white dark:bg-gray-800 rounded-lg p-6 w-full max-w-2xl max-h-[90vh] overflow-y-auto">
                            <div class="flex justify-between items-center mb-4">
                                <h3 class="text-lg font-semibold text-gray-800 dark:text-white/90">
                                    @if ($activeAssignments === 0)
                                        Assign Technicians to Items
                                    @elseif($maintenanceRequest->status === 'not_fixed')
                                        Re-Assign Technicians to Items
                                    @else
                                        Add More Technicians
                                    @endif
                                </h3>
                                <button @click="showAssignModal = false"
                                    class="text-gray-500 hover:text-gray-700 dark:text-gray-400">
                                    <i class="bi bi-x-lg"></i>
                                </button>
                            </div>

                            <form action="{{ route('maintenance-requests.assign', $maintenanceRequest) }}" method="POST"
                                @submit="if(selectedItems.length === 0) { $event.preventDefault(); alert('Please select at least one item.'); } else if(selectedTechnicians.length === 0) { $event.preventDefault(); alert('Please select at least one technician.'); }">
                                @csrf
                                @method('PUT')

                                <div class="space-y-6">
                                    <!-- Technicians Selection (Multiple) -->
                                    <div>
                                        <div class="flex justify-between items-center mb-2">
                                            <label class="text-sm font-medium text-gray-700 dark:text-gray-300">
                                                Select Technicians <span class="text-red-500">*</span>
                                            </label>
                                            <button type="button" @click="selectAllTechnicians"
                                                class="text-xs text-blue-600 hover:text-blue-800">
                                                Select All
                                            </button>
                                        </div>

                                        <div class="border rounded-lg p-3 max-h-48 overflow-y-auto dark:border-gray-700">
                                            <template x-for="(name, id) in technicians" :key="id">
                                                <label
                                                    class="flex items-center gap-3 p-2 hover:bg-gray-50 dark:hover:bg-gray-700 rounded">
                                                    <input type="checkbox" :value="id"
                                                        x-model="selectedTechnicians"
                                                        class="rounded border-gray-300 text-blue-600">
                                                    <div>
                                                        <div class="text-sm font-medium" x-text="name"></div>
                                                    </div>
                                                </label>
                                            </template>
                                        </div>

                                        <template x-for="techId in selectedTechnicians" :key="techId">
                                            <input type="hidden" name="assigned_technicians[]" :value="techId">
                                        </template>
                                    </div>

                                    <!-- Items Selection -->
                                    <div>
                                        <div class="flex justify-between items-center mb-2">
                                            <label class="text-sm font-medium text-gray-700 dark:text-gray-300">
                                                Select Items to Assign <span class="text-red-500">*</span>
                                            </label>
                                            <button type="button" @click="selectAllAvailable"
                                                class="text-xs text-blue-600 hover:text-blue-800"
                                                x-show="availableItems.length > 0">
                                                Select All
                                            </button>
                                        </div>

                                        <div class="border rounded-lg p-3 max-h-48 overflow-y-auto dark:border-gray-700">
                                            <template x-for="item in availableItems" :key="item.id">
                                                <label
                                                    class="flex items-center gap-3 p-2 hover:bg-gray-50 dark:hover:bg-gray-700 rounded relative">
                                                    <input type="checkbox" :value="item.id" x-model="selectedItems"
                                                        class="rounded border-gray-300 text-blue-600">
                                                    <div class="flex-1">
                                                        <div class="flex items-center justify-between">
                                                            <div>
                                                                <span class="text-sm font-medium" x-text="item.name"></span>
                                                                <span class="text-xs text-gray-500 ml-2"
                                                                    x-text="item.issue_type"></span>
                                                            </div>
                                                            <!-- Show indicator if item is already assigned -->
                                                            <template x-if="assignedItemIds.includes(item.id)">
                                                                <span
                                                                    class="text-xs bg-yellow-100 text-yellow-800 px-2 py-0.5 rounded-full ml-2">
                                                                    Already Assigned
                                                                </span>
                                                            </template>
                                                        </div>
                                                    </div>
                                                </label>
                                            </template>

                                            <div x-show="availableItems.length === 0"
                                                class="text-sm text-gray-500 p-3 text-center">
                                                No items available.
                                            </div>
                                        </div>

                                        <template x-for="itemId in selectedItems" :key="itemId">
                                            <input type="hidden" name="assigned_items[]" :value="itemId">
                                        </template>
                                    </div>

                                    <!-- Notes -->
                                    <div>
                                        <label class="mb-2 block text-sm font-medium text-gray-700 dark:text-gray-300">
                                            Notes for Technicians (Optional)
                                        </label>
                                        <textarea name="technician_notes" rows="3" class="w-full rounded-lg border border-gray-300 px-4 py-2.5 text-sm"
                                            placeholder="Add any notes for the technicians..."></textarea>
                                    </div>

                                    <!-- Summary -->
                                    <div class="bg-blue-50 dark:bg-blue-900/20 p-3 rounded-lg">
                                        <div class="text-sm text-blue-800 dark:text-blue-300">
                                            <span x-text="selectedTechnicians.length"></span> technician(s) selected,
                                            <span x-text="selectedItems.length"></span> item(s) selected
                                        </div>
                                        <div class="text-xs text-gray-600 dark:text-gray-400 mt-1">
                                            Items already assigned to other technicians can be assigned to additional
                                            technicians for collaboration.
                                        </div>
                                    </div>

                                    <!-- Actions -->
                                    <div class="flex justify-end gap-3 pt-4 border-t border-gray-200 dark:border-gray-700">
                                        <button type="button" @click="showAssignModal = false"
                                            class="px-4 py-2 text-sm font-medium text-gray-700 bg-white border rounded-lg hover:bg-gray-50 dark:bg-gray-800 dark:text-gray-400">
                                            Cancel
                                        </button>
                                        <button type="submit"
                                            class="px-4 py-2 text-sm font-medium text-white bg-blue-600 rounded-lg hover:bg-blue-700 disabled:opacity-50"
                                            :disabled="selectedItems.length === 0 || selectedTechnicians.length === 0">
                                            @if ($activeAssignments === 0)
                                                Assign to Selected Technicians
                                            @elseif($maintenanceRequest->status === 'not_fixed')
                                                Re-Assign to Selected Technicians
                                            @else
                                                Add Selected Technicians
                                            @endif
                                        </button>
                                    </div>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>
            @endif
        @endcan

        {{-- REJECT REQUEST BUTTON (for users with assign permission) --}}
        @can('maintenance_requests.assign')
            @if (in_array($maintenanceRequest->status, ['pending', 'assigned', 'in_progress']))
                <button @click="$dispatch('open-reject-assigner-modal')"
                    class="flex w-full items-center rounded-lg border border-red-200 bg-white px-4 py-3 text-sm font-medium text-red-700 shadow-theme-xs hover:bg-red-50 dark:border-red-700 dark:bg-gray-800 dark:text-red-400 dark:hover:bg-red-900/20">
                    <i class="bi bi-x-circle me-3"></i>
                    Reject Request
                </button>
            @endif
        @endcan

        @php
            // Check if current user is assigned to any items in this request
            $userAssignment = $maintenanceRequest
                ->assignedTechnicians()
                ->where('user_id', auth()->id())
                ->first();

            $hasActiveAssignment = $userAssignment && count($userAssignment->item_ids ?? []) > 0;
        @endphp

        @if ($userAssignment && $userAssignment->status === 'assigned')
            <button @click="$dispatch('open-worklog-modal')"
                class="flex w-full items-center justify-center rounded-xl border border-green-200 bg-green-50 px-4 py-3 text-sm font-semibold text-green-700 shadow-sm transition-all hover:bg-green-100 dark:border-green-800/30 dark:bg-green-500/10 dark:text-green-400">
                <i class="bi bi-journal-plus me-2 text-lg"></i>
                Start Your Task
                <span class="ml-2 text-xs bg-green-200 text-green-800 px-2 py-0.5 rounded-full">
                    {{ count($userAssignment->item_ids ?? []) }} items
                </span>
            </button>
        @endif



        @if ($maintenanceRequest->status === 'confirmed' && $hasActiveAssignment)
            <a href="{{ route('maintenance.report', $maintenanceRequest) }}"
                class="flex w-full items-center rounded-lg border border-gray-200 bg-white px-4 py-3 text-sm font-medium text-gray-700 shadow-theme-xs hover:bg-gray-50 dark:border-gray-700 dark:bg-gray-800 dark:text-gray-400 dark:hover:bg-white/[0.03]">
                <i class="bi bi-file-earmark-pdf me-3"></i>
                Download Report
            </a>
        @endif

        {{-- UPDATE STATUS BUTTON --}}
        @can('maintenance_requests.view_assigned')
            @if (in_array($maintenanceRequest->status, ['assigned', 'in_progress', 'not_fixed']))
                <button @click="$dispatch('open-update-status-modal')"
                    class="flex w-full items-center rounded-lg border border-gray-200 bg-white px-4 py-3 text-sm font-medium text-gray-700 shadow-theme-xs hover:bg-gray-50 dark:border-gray-700 dark:bg-gray-800 dark:text-gray-400 dark:hover:bg-white/[0.03]">
                    <i class="bi bi-arrow-repeat me-3"></i>
                    Update Status
                </button>
            @endif
        @endcan

        {{-- NAVIGATION BUTTONS --}}
        @can('maintenance_requests.create')
            <a href="{{ route('maintenance-requests.create') }}"
                class="flex items-center rounded-lg border border-gray-200 bg-white px-4 py-3 text-sm font-medium text-gray-700 shadow-theme-xs hover:bg-gray-50 dark:border-gray-700 dark:bg-gray-800 dark:text-gray-400 dark:hover:bg-white/[0.03]">
                <i class="bi bi-plus-lg me-3"></i>
                Submit New Request
            </a>
        @endcan

        @can('maintenance_requests.view-all')
            <a href="{{ route('maintenance-requests.index') }}"
                class="flex items-center rounded-lg border border-gray-200 bg-white px-4 py-3 text-sm font-medium text-gray-700 shadow-theme-xs hover:bg-gray-50 dark:border-gray-700 dark:bg-gray-800 dark:text-gray-400 dark:hover:bg-white/[0.03]">
                <i class="bi bi-list-ul me-3"></i>
                View All Requests
            </a>
        @endcan
    </div>
</div>

{{-- WORK LOG SUBMIT MODAL --}}
<div x-data="workLogSubmitModal()" @open-worklog-modal.window="showWorkLogModal = true; initializeItems();">

    <div x-show="showWorkLogModal" x-cloak class="fixed inset-0 z-[999] overflow-y-auto"
        aria-labelledby="modal-title" role="dialog" aria-modal="true">

        <!-- Backdrop with blur -->
        <div x-show="showWorkLogModal" x-transition:enter="transition ease-out duration-300"
            x-transition:enter-start="opacity-0" x-transition:enter-end="opacity-100"
            x-transition:leave="transition ease-in duration-200" @click="closeModal"
            class="fixed inset-0 bg-gray-900/60 backdrop-blur-sm"></div>

        <!-- Modal Container -->
        <div class="flex min-h-full items-center justify-center p-4 text-center sm:p-0">
            <div x-show="showWorkLogModal" x-transition:enter="transition ease-out duration-300"
                x-transition:enter-start="opacity-0 translate-y-4 sm:translate-y-0 sm:scale-95"
                x-transition:enter-end="opacity-100 translate-y-0 sm:scale-100"
                x-transition:leave="transition ease-in duration-200"
                x-transition:leave-start="opacity-100 translate-y-0 sm:scale-100"
                x-transition:leave-end="opacity-0 translate-y-4 sm:translate-y-0 sm:scale-95"
                class="relative w-full max-w-2xl transform overflow-hidden rounded-2xl bg-white text-left shadow-2xl transition-all dark:bg-gray-900">

                <!-- Modal Header -->
                <div class="flex items-center justify-between border-b border-gray-100 px-6 py-4 dark:border-gray-800">
                    <h3 class="text-lg font-bold text-gray-900 dark:text-white">
                        <i class="bi bi-journal-plus me-2 text-blue-500"></i>Submit Work Progress
                    </h3>
                    <button @click="closeModal" class="text-gray-400 hover:text-gray-600 dark:hover:text-gray-200">
                        <i class="bi bi-x-lg"></i>
                    </button>
                </div>

                <!-- Modal Body -->
                <form @submit.prevent="submitWorkLog" class="max-h-[80vh] overflow-y-auto p-6">
                    @csrf
                    <input type="hidden" name="request_id" value="{{ $maintenanceRequest->id }}">

                    <div class="space-y-6">
                        <!-- Items Worked On Section -->
                        <div
                            class="rounded-xl border border-gray-200 bg-gray-50/30 p-4 dark:border-gray-800 dark:bg-gray-800/30">
                            <h4 class="mb-3 text-sm font-semibold text-gray-700 dark:text-gray-300">
                                <i class="bi bi-tools me-2 text-blue-500"></i>Items Worked On
                            </h4>

                            <div class="space-y-3 max-h-60 overflow-y-auto pr-2">
                                <template x-for="(item, index) in items" :key="item.id">
                                    <div
                                        class="rounded-lg border border-gray-200 bg-white p-3 dark:border-gray-700 dark:bg-gray-800">
                                        <div class="flex items-start justify-between">
                                            <div class="flex-1">
                                                <div class="flex items-center gap-2">
                                                    <input type="checkbox" :id="'item_' + item.id"
                                                        x-model="item.selected"
                                                        class="h-4 w-4 rounded border-gray-300 text-blue-600 focus:ring-blue-500">
                                                    <label :for="'item_' + item.id"
                                                        class="font-medium text-gray-800 dark:text-white/90"
                                                        x-text="item.name"></label>
                                                </div>
                                                <div class="mt-1 ml-6 text-xs text-gray-500 dark:text-gray-400">
                                                    <span class="font-medium">Issue Types:</span>
                                                    <span x-text="item.issue_types.join(', ')"></span>
                                                </div>
                                            </div>
                                            <span class="text-xs bg-blue-100 text-blue-800 px-2 py-1 rounded-full"
                                                x-show="item.selected" x-text="'Selected'"></span>
                                        </div>

                                        <!-- Item-specific notes -->
                                        <div x-show="item.selected" class="mt-3 ml-6">
                                            <textarea x-model="item.notes" rows="2"
                                                class="w-full rounded-lg border border-gray-200 bg-gray-50/50 px-3 py-2 text-xs dark:border-gray-700 dark:bg-gray-900 dark:text-white"
                                                placeholder="Specific work done on this item..."></textarea>
                                        </div>
                                    </div>
                                </template>

                                <div x-show="!hasSelectedItems"
                                    class="text-sm text-amber-600 bg-amber-50 p-3 rounded-lg">
                                    <i class="bi bi-exclamation-triangle me-2"></i>
                                    Please select at least one item you worked on.
                                </div>
                            </div>
                        </div>

                        <!-- Updated Request Status -->
                        <div>
                            <label class="mb-3 block text-sm font-semibold text-gray-700 dark:text-gray-300">
                                Updated Request Status <span class="text-red-500">*</span>
                            </label>
                            <div class="grid grid-cols-1 gap-3 sm:grid-cols-3">
                                @foreach (['in_progress' => ['blue', 'tools', 'In Progress'], 'completed' => ['green', 'check-circle', 'Completed'], 'not_fixed' => ['red', 'x-circle', 'Not Fixed']] as $val => $meta)
                                    <label class="relative flex cursor-pointer group">
                                        <input type="radio" name="new_status" value="{{ $val }}"
                                            x-model="form.new_status" class="peer sr-only">
                                        <div
                                            class="flex w-full flex-col items-center rounded-xl border border-gray-200 p-3 transition-all peer-checked:border-{{ $meta[0] }}-500 peer-checked:bg-{{ $meta[0] }}-50/50 dark:border-gray-800 dark:peer-checked:bg-{{ $meta[0] }}-500/10">
                                            <i
                                                class="bi bi-{{ $meta[1] }} mb-1 text-{{ $meta[0] }}-500 text-xl"></i>
                                            <span
                                                class="text-xs font-bold text-gray-700 dark:text-gray-300 uppercase tracking-tight">{{ $meta[2] }}</span>
                                        </div>
                                    </label>
                                @endforeach
                            </div>
                        </div>

                        <!-- Work Done -->
                        <div>
                            <label class="mb-2 block text-sm font-semibold text-gray-700 dark:text-gray-300">
                                Detailed Work Description <span class="text-red-500">*</span>
                            </label>
                            <textarea x-model="form.work_done" rows="3" required
                                class="w-full rounded-xl border border-gray-200 bg-gray-50/50 px-4 py-3 text-sm focus:border-blue-500 focus:ring-4 focus:ring-blue-500/10 dark:border-gray-800 dark:bg-gray-800/50 dark:text-white"
                                placeholder="Provide a professional summary of tasks completed..."></textarea>
                        </div>

                        <!-- Materials Used & Additional Notes Grid -->
                        <div class="grid grid-cols-1 gap-4 md:grid-cols-2">
                            <div>
                                <label class="mb-2 block text-sm font-semibold text-gray-700 dark:text-gray-300">
                                    Materials Used
                                </label>
                                <textarea x-model="form.materials_used" rows="2"
                                    class="w-full rounded-xl border border-gray-200 bg-gray-50/50 px-4 py-3 text-sm dark:border-gray-800 dark:bg-gray-800/50 dark:text-white"
                                    placeholder="Parts or tools used..."></textarea>
                            </div>
                            <div>
                                <label class="mb-2 block text-sm font-semibold text-gray-700 dark:text-gray-300">
                                    Additional Notes
                                </label>
                                <textarea x-model="form.completion_notes" rows="2"
                                    class="w-full rounded-xl border border-gray-200 bg-gray-50/50 px-4 py-3 text-sm dark:border-gray-800 dark:bg-gray-800/50 dark:text-white"
                                    placeholder="Challenges or observations..."></textarea>
                            </div>
                        </div>

                        <!-- Time Spent & Completion Date Grid -->
                        <div class="grid grid-cols-1 gap-4 md:grid-cols-2">
                            <div
                                class="rounded-xl border border-gray-100 bg-gray-50/30 p-4 dark:border-gray-800 dark:bg-gray-800/30">
                                <label class="mb-2 block text-sm font-semibold text-gray-700 dark:text-gray-300">
                                    Time Spent <span class="text-red-500">*</span>
                                </label>
                                <div class="flex items-center gap-2">
                                    <input type="number" x-model="form.hours" min="0" max="8"
                                        class="w-20 rounded-lg border-gray-200 text-center dark:bg-gray-900 dark:text-white"
                                        placeholder="Hrs">
                                    <span class="font-bold">:</span>
                                    <select x-model="form.minutes"
                                        class="flex-1 rounded-lg border-gray-200 dark:bg-gray-900 dark:text-white">
                                        <option value="0">00 mins</option>
                                        <option value="15">15 mins</option>
                                        <option value="30">30 mins</option>
                                        <option value="45">45 mins</option>
                                    </select>
                                </div>
                                <input type="hidden" name="time_spent_minutes"
                                    :value="(form.hours * 60) + form.minutes">
                            </div>

                            <div
                                class="rounded-xl border border-gray-100 bg-gray-50/30 p-4 dark:border-gray-800 dark:bg-gray-800/30">
                                <label class="mb-2 block text-sm font-semibold text-gray-700 dark:text-gray-300">
                                    Completion Date <span class="text-red-500">*</span>
                                </label>
                                <input type="date" x-model="form.log_date"
                                    class="w-full rounded-lg border-gray-200 py-2 dark:bg-gray-900 dark:text-white"
                                    required>
                            </div>
                        </div>

                        <!-- Error Display -->
                        <div x-show="error"
                            class="text-sm text-red-600 bg-red-50 p-3 rounded-xl border border-red-200"
                            x-text="error"></div>
                    </div>

                    <!-- Form Actions -->
                    <div
                        class="mt-8 flex items-center justify-end gap-3 border-t border-gray-100 pt-6 dark:border-gray-800">
                        <button type="button" @click="closeModal"
                            class="text-sm font-medium text-gray-500 hover:text-gray-700">
                            Cancel
                        </button>
                        <button type="submit"
                            class="flex items-center rounded-xl bg-blue-600 px-6 py-2.5 text-sm font-bold text-white shadow-lg shadow-blue-500/20 transition-all hover:bg-blue-700 active:scale-95 disabled:opacity-50 disabled:cursor-not-allowed"
                            :disabled="!hasSelectedItems || loading">
                            <span x-show="!loading">
                                <i class="bi bi-cloud-arrow-up me-2"></i> Save Progress
                            </span>
                            <span x-show="loading" class="flex items-center">
                                <svg class="animate-spin -ml-1 mr-2 h-4 w-4 text-white"
                                    xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                                    <circle class="opacity-25" cx="12" cy="12" r="10"
                                        stroke="currentColor" stroke-width="4"></circle>
                                    <path class="opacity-75" fill="currentColor"
                                        d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z">
                                    </path>
                                </svg>
                                Saving...
                            </span>
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>


@push('scripts')
    <script>
        function workLogSubmitModal() {
            return {
                showWorkLogModal: false,
                loading: false,
                error: null,
                items: {!! json_encode(
                    $maintenanceRequest->items->map(function ($item) {
                            return [
                                'id' => $item->item_id,
                                'name' => $item->item?->name ?? 'N/A',
                                'issue_types' => $item->issueType ? [$item->issueType->name] : [],
                                'selected' => true,
                                'notes' => '',
                            ];
                        })->values()->toArray(),
                ) !!},
                form: {
                    work_done: '',
                    materials_used: '',
                    time_spent_minutes: 60,
                    new_status: 'in_progress',
                    log_date: '{{ date('Y-m-d') }}',
                    completion_notes: ''
                },

                init() {
                    window.addEventListener('open-worklog-modal', () => {
                        this.showWorkLogModal = true;
                        this.initializeItems();
                    });
                },

                initializeItems() {
                    this.items = this.items.map(item => ({
                        ...item,
                        selected: true,
                        notes: ''
                    }));
                    this.error = null;
                },

                get hasSelectedItems() {
                    return this.items.some(item => item.selected);
                },

                closeModal() {
                    this.showWorkLogModal = false;
                    this.resetForm();
                },

                resetForm() {
                    this.form = {
                        work_done: '',
                        materials_used: '',
                        time_spent_minutes: 60,
                        new_status: 'in_progress',
                        log_date: '{{ date('Y-m-d') }}',
                        completion_notes: ''
                    };
                    this.error = null;
                },

                submitWorkLog() {
                    if (!this.hasSelectedItems) {
                        this.error = 'Please select at least one item you worked on.';
                        return;
                    }

                    this.loading = true;
                    this.error = null;

                    const formData = new FormData();
                    formData.append('request_id', '{{ $maintenanceRequest->id }}');
                    formData.append('work_done', this.form.work_done);
                    formData.append('materials_used', this.form.materials_used);
                    formData.append('time_spent_minutes', this.form.time_spent_minutes);
                    formData.append('new_status', this.form.new_status);
                    formData.append('log_date', this.form.log_date);
                    formData.append('completion_notes', this.form.completion_notes);

                    const selectedItems = this.items.filter(item => item.selected);
                    selectedItems.forEach(item => {
                        formData.append('item_ids[]', item.id);
                        if (item.notes && item.notes.trim()) {
                            formData.append(`item_notes[${item.id}]`, item.notes);
                        }
                    });

                    fetch('{{ route('work-logs.store') }}', {
                            method: 'POST',
                            headers: {
                                'X-CSRF-TOKEN': '{{ csrf_token() }}',
                                'Accept': 'application/json',
                                'X-Requested-With': 'XMLHttpRequest'
                            },
                            body: formData
                        })
                        .then(response => {
                            if (!response.ok) {
                                return response.json().then(err => {
                                    throw err;
                                });
                            }
                            return response.json();
                        })
                        .then(data => {
                            if (data.success) {
                                this.closeModal();
                                alert('Work log submitted successfully!');
                                setTimeout(() => window.location.reload(), 1500);
                            } else {
                                this.error = data.message || 'Failed to submit work log.';
                            }
                        })
                        .catch(err => {
                            console.error('Error:', err);
                            this.error = err.message || 'An error occurred. Please try again.';
                        })
                        .finally(() => {
                            this.loading = false;
                        });
                }
            }
        }
    </script>
@endpush
