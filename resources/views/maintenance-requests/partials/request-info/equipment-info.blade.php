<div>
    <h4 class="mb-4 text-sm font-semibold text-gray-800 dark:text-white/90">
        <i class="bi bi-pc-display me-2"></i>Equipment Information
    </h4>

    <div class="space-y-4">
        <div>
            <div class="text-xs text-gray-500 dark:text-gray-400">Equipment</div>
            <div class="mt-1 font-medium text-gray-800 dark:text-white/90">
                {{ $maintenanceRequest->item?->name ?? 'Not specified' }}
            </div>
        </div>

        <div>
            <div class="text-xs text-gray-500 dark:text-gray-400">Equipment Type</div>
            <div class="mt-1">
                <span
                    class="inline-flex items-center rounded-lg bg-blue-50 px-3 py-1.5 text-sm font-medium text-blue-700 dark:bg-blue-900/30 dark:text-blue-300">
                    <i
                        class="bi bi-{{ $maintenanceRequest->item?->type === 'computer' ? 'pc-display' : 'box' }} me-2"></i>
                    {{ $maintenanceRequest->item?->getTypeText() ?? 'N/A' }}
                </span>
            </div>
        </div>

        <div>
            <div class="text-xs text-gray-500 dark:text-gray-400">Equipment Status</div>
            <div class="mt-1">
                <span
                    class="rounded-full px-2 py-1 text-xs font-medium {{ $maintenanceRequest->item?->getStatusBadgeClass() ?? 'bg-gray-100 text-gray-800' }}">
                    {{ $maintenanceRequest->item?->getStatusText() ?? 'Unknown' }}
                </span>
            </div>
        </div>
    </div>
</div>
