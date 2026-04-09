@extends('layouts.app')

@section('title', 'My Reports')

@section('content')
    <div class="mb-6 flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
        <div>
            <h2 class="text-2xl font-bold text-gray-800 dark:text-white">My Reports</h2>
            <p class="text-gray-500 dark:text-gray-400 mt-1">Download and view your completed tasks</p>
        </div>
        <div class="flex gap-2">
            <button type="button" onclick="exportReport('csv')"
                class="inline-flex items-center px-4 py-2 bg-green-600 hover:bg-green-700 text-white text-sm font-medium rounded-lg transition-colors">
                <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                        d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                </svg>
                Export CSV
            </button>
            <button type="button" onclick="exportReport('pdf')"
                class="inline-flex items-center px-4 py-2 bg-blue-600 hover:bg-blue-700 text-white text-sm font-medium rounded-lg transition-colors">
                <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                        d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                </svg>
                Download PDF
            </button>
        </div>
    </div>

    <!-- Period Selection -->
    <div class="bg-white dark:bg-gray-800 rounded-lg shadow mb-6 p-5 border border-gray-200 dark:border-gray-700">
        <h3 class="text-lg font-semibold text-gray-800 dark:text-white mb-4">Select Period</h3>
        <form id="reportForm" action="{{ route('reports.my.index') }}" method="GET" class="flex flex-wrap gap-4">
            <div class="flex gap-4">
                <label class="inline-flex items-center">
                    <input type="radio" name="period" value="week" {{ $period === 'week' ? 'checked' : '' }}
                        class="w-4 h-4 text-blue-600 border-gray-300 focus:ring-blue-500" onchange="this.form.submit()">
                    <span class="ml-2 text-sm text-gray-700 dark:text-gray-300">This Week</span>
                </label>
                <label class="inline-flex items-center">
                    <input type="radio" name="period" value="month" {{ $period === 'month' ? 'checked' : '' }}
                        class="w-4 h-4 text-blue-600 border-gray-300 focus:ring-blue-500" onchange="this.form.submit()">
                    <span class="ml-2 text-sm text-gray-700 dark:text-gray-300">This Month</span>
                </label>
                <label class="inline-flex items-center">
                    <input type="radio" name="period" value="custom" {{ $period === 'custom' ? 'checked' : '' }}
                        class="w-4 h-4 text-blue-600 border-gray-300 focus:ring-blue-500" id="customPeriodRadio">
                    <span class="ml-2 text-sm text-gray-700 dark:text-gray-300">Custom Range</span>
                </label>
            </div>

            <div id="customDateRange" class="flex items-center gap-4 w-full"
                style="{{ $period === 'custom' ? '' : 'display: none;' }}">
                <div class="flex-1">
                    <label for="start_date" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Start
                        Date</label>
                    <input type="date" name="start_date" id="start_date"
                        value="{{ request('start_date', $startDate->format('Y-m-d')) }}"
                        class="w-full rounded-lg border border-gray-300 dark:border-gray-600 bg-white dark:bg-gray-700 text-gray-900 dark:text-white px-3 py-2 focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
                </div>
                <div class="flex-1">
                    <label for="end_date" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">End
                        Date</label>
                    <input type="date" name="end_date" id="end_date"
                        value="{{ request('end_date', $endDate->format('Y-m-d')) }}"
                        class="w-full rounded-lg border border-gray-300 dark:border-gray-600 bg-white dark:bg-gray-700 text-gray-900 dark:text-white px-3 py-2 focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
                </div>
                <div class="flex items-end gap-2">
                    <button type="submit"
                        class="px-4 py-2 bg-blue-600 hover:bg-blue-700 text-white text-sm font-medium rounded-lg transition-colors">
                        Apply
                    </button>
                    <button type="button" onclick="clearDates()"
                        class="px-4 py-2 bg-gray-500 hover:bg-gray-600 text-white text-sm font-medium rounded-lg transition-colors">
                        Clear
                    </button>
                </div>
            </div>
        </form>
    </div>

    <!-- Statistics Cards -->
    <div class="grid grid-cols-1 md:grid-cols-4 gap-4 mb-6">
        <div class="bg-white dark:bg-gray-800 rounded-lg shadow p-5 border border-gray-200 dark:border-gray-700">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm text-gray-500 dark:text-gray-400">Tasks Completed</p>
                    <p class="text-3xl font-bold text-gray-800 dark:text-white">{{ $totalCompleted }}</p>
                </div>
                <div class="w-12 h-12 bg-green-100 dark:bg-green-900 rounded-full flex items-center justify-center">
                    <svg class="w-6 h-6 text-green-600 dark:text-green-400" fill="none" stroke="currentColor"
                        viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
                    </svg>
                </div>
            </div>
        </div>

        <div class="bg-white dark:bg-gray-800 rounded-lg shadow p-5 border border-gray-200 dark:border-gray-700">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm text-gray-500 dark:text-gray-400">Avg. Completion Time</p>
                    <p class="text-3xl font-bold text-gray-800 dark:text-white">
                        {{ $avgCompletionTime ? round($avgCompletionTime, 1) . ' hrs' : 'N/A' }}
                    </p>
                </div>
                <div class="w-12 h-12 bg-blue-100 dark:bg-blue-900 rounded-full flex items-center justify-center">
                    <svg class="w-6 h-6 text-blue-600 dark:text-blue-400" fill="none" stroke="currentColor"
                        viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" />
                    </svg>
                </div>
            </div>
        </div>

        <div class="bg-white dark:bg-gray-800 rounded-lg shadow p-5 border border-gray-200 dark:border-gray-700">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm text-gray-500 dark:text-gray-400">Period</p>
                    <p class="text-lg font-bold text-gray-800 dark:text-white capitalize">
                        {{ $period === 'custom' ? 'Custom Range' : $period }}</p>
                    <p class="text-sm text-gray-500 dark:text-gray-400">
                        {{ $startDate->format('M d, Y') }} - {{ $endDate->format('M d, Y') }}
                    </p>
                </div>
                <div class="w-12 h-12 bg-purple-100 dark:bg-purple-900 rounded-full flex items-center justify-center">
                    <svg class="w-6 h-6 text-purple-600 dark:text-purple-400" fill="none" stroke="currentColor"
                        viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z" />
                    </svg>
                </div>
            </div>
        </div>

        <div class="bg-white dark:bg-gray-800 rounded-lg shadow p-5 border border-gray-200 dark:border-gray-700">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm text-gray-500 dark:text-gray-400">Quick Actions</p>
                    <div class="flex gap-2 mt-2">
                        <button onclick="setDateRange('week')"
                            class="px-2 py-1 text-xs bg-gray-100 hover:bg-gray-200 dark:bg-gray-700 dark:hover:bg-gray-600 rounded">
                            This Week
                        </button>
                        <button onclick="setDateRange('month')"
                            class="px-2 py-1 text-xs bg-gray-100 hover:bg-gray-200 dark:bg-gray-700 dark:hover:bg-gray-600 rounded">
                            This Month
                        </button>
                        <button onclick="setDateRange('last30')"
                            class="px-2 py-1 text-xs bg-gray-100 hover:bg-gray-200 dark:bg-gray-700 dark:hover:bg-gray-600 rounded">
                            Last 30 Days
                        </button>
                    </div>
                </div>
                <div class="w-12 h-12 bg-orange-100 dark:bg-orange-900 rounded-full flex items-center justify-center">
                    <svg class="w-6 h-6 text-orange-600 dark:text-orange-400" fill="none" stroke="currentColor"
                        viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M13 10V3L4 14h7v7l9-11h-7z" />
                    </svg>
                </div>
            </div>
        </div>
    </div>

    <!-- Completed Tasks Table -->
    <div class="bg-white dark:bg-gray-800 rounded-lg shadow overflow-hidden border border-gray-200 dark:border-gray-700">
        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-gray-200 dark:divide-gray-700">
                <thead class="bg-gray-50 dark:bg-gray-700">
                    <tr>
                        <th
                            class="px-4 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">
                            Ticket
                        </th>
                        <th
                            class="px-4 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">
                            Request Description
                        </th>
                        <th
                            class="px-4 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">
                            Priority
                        </th>
                        <th
                            class="px-4 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">
                            Requester
                        </th>
                        <th
                            class="px-4 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">
                            Items Worked On
                        </th>
                        <th
                            class="px-4 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">
                            Completed Date
                        </th>
                        <th
                            class="px-4 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">
                            Time (hrs)
                        </th>
                    </tr>
                </thead>
                <tbody class="bg-white dark:bg-gray-800 divide-y divide-gray-200 dark:divide-gray-700">
                    @forelse($completedAssignments as $assignment)
                        @php
                            $completionTime = null;
                            if ($assignment->assigned_at && $assignment->completed_at) {
                                $completionTime = round(
                                    $assignment->assigned_at->diffInHours($assignment->completed_at),
                                    1,
                                );
                            }
                        @endphp
                        <tr class="hover:bg-gray-50 dark:hover:bg-gray-700">
                            <td class="px-4 py-3 whitespace-nowrap">
                                <a href="{{ route('maintenance-requests.show', $assignment->maintenanceRequest->id) }}"
                                    class="text-blue-600 hover:text-blue-800 dark:text-blue-400 dark:hover:text-blue-300 font-medium text-sm">
                                    {{ $assignment->maintenanceRequest->ticket_number ?? 'N/A' }}
                                </a>
                            </td>
                            <td class="px-4 py-3">
                                <p class="text-sm text-gray-700 dark:text-gray-300 max-w-xs truncate">
                                    {{ Str::limit($assignment->maintenanceRequest->description ?? 'N/A', 50) }}
                                </p>
                            </td>
                            <td class="px-4 py-3 whitespace-nowrap">
                                @php
                                    $priorityColors = [
                                        'emergency' => 'bg-red-100 text-red-800 dark:bg-red-900 dark:text-red-300',
                                        'high' =>
                                            'bg-orange-100 text-orange-800 dark:bg-orange-900 dark:text-orange-300',
                                        'medium' =>
                                            'bg-yellow-100 text-yellow-800 dark:bg-yellow-900 dark:text-yellow-300',
                                        'low' => 'bg-green-100 text-green-800 dark:bg-green-900 dark:text-green-300',
                                    ];
                                    $colorClass =
                                        $priorityColors[$assignment->maintenanceRequest->priority] ??
                                        'bg-gray-100 text-gray-800';
                                @endphp
                                <span class="px-2 py-1 text-xs rounded-full {{ $colorClass }}">
                                    {{ ucfirst($assignment->maintenanceRequest->priority ?? 'N/A') }}
                                </span>
                            </td>
                            <td class="px-4 py-3 whitespace-nowrap">
                                <p class="text-sm text-gray-700 dark:text-gray-300">
                                    {{ $assignment->maintenanceRequest->user->full_name ?? 'N/A' }}
                                </p>
                            </td>
                            <td class="px-4 py-3">
                                <div class="flex flex-wrap gap-1">
                                    @foreach ($assignment->maintenanceRequest->items as $item)
                                        <span
                                            class="px-2 py-1 text-xs bg-gray-100 dark:bg-gray-700 text-gray-700 dark:text-gray-300 rounded">
                                            {{ $item->item->name ?? 'N/A' }}
                                        </span>
                                    @endforeach
                                </div>
                            </td>
                            <td class="px-4 py-3 whitespace-nowrap text-sm text-gray-500 dark:text-gray-400">
                                {{ $assignment->completed_at ? $assignment->completed_at->format('M d, Y H:i') : 'N/A' }}
                            </td>
                            <td class="px-4 py-3 whitespace-nowrap text-sm text-gray-500 dark:text-gray-400">
                                {{ $completionTime ? $completionTime . ' hrs' : 'N/A' }}
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="7" class="px-4 py-8 text-center text-gray-500 dark:text-gray-400">
                                No completed tasks found for the selected period.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        @if ($completedAssignments->hasPages())
            <div class="px-4 py-3 border-t border-gray-200 dark:border-gray-700">
                {{ $completedAssignments->withQueryString()->links() }}
            </div>
        @endif
    </div>
@endsection

@push('scripts')
    <script>
        // Toggle custom date range visibility
        document.getElementById('customPeriodRadio')?.addEventListener('change', function() {
            const customRange = document.getElementById('customDateRange');
            if (this.checked) {
                customRange.style.display = 'flex';
            }
        });

        document.querySelectorAll('input[name="period"]:not(#customPeriodRadio)').forEach(radio => {
            radio.addEventListener('change', function() {
                if (this.checked && this.value !== 'custom') {
                    document.getElementById('customDateRange').style.display = 'none';
                }
            });
        });

        // Export function
        function exportReport(type) {
            const form = document.getElementById('reportForm');
            const startDate = document.getElementById('start_date')?.value;
            const endDate = document.getElementById('end_date')?.value;
            const period = document.querySelector('input[name="period"]:checked')?.value;

            let url = '';
            if (type === 'csv') {
                url = '{{ route('reports.my.export') }}';
            } else {
                url = '{{ route('reports.my.download') }}';
            }

            // Build query parameters
            const params = new URLSearchParams();
            if (period) params.append('period', period);
            if (startDate) params.append('start_date', startDate);
            if (endDate) params.append('end_date', endDate);

            window.location.href = url + '?' + params.toString();
        }

        // Set date range shortcuts
        function setDateRange(range) {
            const today = new Date();
            let startDate = new Date();

            switch (range) {
                case 'week':
                    startDate = new Date(today.setDate(today.getDate() - today.getDay()));
                    break;
                case 'month':
                    startDate = new Date(today.getFullYear(), today.getMonth(), 1);
                    break;
                case 'last30':
                    startDate = new Date(today.setDate(today.getDate() - 30));
                    break;
            }

            const endDate = new Date();

            document.getElementById('start_date').value = formatDate(startDate);
            document.getElementById('end_date').value = formatDate(endDate);
            document.getElementById('customPeriodRadio').checked = true;
            document.getElementById('customDateRange').style.display = 'flex';

            // Auto-submit the form
            document.getElementById('reportForm').submit();
        }

        function formatDate(date) {
            const year = date.getFullYear();
            const month = String(date.getMonth() + 1).padStart(2, '0');
            const day = String(date.getDate()).padStart(2, '0');
            return `${year}-${month}-${day}`;
        }

        function clearDates() {
            document.getElementById('start_date').value = '';
            document.getElementById('end_date').value = '';
            document.getElementById('customPeriodRadio').checked = false;
            document.getElementById('customDateRange').style.display = 'none';
            document.querySelector('input[name="period"][value="week"]').checked = true;
            document.getElementById('reportForm').submit();
        }
    </script>
@endpush
