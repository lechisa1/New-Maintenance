<div>
    <h4 class="mb-4 text-sm font-semibold text-gray-800 dark:text-white/90">
        <i class="bi bi-info-circle me-2"></i>Request Details
    </h4>

    <div class="space-y-4">
        <div>
            <div class="text-xs text-gray-500 dark:text-gray-400">Issue Type</div>
            <div class="mt-1 font-medium text-gray-800 dark:text-white/90">
                {{ $maintenanceRequest->getIssueTypeText() }}
            </div>
        </div>

        <div>
            <div class="text-xs text-gray-500 dark:text-gray-400">Submitted By</div>
            <div class="mt-1 font-medium text-gray-800 dark:text-white/90">
                {{ $maintenanceRequest->user?->full_name ?? 'Unknown' }}
            </div>
            <div class="text-xs text-gray-500 dark:text-gray-400">
                {{ $maintenanceRequest->user?->email ?? '' }}
            </div>
        </div>

        <div>
            <div class="text-xs text-gray-500 dark:text-gray-400">Submitted On</div>
            <div class="mt-1 font-medium text-gray-800 dark:text-white/90">
                {{ $maintenanceRequest->getRequestedDate() }} at
                {{ $maintenanceRequest->getRequestedTime() }}
            </div>
        </div>

        @if ($maintenanceRequest->assigned_to)
            <div>
                <div class="text-xs text-gray-500 dark:text-gray-400">Assigned To</div>
                <div class="mt-1 font-medium text-gray-800 dark:text-white/90">
                    {{ $maintenanceRequest->assignedTechnician?->full_name ?? 'Unknown' }}
                </div>
                <div class="text-xs text-gray-500 dark:text-gray-400">
                    {{ $maintenanceRequest->assigned_at?->format('M d, Y h:i A') ?? '' }}
                </div>
            </div>
        @endif
    </div>
</div>
