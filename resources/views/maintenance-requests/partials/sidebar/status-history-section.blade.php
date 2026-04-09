<div class="bg-white dark:bg-gray-800 rounded-lg shadow-md overflow-hidden">
    <div class="p-4 border-b border-gray-200 dark:border-gray-700">
        <div class="flex items-center justify-between">
            <h3 class="text-lg font-semibold text-gray-900 dark:text-white flex items-center">
                <svg class="w-5 h-5 mr-2 text-blue-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                        d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                </svg>
                Status History
            </h3>
            <button onclick="toggleStatusHistory()"
                class="text-gray-500 hover:text-gray-700 dark:text-gray-400 dark:hover:text-gray-200">
                <svg id="status-history-icon" class="w-5 h-5 transform transition-transform duration-200" fill="none"
                    stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path>
                </svg>
            </button>
        </div>
    </div>
    <div id="status-history-content" class="p-4 max-h-96 overflow-y-auto">
        @if ($maintenanceRequest->statusHistories && $maintenanceRequest->statusHistories->count() > 0)
            <div class="space-y-4">
                @foreach ($maintenanceRequest->statusHistories as $history)
                    <div class="relative pl-8 pb-4 border-l-2 border-gray-200 dark:border-gray-700">
                        <div
                            class="absolute left-0 top-0 w-4 h-4 rounded-full bg-blue-500 border-2 border-white dark:border-gray-800 -translate-x-1/2">
                        </div>
                        <div class="bg-gray-50 dark:bg-gray-700 rounded-lg p-3">
                            <div class="flex items-center justify-between mb-2">
                                <span class="text-sm font-medium text-gray-900 dark:text-white">
                                    @if ($history->from_status)
                                        <span
                                            class="inline-flex items-center px-2 py-1 rounded text-xs font-medium bg-gray-200 dark:bg-gray-600 text-gray-800 dark:text-gray-200">
                                            {{ ucfirst(str_replace('_', ' ', $history->from_status)) }}
                                        </span>
                                        <svg class="w-4 h-4 inline mx-1 text-gray-400" fill="none"
                                            stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                d="M14 5l7 7m0 0l-7 7m7-7H3"></path>
                                        </svg>
                                    @endif
                                    <span
                                        class="inline-flex items-center px-2 py-1 rounded text-xs font-medium bg-blue-100 dark:bg-blue-900 text-blue-800 dark:text-blue-200">
                                        {{ ucfirst(str_replace('_', ' ', $history->to_status)) }}
                                    </span>
                                </span>
                            </div>
                            <div class="flex items-center text-xs text-gray-500 dark:text-gray-400">
                                <svg class="w-3 h-3 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"></path>
                                </svg>
                                {{ $history->changedBy->full_name ?? 'System' }}
                            </div>
                            <div class="flex items-center text-xs text-gray-500 dark:text-gray-400 mt-1">
                                <svg class="w-3 h-3 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                                </svg>
                                {{ $history->created_at->format('M d, Y h:i A') }}
                            </div>
                        </div>
                    </div>
                @endforeach
            </div>
        @else
            <div class="text-center py-8 text-gray-500 dark:text-gray-400">
                <svg class="w-12 h-12 mx-auto mb-4 text-gray-300 dark:text-gray-600" fill="none"
                    stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                        d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                </svg>
                <p>No status history available</p>
            </div>
        @endif
    </div>
</div>

<script>
    function toggleStatusHistory() {
        const content = document.getElementById('status-history-content');
        const icon = document.getElementById('status-history-icon');

        if (content.classList.contains('hidden')) {
            content.classList.remove('hidden');
            icon.classList.remove('rotate-180');
        } else {
            content.classList.add('hidden');
            icon.classList.add('rotate-180');
        }
    }
</script>
