 {{-- @include('maintenance-requests.partials.alerts') --}}
 <div class="rounded-2xl border border-gray-200 bg-white p-6 dark:border-gray-800 dark:bg-white/[0.03]">
     <h3 class="mb-4 text-sm font-semibold text-gray-800 dark:text-white/90">
         <i class="bi bi-lightning me-2"></i>Quick Actions
     </h3>

     <div class="space-y-3">
         @can('maintenance_requests.assign')
             @if ($maintenanceRequest->assigned_to === null || $maintenanceRequest->status === 'not_fixed')
                 <div x-data="{ showAssignModal: false }">
                     <button @click="showAssignModal = true"
                         class="flex w-full items-center rounded-lg border border-gray-200 bg-white px-4 py-3 text-sm font-medium text-gray-700 shadow-theme-xs hover:bg-gray-50 dark:border-gray-700 dark:bg-gray-800 dark:text-gray-400 dark:hover:bg-white/[0.03]">
                         <i class="bi bi-person-plus me-3"></i>
                         {{ is_null($maintenanceRequest->assigned_to) ? 'Assign to Technician' : 'Re-Assign Technician' }}
                     </button>

                     <!-- Assign Technician Modal -->
                     <div x-show="showAssignModal" x-cloak style="display: none;"
                         class="fixed inset-0 z-50 flex items-center justify-center bg-black bg-opacity-50">
                         <div class="bg-white dark:bg-gray-800 rounded-lg p-6 w-full max-w-md">
                             <div class="flex justify-between items-center mb-4">
                                 <h3 class="text-lg font-semibold text-gray-800 dark:text-white/90">
                                     Assign Request to Technician
                                 </h3>
                                 <button @click="showAssignModal = false"
                                     class="text-gray-500 hover:text-gray-700 dark:text-gray-400 dark:hover:text-gray-200">
                                     <i class="bi bi-x-lg"></i>
                                 </button>
                             </div>

                             <form action="{{ route('maintenance-requests.assign', $maintenanceRequest) }}" method="POST"
                                 class="mt-4" id="assign-technician-form">
                                 @csrf
                                 @method('PUT')
                                 <div class="space-y-4">
                                     <div>
                                         <label class="mb-2 block text-sm font-medium text-gray-700 dark:text-gray-300">
                                             Select Technician
                                         </label>
                                         <select name="assigned_to" required
                                             class="h-11 w-full rounded-lg border border-gray-300 bg-transparent px-4 py-2.5 text-sm text-gray-800 shadow-theme-xs focus:border-blue-300 focus:outline-none focus:ring-2 focus:ring-blue-500/10 dark:border-gray-700 dark:bg-gray-900 dark:text-white/90 dark:focus:border-blue-800">
                                             <option value="" disabled
                                                 {{ !$maintenanceRequest->assigned_to ? 'selected' : '' }}>Select
                                                 technician...</option>
                                             @foreach ($technicians as $id => $name)
                                                 <option value="{{ $id }}"
                                                     {{ $maintenanceRequest->assigned_to == $id ? 'selected' : '' }}>
                                                     {{ $name }}
                                                 </option>
                                             @endforeach
                                         </select>
                                         <p class="mt-1 text-xs text-gray-500 dark:text-gray-400">
                                             Only users with 'maintenance_requests.resolve' permission are listed
                                         </p>
                                     </div>

                                     <div>
                                         <label class="mb-2 block text-sm font-medium text-gray-700 dark:text-gray-300">
                                             Technician Notes (Optional)
                                         </label>
                                         <textarea name="technician_notes" rows="3"
                                             class="w-full rounded-lg border border-gray-300 bg-transparent px-4 py-2.5 text-sm text-gray-800 shadow-theme-xs placeholder:text-gray-400 focus:border-blue-300 focus:outline-none focus:ring-2 focus:ring-blue-500/10 dark:border-gray-700 dark:bg-gray-900 dark:text-white/90 dark:placeholder:text-white/30 dark:focus:border-blue-800"
                                             placeholder="Add any notes for the technician...">{{ $maintenanceRequest->technician_notes }}</textarea>
                                     </div>

                                     <div class="flex justify-end gap-3">
                                         <button @click="showAssignModal = false" type="button"
                                             class="rounded-lg border border-gray-300 bg-white px-4 py-2.5 text-sm font-medium text-gray-700 shadow-theme-xs hover:bg-gray-50 dark:border-gray-700 dark:bg-gray-800 dark:text-gray-400 dark:hover:bg-white/[0.03]">
                                             Cancel
                                         </button>
                                         <button type="submit"
                                             class="rounded-lg bg-blue-500 px-4 py-2.5 text-sm font-medium text-white shadow-theme-xs hover:bg-blue-600 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:ring-offset-2 dark:focus:ring-offset-gray-900">
                                             Assign Technician
                                         </button>
                                     </div>
                                 </div>
                             </form>
                         </div>
                     </div>
                 </div>
             @endif
         @endcan

         <!-- Rest of the buttons remain the same -->
         @if ($maintenanceRequest->status === 'confirmed' && auth()->id() === $maintenanceRequest->assigned_to)
             <a href="{{ route('maintenance.report', $maintenanceRequest) }}"
                 class="flex w-full items-center rounded-lg border border-gray-200 bg-white px-4 py-3 text-sm font-medium text-gray-700 shadow-theme-xs hover:bg-gray-50 dark:border-gray-700 dark:bg-gray-800 dark:text-gray-400 dark:hover:bg-white/[0.03]">
                 <i class="bi bi-file-earmark-pdf"></i> Download Report
             </a>
         @endif
         <!-- ADD THIS: Work Log Button (for assigned technician) -->
         @if (auth()->user()->id == $maintenanceRequest->assigned_to &&
                 in_array($maintenanceRequest->status, ['assigned', 'in_progress', 'approved', 'pending', 'not_fixed']))
             <button @click="$dispatch('open-worklog-submit-modal')"
                 class="flex w-full items-center justify-center rounded-xl border border-green-200 bg-green-50 px-4 py-3 text-sm font-semibold text-green-700 shadow-sm transition-all hover:bg-green-100 dark:border-green-800/30 dark:bg-green-500/10 dark:text-green-400">
                 <i class="bi bi-journal-plus me-2 text-lg"></i>
                 Start Your Task
             </button>
         @endif
         <!-- Update Status Button -->
         @can('maintenance_requests.view_assigned' && 'maintenance_requests.update')
             @if (in_array($maintenanceRequest->status, ['assigned', 'in_progress', 'not_fixed']))
                 <button @click="$dispatch('open-update-status-modal')"
                     class="flex w-full items-center rounded-lg border border-gray-200 bg-white px-4 py-3 text-sm font-medium text-gray-700 shadow-theme-xs hover:bg-gray-50 dark:border-gray-700 dark:bg-gray-800 dark:text-gray-400 dark:hover:bg-white/[0.03]">
                     <i class="bi bi-arrow-repeat me-3"></i>
                     Update Status
                 </button>
             @endif
         @endcan

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
 @push('scripts')
     <script>
         // Simple function that uses querySelector
         function openWorkLogSubmitModal() {
             console.log('Trying to open work log modal...');

             // Try to find any element with x-data containing showWorkLogModal
             const elements = document.querySelectorAll('[x-data]');
             let found = false;

             elements.forEach(element => {
                 if (element.__x) {
                     const dataStr = element.__x.$data.toString();
                     if (dataStr.includes('showWorkLogModal')) {
                         console.log('Found work log modal component');
                         element.__x.$data.showWorkLogModal = true;
                         found = true;
                     }
                 }
             });

             if (!found) {
                 console.error('Work log modal not found');
                 // Try a more direct approach
                 const modalDiv = document.querySelector('div[x-show*="showWorkLogModal"]');
                 if (modalDiv) {
                     modalDiv.style.display = 'block';
                 }
             }
         }
     </script>
 @endpush
