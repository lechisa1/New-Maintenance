@if ($maintenanceRequest->technician_notes || $maintenanceRequest->resolution_notes)
    <div class="mt-6">
        <h4 class="mb-4 text-sm font-semibold text-gray-800 dark:text-white/90">
            <i class="bi bi-journal-text me-2"></i>Assignment Remark
        </h4>

        <div class="space-y-4">
            @if ($maintenanceRequest->technician_notes)
                <div>
                    <div class="text-xs text-gray-500 dark:text-gray-400">Remarks</div>
                    <div
                        class="mt-2 rounded-lg border border-gray-200 bg-blue-50 p-4 dark:border-gray-700 dark:bg-blue-900/20">
                        <p class="whitespace-pre-line text-sm text-gray-800 dark:text-white/90">
                            {{ $maintenanceRequest->technician_notes }}
                        </p>
                    </div>
                </div>
            @endif

            @if ($maintenanceRequest->resolution_notes)
                <div>
                    <div class="text-xs text-gray-500 dark:text-gray-400">Resolution Notes</div>
                    <div
                        class="mt-2 rounded-lg border border-gray-200 bg-green-50 p-4 dark:border-gray-700 dark:bg-green-900/20">
                        <p class="whitespace-pre-line text-sm text-gray-800 dark:text-white/90">
                            {{ $maintenanceRequest->resolution_notes }}
                        </p>
                    </div>
                </div>
            @endif
        </div>
    </div>
@endif
