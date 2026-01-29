@extends('layouts.app')

@section('content')
    <x-common.page-breadcrumb pageTitle="Maintenance Requests" />

    <div class="space-y-6">
        {{-- <div class="grid grid-cols-1 gap-4 sm:grid-cols-2 lg:grid-cols-4">
            <x-common.stat-card title="Total Requests" value="{{ $totalRequests ?? 0 }}" icon="bi bi-clipboard-check"
                variant="primary" />
            <x-common.stat-card title="Open Requests" value="{{ $openRequests ?? 0 }}" icon="bi bi-clock" variant="warning" />
            <x-common.stat-card title="Completed" value="{{ $completedRequests ?? 0 }}" icon="bi bi-check-circle"
                variant="success" />
            <x-common.stat-card title="My Requests" value="{{ $myRequests ?? 0 }}" icon="bi bi-person" variant="info" />
        </div> --}}

        <div class="rounded-2xl border border-gray-200 bg-white p-5 dark:border-gray-800 dark:bg-white/[0.03]">
            <form action="{{ $pageType === 'tasks' ? route('user.task') : route('my.requests') }}" method="GET"
                id="filterForm" class="space-y-4">

                <div class="flex flex-wrap items-center justify-between gap-4">
                    <div class="relative w-full max-w-md">
                        <input type="text" name="search" id="searchInput"
                            class="h-11 w-full rounded-lg border border-gray-300 bg-transparent px-4 pl-11 text-sm text-gray-800 focus:border-blue-500 focus:ring-2 focus:ring-blue-500/10 dark:border-gray-700 dark:bg-gray-900 dark:text-white"
                            placeholder="Search by ticket, item or description..." value="{{ request('search') }}">
                        <i class="bi bi-search absolute left-4 top-1/2 -translate-y-1/2 text-gray-400"></i>
                    </div>

                    <div class="flex items-center gap-3">
                        <a href="{{ route('maintenance-requests.export') }}"
                            class="inline-flex items-center rounded-lg border border-gray-300 bg-white px-4 py-2.5 text-sm font-medium text-gray-700 hover:bg-gray-50 dark:border-gray-700 dark:bg-gray-800 dark:text-gray-400 dark:hover:bg-white/[0.03]">
                            <i class="bi bi-download me-2"></i> Export
                        </a>
                        <a href="{{ route('maintenance-requests.create') }}"
                            class="inline-flex items-center rounded-lg bg-blue-600 px-4 py-2.5 text-sm font-medium text-white hover:bg-blue-700 transition shadow-sm">
                            <i class="bi bi-plus-lg me-2"></i> New Request
                        </a>
                    </div>
                </div>

                <div class="flex flex-wrap items-center gap-6 border-t border-gray-100 pt-4 dark:border-gray-800">
                    <div class="flex items-center gap-2">
                        <label class="text-xs font-semibold uppercase tracking-wider text-gray-500">Status:</label>
                        <select name="status"
                            class="filter-select h-9 rounded-md border-gray-300 bg-gray-50 py-1 text-xs dark:border-gray-700 dark:bg-gray-900 dark:text-gray-300">
                            <option value="">All Statuses</option>
                            @foreach (App\Models\MaintenanceRequest::getStatusOptions() as $key => $value)
                                <option value="{{ $key }}" {{ request('status') == $key ? 'selected' : '' }}>
                                    {{ $value }}</option>
                            @endforeach
                        </select>
                    </div>

                    <div class="flex items-center gap-2">
                        <label class="text-xs font-semibold uppercase tracking-wider text-gray-500">Priority:</label>
                        <select name="priority"
                            class="filter-select h-9 rounded-md border-gray-300 bg-gray-50 py-1 text-xs dark:border-gray-700 dark:bg-gray-900 dark:text-gray-300">
                            <option value="">All Priorities</option>
                            @foreach (App\Models\MaintenanceRequest::getPriorityOptions() as $key => $value)
                                <option value="{{ $key }}" {{ request('priority') == $key ? 'selected' : '' }}>
                                    {{ $value }}</option>
                            @endforeach
                        </select>
                    </div>

                    @if (request()->anyFilled(['search', 'status', 'priority']))
                        <a href="{{ $pageType === 'tasks' ? route('user.task') : route('my.requests') }}"
                            class="text-xs font-medium text-red-500 hover:text-red-600 transition-colors">
                            <i class="bi bi-arrow-counterclockwise me-1"></i> Reset Filters
                        </a>
                    @endif
                </div>
            </form>
        </div>

        <div class="overflow-hidden rounded-2xl border border-gray-200 bg-white dark:border-gray-800 dark:bg-white/[0.03]">
            <div class="overflow-x-auto">
                <table class="w-full text-left text-sm">
                    <thead class="bg-gray-50/50 border-b border-gray-100 dark:bg-gray-800/50 dark:border-gray-800">
                        <tr>
                            <th class="px-6 py-4 font-semibold text-gray-700 dark:text-gray-300">#</th>
                            <th class="px-6 py-4 font-semibold text-gray-700 dark:text-gray-300">Ticket</th>
                            <th class="px-6 py-4 font-semibold text-gray-700 dark:text-gray-300">Item</th>
                            <th class="px-6 py-4 font-semibold text-gray-700 dark:text-gray-300">Issue Type</th>
                            <th class="px-6 py-4 font-semibold text-gray-700 dark:text-gray-300">Priority</th>
                            <th class="px-6 py-4 font-semibold text-gray-700 dark:text-gray-300">Status</th>
                            <th class="px-6 py-4 text-right font-semibold text-gray-700 dark:text-gray-300">Actions</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-100 dark:divide-gray-800">
                        @forelse ($requests as $index => $request)
                            <tr class="hover:bg-gray-50/50 transition dark:hover:bg-gray-800/40">
                                <td class="px-6 py-4 text-gray-500">{{ $requests->firstItem() + $index }}</td>
                                <td class="px-6 py-4 font-medium text-gray-800 dark:text-white">
                                    {{ $request->ticket_number }}</td>
                                <td class="px-6 py-4 text-gray-600 dark:text-gray-400">{{ $request->item?->name ?? 'N/A' }}
                                </td>
                                <td class="px-6 py-4 text-gray-600 dark:text-gray-400">{{ $request->getIssueTypeText() }}
                                </td>
                                <td class="px-6 py-4">
                                    <span
                                        class="inline-flex rounded-full px-2.5 py-0.5 text-xs font-medium {{ $request->getPriorityBadgeClass() }}">
                                        {{ $request->getPriorityText() }}
                                    </span>
                                </td>
                                <td class="px-6 py-4">
                                    <span
                                        class="inline-flex items-center gap-1.5 rounded-full px-2.5 py-0.5 text-xs font-medium {{ $request->getStatusBadgeClass() }}">
                                        {{ $request->getStatusText() }}
                                    </span>
                                </td>
                                <td class="px-6 py-4 text-right">
                                    <div class="flex justify-end gap-2">
                                        <a href="{{ route('maintenance-requests.show', $request) }}"
                                            class="flex h-8 w-8 items-center justify-center rounded-lg bg-blue-50 text-blue-600 hover:bg-blue-600 hover:text-white transition dark:bg-blue-500/10 dark:text-blue-400">
                                            <i class="bi bi-eye"></i>
                                        </a>
                                        @can('update', $request)
                                            <a href="{{ route('maintenance-requests.edit', $request) }}"
                                                class="flex h-8 w-8 items-center justify-center rounded-lg bg-amber-50 text-amber-600 hover:bg-amber-600 hover:text-white transition dark:bg-amber-500/10 dark:text-amber-400">
                                                <i class="bi bi-pencil"></i>
                                            </a>
                                        @endcan
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="7" class="py-12 text-center text-gray-500">No maintenance requests found.
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
            @if ($requests->hasPages())
                <div class="border-t border-gray-100 p-6 dark:border-gray-800">
                    {{ $requests->withQueryString()->links('vendor.pagination.dashboard') }}
                </div>
            @endif
        </div>
    </div>
@endsection

@push('scripts')
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const filterForm = document.getElementById('filterForm');
            const searchInput = document.getElementById('searchInput');
            const selects = document.querySelectorAll('.filter-select');

            // Auto-submit dropdowns
            selects.forEach(select => {
                select.addEventListener('change', () => filterForm.submit());
            });

            // Debounced auto-submit for search
            let typingTimer;
            searchInput.addEventListener('input', () => {
                clearTimeout(typingTimer);
                typingTimer = setTimeout(() => {
                    filterForm.submit();
                }, 600);
            });
        });
    </script>
@endpush
