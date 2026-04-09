@php
    $user = auth()->user();
    $requester = $maintenanceRequest->user ?? null;

    // Get the appropriate approval request based on status
    $pendingApproval = $maintenanceRequest->approvalRequest; // This is only for pending
    $forwardedApproval = $maintenanceRequest->forwardedApprovalRequest; // This is for forwarded
    $technicianApproval = null;

    // Determine which approval request to use
    $activeApproval = null;

    if ($maintenanceRequest->status === 'waiting_approval' && $forwardedApproval) {
        $activeApproval = $forwardedApproval;
    } elseif ($pendingApproval) {
        $activeApproval = $pendingApproval;
    }

    // For technician notification - check all approval requests including processed ones
    $latestApproval = $maintenanceRequest->allApprovalRequests()->latest()->first();

    // Check if this is a chairman viewing a request with forwarded approval request
    $showChairmanApproval = false;
    $isForwardedForChairman = false;

    // Chairman should see approval section when:
    // 1. The request status is waiting_approval AND
    // 2. The forwarded approval request exists AND
    // 3. The chairman is the correct chairman for this requester's division/cluster
if ($maintenanceRequest->status === 'waiting_approval' && $forwardedApproval) {
    $approvalRequest = $forwardedApproval;

    if ($approvalRequest->status === 'forwarded' && $requester) {
        // Division Chairman check
        if ($user->isDivisionChairman() && $requester->division_id === $user->division_id) {
            $showChairmanApproval = true;
            $isForwardedForChairman = true;
        }

        // Cluster Chairman check
        if ($user->isClusterChairman()) {
            $requesterClusterId = optional($requester->division)->cluster_id;
            if ($requesterClusterId === $user->cluster_id) {
                $showChairmanApproval = true;
                $isForwardedForChairman = true;
            }
            // Also check if requester has no division but same cluster
            if (!$requester->division_id && $requester->cluster_id === $user->cluster_id) {
                $showChairmanApproval = true;
                $isForwardedForChairman = true;
            }
        }
    }
}

// Also show for technicians when their request has been approved/rejected by chairman
$showTechnicianNotification = false;
$isTechnicianAssigned = false;

// Check if user is an assigned technician to this request
if ($maintenanceRequest->relationLoaded('assignedTechnicians')) {
    $isTechnicianAssigned = $maintenanceRequest->assignedTechnicians->where('user_id', $user->id)->isNotEmpty();
} else {
    $isTechnicianAssigned = $maintenanceRequest->assignedTechnicians()->where('user_id', $user->id)->exists();
}

// Show notification if user is the assigned technician and the latest approval has been processed
if ($isTechnicianAssigned && $latestApproval) {
    if (in_array($latestApproval->status, ['approved', 'rejected'])) {
            $showTechnicianNotification = true;
            $technicianApproval = $latestApproval;
        }
    }
@endphp

{{-- CHAIRMAN APPROVAL SECTION --}}
@if ($showChairmanApproval && $isForwardedForChairman && $forwardedApproval)
    <div class="rounded-lg border border-purple-200 bg-purple-50 p-5 dark:border-purple-800 dark:bg-purple-900/20">
        <div class="flex items-center mb-4">
            <i class="bi bi-shield-check text-purple-600 me-2 text-lg"></i>
            <h3 class="text-md font-semibold text-purple-800 dark:text-purple-300">
                Chairman Approval Required
            </h3>
        </div>

        <div class="bg-white dark:bg-gray-900 rounded-lg p-4 shadow-sm mb-4">
            <div class="space-y-2 text-sm">
                <p>
                    <span class="font-medium">Requested By:</span>
                    {{ $forwardedApproval->technician->full_name ?? 'N/A' }}
                </p>

                <p>
                    <span class="font-medium">Current Issue Type:</span>
                    <span class="text-red-600 line-through">
                        @foreach ($maintenanceRequest->items as $item)
                            @if ($item->item_id === $forwardedApproval->item_id)
                                {{ $item->issueType->name ?? 'Other' }}
                            @endif
                        @endforeach
                    </span>
                </p>

                <p>
                    <span class="font-medium">Proposed Issue Type:</span>
                    <span class="text-green-600 font-semibold">
                        {{ $forwardedApproval->issueType->name ?? 'N/A' }}
                    </span>
                </p>

                <p>
                    <span class="font-medium">Item:</span>
                    {{ $forwardedApproval->item->name ?? 'N/A' }}
                </p>

                @if ($forwardedApproval->notes)
                    <p class="mt-2">
                        <span class="font-medium">Technician Notes:</span><br>
                        <span class="text-gray-600 dark:text-gray-400">{{ $forwardedApproval->notes }}</span>
                    </p>
                @endif

                <p class="mt-2 text-xs text-gray-500">
                    <span class="font-medium">Forwarded At:</span>
                    {{ $forwardedApproval->forwarded_at ? $forwardedApproval->forwarded_at->format('M d, Y h:i A') : 'N/A' }}
                </p>
            </div>
        </div>

        {{-- Approval Form with Attachments --}}
        <form action="{{ route('maintenance-requests.chairman-approve', $maintenanceRequest) }}" method="POST"
            enctype="multipart/form-data">
            @csrf

            <div class="mb-4">
                <label class="mb-2 block text-sm font-medium text-gray-700 dark:text-gray-300">
                    Approval Notes (Optional)
                </label>
                <textarea name="approval_notes" rows="3"
                    class="w-full rounded-lg border border-gray-300 bg-transparent px-4 py-2.5 text-sm dark:border-gray-700 dark:bg-gray-900 dark:text-white"
                    placeholder="Add any notes about this approval..."></textarea>
            </div>

            <div class="mb-4">
                <label class="mb-2 block text-sm font-medium text-gray-700 dark:text-gray-300">
                    Attachments (Optional)
                </label>
                <input type="file" name="attachments[]" multiple
                    accept=".pdf,.jpg,.jpeg,.png,.gif,.doc,.docx,.xls,.xlsx"
                    class="w-full rounded-lg border border-gray-300 bg-transparent px-4 py-2.5 text-sm dark:border-gray-700 dark:bg-gray-900 dark:text-white">
                <p class="mt-1 text-xs text-gray-500">Allowed: PDF, Images, Word, Excel (Max 5MB per file)</p>
            </div>

            <div class="flex justify-end gap-3">
                <button type="submit"
                    class="px-4 py-2 text-sm font-medium text-white bg-green-600 rounded-lg hover:bg-green-700 shadow-sm">
                    <i class="bi bi-check-circle me-1"></i>
                    Approve Issue Type
                </button>

                <button type="button" @click="$refs.chairmanRejectModal.classList.remove('hidden')"
                    class="px-4 py-2 text-sm font-medium text-white bg-red-600 rounded-lg hover:bg-red-700 shadow-sm">
                    <i class="bi bi-x-circle me-1"></i>
                    Reject
                </button>
            </div>
        </form>

        {{-- Reject Modal with Attachments --}}
        <div x-ref="chairmanRejectModal"
            class="hidden fixed inset-0 z-50 flex items-center justify-center bg-black bg-opacity-50">
            <div class="bg-white dark:bg-gray-800 rounded-lg p-6 w-full max-w-md mx-4">
                <div class="flex justify-between items-center mb-4">
                    <h3 class="text-lg font-semibold text-gray-800 dark:text-white/90">
                        Reject Issue Type Request
                    </h3>
                    <button @click="$refs.chairmanRejectModal.classList.add('hidden')"
                        class="text-gray-500 hover:text-gray-700 dark:text-gray-400">
                        <i class="bi bi-x-lg"></i>
                    </button>
                </div>

                <form action="{{ route('maintenance-requests.chairman-reject', $maintenanceRequest) }}" method="POST"
                    enctype="multipart/form-data">
                    @csrf
                    <div class="space-y-4">
                        <div>
                            <label class="mb-2 block text-sm font-medium text-gray-700 dark:text-gray-300">
                                Rejection Reason <span class="text-red-500">*</span>
                            </label>
                            <textarea name="rejection_reason" rows="4" required
                                class="w-full rounded-lg border border-gray-300 bg-transparent px-4 py-2.5 text-sm dark:border-gray-700 dark:bg-gray-900 dark:text-white"
                                placeholder="Please explain why this issue type request is being rejected..."></textarea>
                        </div>

                        <div>
                            <label class="mb-2 block text-sm font-medium text-gray-700 dark:text-gray-300">
                                Attachments (Optional)
                            </label>
                            <input type="file" name="attachments[]" multiple
                                accept=".pdf,.jpg,.jpeg,.png,.gif,.doc,.docx,.xls,.xlsx"
                                class="w-full rounded-lg border border-gray-300 bg-transparent px-4 py-2.5 text-sm dark:border-gray-700 dark:bg-gray-900 dark:text-white">
                            <p class="mt-1 text-xs text-gray-500">Allowed: PDF, Images, Word, Excel (Max 5MB per file)
                            </p>
                        </div>

                        <div class="flex justify-end gap-3">
                            <button type="button" @click="$refs.chairmanRejectModal.classList.add('hidden')"
                                class="px-4 py-2 text-sm font-medium text-gray-700 bg-white border border-gray-300 rounded-lg hover:bg-gray-50 dark:bg-gray-800 dark:border-gray-700 dark:text-gray-400">
                                Cancel
                            </button>
                            <button type="submit"
                                class="px-4 py-2 text-sm font-medium text-white bg-red-600 rounded-lg hover:bg-red-700">
                                Reject Request
                            </button>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </div>
@endif
{{-- TECHNICIAN NOTIFICATION SECTION --}}
@if ($showTechnicianNotification && $technicianApproval)
    <div
        class="rounded-lg border 
        @if ($technicianApproval->status === 'approved') border-green-200 bg-green-50 dark:border-green-800 dark:bg-green-900/20
        @else
            border-red-200 bg-red-50 dark:border-red-800 dark:bg-red-900/20 @endif
        p-5">
        <div class="flex items-center mb-3">
            @if ($technicianApproval->status === 'approved')
                <i class="bi bi-check-circle text-green-600 me-2 text-lg"></i>
                <h3 class="text-md font-semibold text-green-800 dark:text-green-300">
                    Issue Type Approved by Chairman
                </h3>
            @else
                <i class="bi bi-x-circle text-red-600 me-2 text-lg"></i>
                <h3 class="text-md font-semibold text-red-800 dark:text-red-300">
                    Issue Type Request Rejected
                </h3>
            @endif
        </div>

        <div class="bg-white dark:bg-gray-900 rounded-lg p-4 shadow-sm">
            <div class="space-y-2 text-sm">
                <p>
                    <span class="font-medium">
                        @if ($technicianApproval->status === 'approved')
                            Approved
                        @else
                            Requested
                        @endif Issue Type:
                    </span>
                    <span
                        class="@if ($technicianApproval->status === 'approved') text-green-600 font-semibold @else text-red-600 @endif">
                        {{ $technicianApproval->issueType->name ?? 'N/A' }}
                    </span>
                </p>

                <p>
                    <span class="font-medium">Item:</span>
                    {{ $technicianApproval->item->name ?? 'N/A' }}
                </p>

                @if ($technicianApproval->status === 'approved')
                    <p class="mt-3 text-green-700 dark:text-green-400">
                        <i class="bi bi-info-circle me-1"></i>
                        The issue type has been updated. Please proceed with the maintenance work.
                    </p>
                @else
                    <p class="mt-2">
                        <span class="font-medium">Rejection Reason:</span><br>
                        <span class="text-gray-600 dark:text-gray-400">
                            {{ $technicianApproval->rejection_reason ?? 'The chairman has reviewed and rejected this request.' }}
                        </span>
                    </p>
                @endif
            </div>
        </div>

        @if ($technicianApproval->status === 'approved')
            <div class="mt-3 text-sm text-green-700 dark:text-green-400">
                <i class="bi bi-arrow-right me-1"></i>
                You can now proceed with the maintenance work. The issue type has been updated.
            </div>
        @endif
    </div>
@endif
