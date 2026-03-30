@php
    // Check if any item in the request requires approval
    $needsApproval = false;
    foreach ($maintenanceRequest->items as $requestItem) {
        if ($requestItem->issueType && $requestItem->issueType->is_need_approval) {
            $needsApproval = true;
            break;
        }
    }

    // Check if request has "Other" issue type that might need approval determination
    $hasOtherIssueType = false;
    foreach ($maintenanceRequest->items as $requestItem) {
        if ($requestItem->issueType && $requestItem->issueType->name === 'Other') {
            $hasOtherIssueType = true;
            break;
        }
    }

    // Determine if approval buttons should be shown
    $showApprovalButtons = false;

    if ($needsApproval) {
        // Only show approval buttons if status is pending or waiting_approval
        if (in_array($maintenanceRequest->status, ['pending', 'waiting_approval'])) {
            $approver = $maintenanceRequest->getNextApprover();
            $canApprove =
                ($approver && auth()->id() == $approver->id) || auth()->user()->can('maintenance_requests.approve');

            $showApprovalButtons = $canApprove;
        }
    }

    // Check if current user is a technician assigned to this request
    $isAssignedTechnician = false;
    if ($maintenanceRequest->relationLoaded('assignedTechnicians')) {
        $isAssignedTechnician = $maintenanceRequest->assignedTechnicians
            ->where('user_id', auth()->id())
            ->whereIn('status', ['assigned', 'in_progress'])
            ->isNotEmpty();
    } else {
        // Fallback to query if relationship not loaded
        $isAssignedTechnician = $maintenanceRequest
            ->assignedTechnicians()
            ->where('user_id', auth()->id())
            ->whereIn('status', ['assigned', 'in_progress'])
            ->exists();
    }
@endphp

{{-- TECHNICIAN REQUEST APPROVAL BUTTON --}}
@if ($isAssignedTechnician && $hasOtherIssueType && $maintenanceRequest->status === 'assigned')
    <div x-data="{ showRequestApprovalModal: false, selectedIssueType: '', notes: '' }">
        <button @click="showRequestApprovalModal = true"
            class="flex w-full items-center justify-center rounded-lg border border-purple-200 bg-purple-50 px-4 py-3 text-sm font-medium text-purple-700 shadow-theme-xs hover:bg-purple-100 hover:text-purple-800 dark:border-purple-800 dark:bg-purple-900/20 dark:text-purple-400 dark:hover:bg-purple-900/30">
            <i class="bi bi-question-circle me-2"></i>
            Request Approval for Issue Type
        </button>

        <!-- Request Approval Modal -->
        <div x-show="showRequestApprovalModal" x-cloak
            class="fixed inset-0 z-50 flex items-center justify-center bg-black bg-opacity-50">
            <div class="bg-white dark:bg-gray-800 rounded-lg p-6 w-full max-w-md">
                <div class="flex justify-between items-center mb-4">
                    <h3 class="text-lg font-semibold text-gray-800 dark:text-white/90">
                        Request Issue Type Approval
                    </h3>
                    <button @click="showRequestApprovalModal = false"
                        class="text-gray-500 hover:text-gray-700 dark:text-gray-400">
                        <i class="bi bi-x-lg"></i>
                    </button>
                </div>

                <form action="{{ route('maintenance-requests.request-approval', $maintenanceRequest) }}" method="POST">
                    @csrf
                    <div class="space-y-4">
                        <!-- Select Issue Type -->
                        <div>
                            <label class="mb-2 block text-sm font-medium text-gray-700 dark:text-gray-300">
                                Select Issue Type <span class="text-red-500">*</span>
                            </label>
                            <select name="issue_type_id" x-model="selectedIssueType" required
                                class="w-full rounded-lg border border-gray-300 bg-transparent px-4 py-2.5 text-sm dark:bg-gray-900 dark:text-white">
                                <option value="">Select issue type...</option>
                                @foreach ($issueTypes as $issueType)
                                    @if ($issueType->is_need_approval)
                                        <option value="{{ $issueType->id }}">{{ $issueType->name }}</option>
                                    @endif
                                @endforeach
                            </select>
                        </div>

                        <!-- Item Selection -->
                        <div>
                            <label class="mb-2 block text-sm font-medium text-gray-700 dark:text-gray-300">
                                Select Item <span class="text-red-500">*</span>
                            </label>
                            <select name="item_id" required
                                class="w-full rounded-lg border border-gray-300 bg-transparent px-4 py-2.5 text-sm dark:bg-gray-900 dark:text-white">
                                <option value="">Select item...</option>
                                @foreach ($maintenanceRequest->items as $requestItem)
                                    @if ($requestItem->issueType && $requestItem->issueType->name === 'Other')
                                        <option value="{{ $requestItem->item_id }}">
                                            {{ $requestItem->item->name }} (Current: Other)
                                        </option>
                                    @endif
                                @endforeach
                            </select>
                        </div>

                        <!-- Notes -->
                        <div>
                            <label class="mb-2 block text-sm font-medium text-gray-700 dark:text-gray-300">
                                Additional Notes
                            </label>
                            <textarea name="notes" x-model="notes" rows="3"
                                class="w-full rounded-lg border border-gray-300 bg-transparent px-4 py-2.5 text-sm dark:bg-gray-900 dark:text-white"
                                placeholder="Explain why this issue type needs approval..."></textarea>
                        </div>

                        <div class="flex justify-end gap-3">
                            <button type="button" @click="showRequestApprovalModal = false"
                                class="px-4 py-2 text-sm font-medium text-gray-700 bg-white border rounded-lg">
                                Cancel
                            </button>
                            <button type="submit"
                                class="px-4 py-2 text-sm font-medium text-white bg-purple-600 rounded-lg hover:bg-purple-700"
                                :disabled="!selectedIssueType">
                                Submit for Approval
                            </button>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </div>
@endif

{{-- ICT DIRECTOR REVIEW BUTTON --}}
@if (auth()->user()->can('maintenance_requests.assign') &&
        $maintenanceRequest->status === 'pending_approval_review' &&
        $maintenanceRequest->approvalRequest)

    <div class="mt-4 rounded-lg border border-blue-200 bg-blue-50 p-5 dark:border-blue-800 dark:bg-blue-900/20">

        <div class="flex items-center mb-4">
            <i class="bi bi-clipboard-check text-blue-600 me-2 text-lg"></i>
            <h3 class="text-md font-semibold text-blue-800 dark:text-blue-300">
                Approval Review Required
            </h3>
        </div>

        <div class="bg-white dark:bg-gray-900 rounded-lg p-4 shadow-sm mb-4">
            <div class="space-y-2 text-sm">

                <p>
                    <span class="font-medium">Requested By:</span>
                    {{ $maintenanceRequest->approvalRequest->technician->full_name }}
                </p>

                <p>
                    <span class="font-medium">Proposed Issue Type:</span>
                    {{ $maintenanceRequest->approvalRequest->issueType->name }}
                </p>

                <p>
                    <span class="font-medium">Item:</span>
                    {{ $maintenanceRequest->approvalRequest->item->name }}
                </p>

                @if ($maintenanceRequest->approvalRequest->notes)
                    <p class="mt-2">
                        <span class="font-medium">Technician Notes:</span><br>
                        {{ $maintenanceRequest->approvalRequest->notes }}
                    </p>
                @endif

            </div>
        </div>

        <div class="flex justify-end gap-3">

            <form action="{{ route('maintenance-requests.forward-to-chairman', $maintenanceRequest) }}" method="POST">
                @csrf
                <button type="submit"
                    class="px-4 py-2 text-sm font-medium text-white bg-green-600 rounded-lg hover:bg-green-700">
                    <i class="bi bi-arrow-up-circle me-1"></i>
                    Forward to Chairman
                </button>
            </form>

            <form action="{{ route('maintenance-requests.reject-approval-request', $maintenanceRequest) }}"
                method="POST">
                @csrf
                <button type="submit"
                    class="px-4 py-2 text-sm font-medium text-white bg-red-600 rounded-lg hover:bg-red-700">
                    <i class="bi bi-x-circle me-1"></i>
                    Reject Request
                </button>
            </form>

        </div>
    </div>
@endif
