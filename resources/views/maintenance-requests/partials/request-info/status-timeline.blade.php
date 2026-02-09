<div class="mt-6">
    <h4 class="mb-4 text-sm font-semibold text-gray-800 dark:text-white/90">
        <i class="bi bi-clock-history me-2"></i>Status Timeline
    </h4>

    <div class="space-y-4">
        <div class="flex items-center">
            <div class="mr-4 flex h-8 w-8 items-center justify-center rounded-full bg-green-100 dark:bg-green-900">
                <i class="bi bi-plus-lg text-green-600 dark:text-green-300"></i>
            </div>
            <div class="flex-1">
                <div class="text-sm font-medium text-gray-800 dark:text-white/90">Request Submitted</div>
                <div class="text-xs text-gray-500 dark:text-gray-400">
                    {{ $maintenanceRequest->requested_at->format('M d, Y h:i A') }}
                </div>
            </div>
        </div>

        @if ($maintenanceRequest->assigned_at)
            <div class="flex items-center">
                <div class="mr-4 flex h-8 w-8 items-center justify-center rounded-full bg-blue-100 dark:bg-blue-900">
                    <i class="bi bi-person-check text-blue-600 dark:text-blue-300"></i>
                </div>
                <div class="flex-1">
                    <div class="text-sm font-medium text-gray-800 dark:text-white/90">Assigned to Technician</div>
                    <div class="text-xs text-gray-500 dark:text-gray-400">
                        {{ $maintenanceRequest->assigned_at->format('M d, Y h:i A') }}
                        @if ($maintenanceRequest->getResponseTime())
                            {{-- <span class="ml-2 text-green-600 dark:text-green-400">
                                (Response: {{ $maintenanceRequest->getResponseTime() }} hours)
                            </span> --}}
                        @endif
                    </div>
                </div>
            </div>
        @endif

        @if ($maintenanceRequest->started_at)
            <div class="flex items-center">
                <div
                    class="mr-4 flex h-8 w-8 items-center justify-center rounded-full bg-indigo-100 dark:bg-indigo-900">
                    <i class="bi bi-tools text-indigo-600 dark:text-indigo-300"></i>
                </div>
                <div class="flex-1">
                    <div class="text-sm font-medium text-gray-800 dark:text-white/90">Work Started</div>
                    <div class="text-xs text-gray-500 dark:text-gray-400">
                        {{ $maintenanceRequest->started_at->format('M d, Y h:i A') }}
                    </div>
                </div>
            </div>
        @endif

        @if ($maintenanceRequest->completed_at)
            <div class="flex items-center">
                <div class="mr-4 flex h-8 w-8 items-center justify-center rounded-full bg-green-100 dark:bg-green-900">
                    <i class="bi bi-check-circle text-green-600 dark:text-green-300"></i>
                </div>
                <div class="flex-1">
                    <div class="text-sm font-medium text-gray-800 dark:text-white/90">Completed</div>
                    <div class="text-xs text-gray-500 dark:text-gray-400">
                        {{ $maintenanceRequest->completed_at->format('M d, Y h:i A') }}
                        @if ($maintenanceRequest->getResolutionTime())
                            {{-- <span class="ml-2 text-green-600 dark:text-green-400">
                                (Resolution: {{ $maintenanceRequest->getResolutionTime() }} hours)
                            </span> --}}
                        @endif
                    </div>
                </div>
            </div>
        @endif

        @if ($maintenanceRequest->rejected_at)
            <div class="flex items-center">
                <div class="mr-4 flex h-8 w-8 items-center justify-center rounded-full bg-red-100 dark:bg-red-900">
                    <i class="bi bi-x-circle text-red-600 dark:text-red-300"></i>
                </div>
                <div class="flex-1">
                    <div class="text-sm font-medium text-gray-800 dark:text-white/90">Rejected</div>
                    <div class="text-xs text-gray-500 dark:text-gray-400">
                        {{ $maintenanceRequest->rejected_at->format('M d, Y h:i A') }}
                    </div>
                </div>
            </div>
        @endif
    </div>
</div>
