@props(['recentRequests' => []])

@php
    $getStatusClasses = function ($status) {
        $baseClasses = 'rounded-full px-2 py-0.5 text-theme-xs font-medium';

        return match ($status) {
            'completed' => $baseClasses .
                ' bg-success-50 text-success-600 dark:bg-success-500/15 dark:text-success-500',
            'pending' => $baseClasses . ' bg-warning-50 text-warning-600 dark:bg-warning-500/15 dark:text-orange-400',
            'in_progress' => $baseClasses . ' bg-blue-50 text-blue-600 dark:bg-blue-500/15 dark:text-blue-400',
            'rejected' => $baseClasses . ' bg-error-50 text-error-600 dark:bg-error-500/15 dark:text-error-500',
            'assigned' => $baseClasses . ' bg-indigo-50 text-indigo-600 dark:bg-indigo-500/15 dark:text-indigo-400',
            'waiting_approval' => $baseClasses .
                ' bg-purple-50 text-purple-600 dark:bg-purple-500/15 dark:text-purple-400',
            'approved' => $baseClasses . ' bg-green-50 text-green-600 dark:bg-green-500/15 dark:text-green-400',
            default => $baseClasses . ' bg-gray-50 text-gray-600 dark:bg-gray-500/15 dark:text-gray-400',
        };
    };

    $getStatusText = function ($status) {
        return match ($status) {
            'completed' => 'Completed',
            'pending' => 'Pending',
            'in_progress' => 'In Progress',
            'rejected' => 'Rejected',
            'assigned' => 'Assigned',
            'waiting_approval' => 'Waiting Approval',
            'approved' => 'Approved',
            default => ucfirst(str_replace('_', ' ', $status)),
        };
    };
@endphp

<div
    class="overflow-hidden rounded-2xl border border-gray-200 bg-white px-4 pb-3 pt-4 dark:border-gray-800 dark:bg-white/[0.03] sm:px-6">
    <div class="flex flex-col gap-2 mb-4 sm:flex-row sm:items-center sm:justify-between">
        <div>
            <h3 class="text-lg font-semibold text-gray-800 dark:text-white/90">Recent Maintenance Requests</h3>
            <p class="mt-1 text-gray-500 text-theme-sm dark:text-gray-400">Latest maintenance requests</p>
        </div>

        <div class="flex items-center gap-3">
            <!-- Simple Filter Button -->
            <div class="relative">
                <button id="filterButton"
                    class="inline-flex items-center gap-2 rounded-lg border border-gray-300 bg-white px-4 py-2.5 text-theme-sm font-medium text-gray-700 shadow-theme-xs hover:bg-gray-50 dark:border-gray-700 dark:bg-gray-800 dark:text-gray-400">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M3 4a1 1 0 011-1h16a1 1 0 011 1v2.586a1 1 0 01-.293.707l-6.414 6.414a1 1 0 00-.293.707V17l-4 4v-6.586a1 1 0 00-.293-.707L3.293 7.293A1 1 0 013 6.586V4z" />
                    </svg>
                    Filter
                    <span id="filterBadge"
                        class="hidden ml-1 px-1.5 py-0.5 text-xs bg-blue-500 text-white rounded-full"></span>
                </button>

                <!-- Simple Filter Dropdown -->
                <div id="filterMenu"
                    class="hidden absolute right-0 mt-2 w-72 bg-white rounded-lg shadow-lg border border-gray-200 dark:bg-gray-800 dark:border-gray-700 z-50">
                    <div class="p-4 border-b border-gray-200 dark:border-gray-700">
                        <div class="flex justify-between items-center">
                            <h4 class="font-semibold text-gray-900 dark:text-white">Filter Requests</h4>
                            <button id="clearFilters" class="text-sm text-blue-600 hover:text-blue-700">Clear
                                All</button>
                        </div>
                    </div>

                    <div class="p-4 max-h-96 overflow-y-auto">
                        <!-- Search -->
                        <div class="mb-4">
                            <input type="text" id="searchFilter" placeholder="Search by ticket or user..."
                                class="w-full px-3 py-2 text-sm border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 dark:bg-gray-700 dark:border-gray-600">
                        </div>

                        <!-- Status -->
                        <div class="mb-4">
                            <label
                                class="block text-sm font-medium mb-2 text-gray-700 dark:text-gray-300">Status</label>
                            <div class="space-y-2">
                                @foreach (['pending', 'in_progress', 'completed', 'assigned', 'waiting_approval', 'approved', 'rejected'] as $status)
                                    <label class="flex items-center">
                                        <input type="checkbox" value="{{ $status }}"
                                            class="status-filter rounded border-gray-300 text-blue-600 focus:ring-blue-500">
                                        <span
                                            class="ml-2 text-sm text-gray-700 dark:text-gray-300">{{ ucfirst(str_replace('_', ' ', $status)) }}</span>
                                    </label>
                                @endforeach
                            </div>
                        </div>

                        <!-- Priority -->
                        <div class="mb-4">
                            <label
                                class="block text-sm font-medium mb-2 text-gray-700 dark:text-gray-300">Priority</label>
                            <div class="space-y-2">
                                @foreach (['emergency', 'high', 'medium', 'low'] as $priority)
                                    <label class="flex items-center">
                                        <input type="checkbox" value="{{ $priority }}"
                                            class="priority-filter rounded border-gray-300 text-blue-600 focus:ring-blue-500">
                                        <span
                                            class="ml-2 text-sm text-gray-700 dark:text-gray-300">{{ ucfirst($priority) }}</span>
                                    </label>
                                @endforeach
                            </div>
                        </div>

                        <!-- Date Range -->
                        <div>
                            <label class="block text-sm font-medium mb-2 text-gray-700 dark:text-gray-300">Date
                                Range</label>
                            <select id="dateRange"
                                class="w-full px-3 py-2 text-sm border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 dark:bg-gray-700 dark:border-gray-600">
                                <option value="all">All time</option>
                                <option value="today">Today</option>
                                <option value="week">Last 7 days</option>
                                <option value="month">Last 30 days</option>
                            </select>
                        </div>
                        <div class="p-4 border-t border-gray-200 dark:border-gray-700 mb-10">
                            <button id="applyFilters"
                                class="w-full px-4 py-2 text-sm font-medium text-white bg-blue-600 rounded-lg hover:bg-blue-700">
                                Apply Filters
                            </button>
                        </div>
                    </div>


                </div>
            </div>

            {{-- <!-- See All Button -->
            <a href="{{ route('maintenance-requests.index') }}"
                class="inline-flex items-center gap-2 rounded-lg border border-gray-300 bg-white px-4 py-2.5 text-theme-sm font-medium text-gray-700 hover:bg-gray-50 dark:border-gray-700 dark:bg-gray-800 dark:text-gray-400">
                See all
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7" />
                </svg>
            </a> --}}
        </div>
    </div>

    @if ($recentRequests->count() > 0)
        <div class="overflow-x-auto">
            <table class="w-full">
                <thead>
                    <tr class="border-t border-gray-100 dark:border-gray-800">
                        <th class="py-3 text-left text-xs font-medium text-gray-500">Ticket #</th>
                        <th class="py-3 text-left text-xs font-medium text-gray-500">Issue Type</th>
                        <th class="py-3 text-left text-xs font-medium text-gray-500">Priority</th>
                        <th class="py-3 text-left text-xs font-medium text-gray-500">Status</th>
                        <th class="py-3 text-left text-xs font-medium text-gray-500">Requested</th>
                    </tr>
                </thead>
                <tbody id="requestsTableBody">
                    @foreach ($recentRequests as $request)
                        <tr class="border-t border-gray-100 dark:border-gray-800 request-row"
                            data-status="{{ $request->status }}" data-priority="{{ $request->priority }}"
                            data-date="{{ $request->requested_at ? $request->requested_at->format('Y-m-d') : '' }}"
                            data-ticket="{{ strtolower($request->ticket_number) }}"
                            data-user="{{ strtolower($request->user ? $request->user->full_name : '') }}">
                            <td class="py-3">
                                <div>
                                    <p class="font-medium text-gray-800 dark:text-white/90">
                                        <a href="{{ route('maintenance-requests.show', $request) }}"
                                            class="hover:text-blue-600">
                                            {{ $request->ticket_number }}
                                        </a>
                                    </p>
                                    <span class="text-xs text-gray-500">
                                        {{ $request->user ? $request->user->full_name : 'N/A' }}
                                    </span>
                                </div>
                            </td>
                            <td class="py-3 text-sm text-gray-500">{{ $request->getIssueTypeText() }}</td>
                            <td class="py-3">
                                <span
                                    class="rounded-full px-2 py-0.5 text-xs font-medium {{ $request->getPriorityBadgeClass() }}">
                                    {{ ucfirst($request->priority) }}
                                </span>
                            </td>
                            <td class="py-3">
                                <span class="{{ $getStatusClasses($request->status) }}">
                                    {{ $getStatusText($request->status) }}
                                </span>
                            </td>
                            <td class="py-3 text-sm text-gray-500">
                                {{ $request->requested_at ? $request->requested_at->format('M d, Y') : 'N/A' }}
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>

        <div class="mt-4 text-center text-sm text-gray-500">
            Showing <span id="visibleCount">{{ $recentRequests->count() }}</span> of <span
                id="totalCount">{{ $recentRequests->count() }}</span> requests
        </div>
    @else
        <div class="py-12 text-center">
            <svg class="w-16 h-16 mx-auto text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1"
                    d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
            </svg>
            <h3 class="mt-4 text-lg font-medium text-gray-900">No requests yet</h3>
            <p class="mt-1 text-gray-500">Get started by creating a new maintenance request.</p>
            <div class="mt-6">
                <a href="{{ route('maintenance-requests.create') }}"
                    class="inline-flex items-center px-4 py-2 text-sm font-medium text-white bg-blue-600 rounded-md hover:bg-blue-700">
                    New Request
                </a>
            </div>
        </div>
    @endif
</div>

@push('scripts')
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const filterButton = document.getElementById('filterButton');
            const filterMenu = document.getElementById('filterMenu');
            const applyBtn = document.getElementById('applyFilters');
            const clearBtn = document.getElementById('clearFilters');
            const filterBadge = document.getElementById('filterBadge');
            const searchInput = document.getElementById('searchFilter');
            const dateRange = document.getElementById('dateRange');
            const statusFilters = document.querySelectorAll('.status-filter');
            const priorityFilters = document.querySelectorAll('.priority-filter');
            const rows = document.querySelectorAll('.request-row');
            const visibleCountSpan = document.getElementById('visibleCount');
            const totalCountSpan = document.getElementById('totalCount');

            if (totalCountSpan) totalCountSpan.textContent = rows.length;

            // Toggle dropdown
            filterButton.addEventListener('click', (e) => {
                e.stopPropagation();
                filterMenu.classList.toggle('hidden');
            });

            document.addEventListener('click', (e) => {
                if (!filterButton.contains(e.target) && !filterMenu.contains(e.target)) {
                    filterMenu.classList.add('hidden');
                }
            });

            function filterRows() {
                const searchTerm = searchInput ? searchInput.value.toLowerCase() : '';
                const selectedStatuses = Array.from(statusFilters).filter(cb => cb.checked).map(cb => cb.value);
                const selectedPriorities = Array.from(priorityFilters).filter(cb => cb.checked).map(cb => cb.value);
                const dateRangeValue = dateRange ? dateRange.value : 'all';

                let visibleCount = 0;
                const today = new Date();
                today.setHours(0, 0, 0, 0);

                rows.forEach(row => {
                    let show = true;

                    // Search filter
                    if (searchTerm) {
                        const ticket = row.dataset.ticket || '';
                        const user = row.dataset.user || '';
                        if (!ticket.includes(searchTerm) && !user.includes(searchTerm)) {
                            show = false;
                        }
                    }

                    // Status filter
                    if (show && selectedStatuses.length > 0) {
                        if (!selectedStatuses.includes(row.dataset.status)) {
                            show = false;
                        }
                    }

                    // Priority filter
                    if (show && selectedPriorities.length > 0) {
                        if (!selectedPriorities.includes(row.dataset.priority)) {
                            show = false;
                        }
                    }

                    // Date filter
                    if (show && dateRangeValue !== 'all') {
                        const rowDate = row.dataset.date;
                        if (rowDate) {
                            const requestDate = new Date(rowDate);
                            switch (dateRangeValue) {
                                case 'today':
                                    if (rowDate !== today.toISOString().split('T')[0]) show = false;
                                    break;
                                case 'week':
                                    const weekAgo = new Date(today);
                                    weekAgo.setDate(today.getDate() - 7);
                                    if (requestDate < weekAgo) show = false;
                                    break;
                                case 'month':
                                    const monthAgo = new Date(today);
                                    monthAgo.setMonth(today.getMonth() - 1);
                                    if (requestDate < monthAgo) show = false;
                                    break;
                            }
                        }
                    }

                    row.style.display = show ? '' : 'none';
                    if (show) visibleCount++;
                });

                if (visibleCountSpan) visibleCountSpan.textContent = visibleCount;

                // Update badge
                const hasFilters = selectedStatuses.length > 0 || selectedPriorities.length > 0 ||
                    dateRangeValue !== 'all' || searchTerm;
                if (filterBadge) {
                    if (hasFilters) {
                        const count = selectedStatuses.length + selectedPriorities.length + (dateRangeValue !==
                            'all' ? 1 : 0) + (searchTerm ? 1 : 0);
                        filterBadge.textContent = count;
                        filterBadge.classList.remove('hidden');
                    } else {
                        filterBadge.classList.add('hidden');
                    }
                }

                filterMenu.classList.add('hidden');
            }

            applyBtn.addEventListener('click', filterRows);

            clearBtn.addEventListener('click', () => {
                statusFilters.forEach(cb => cb.checked = false);
                priorityFilters.forEach(cb => cb.checked = false);
                if (dateRange) dateRange.value = 'all';
                if (searchInput) searchInput.value = '';
                filterRows();
            });

            if (searchInput) {
                searchInput.addEventListener('keypress', (e) => {
                    if (e.key === 'Enter') filterRows();
                });
            }
        });
    </script>
@endpush
