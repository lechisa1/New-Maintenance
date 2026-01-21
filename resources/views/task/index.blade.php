@extends('layouts.app')

@section('content')
    <x-common.page-breadcrumb pageTitle="Maintenance Requests" />

    <div class="space-y-6">
        <!-- Header -->
        <div class="flex flex-col gap-4 sm:flex-row sm:items-center sm:justify-between">
            <div>
                <h2 class="text-xl font-semibold text-gray-800 dark:text-white/90">
                    <i class="bi bi-tools me-2"></i>Maintenance Requests
                </h2>
                <p class="mt-1 text-sm text-gray-500 dark:text-gray-400">
                    Track and manage all maintenance requests
                </p>
            </div>

            <div class="flex gap-2">
                <a href="{{ route('maintenance-requests.export') }}"
                    class="rounded-lg border border-gray-300 bg-white px-4 py-2 text-sm font-medium text-gray-700 shadow-theme-xs hover:bg-gray-50 dark:border-gray-700 dark:bg-gray-800 dark:text-gray-400 dark:hover:bg-white/[0.03]">
                    <i class="bi bi-download me-2"></i>Export
                </a>
                <a href="{{ route('maintenance-requests.create') }}"
                    class="rounded-lg bg-blue-500 px-4 py-2 text-sm font-medium text-white shadow-theme-xs hover:bg-blue-600 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:ring-offset-2 dark:focus:ring-offset-gray-900">
                    <i class="bi bi-plus-lg me-2"></i>New Request
                </a>
            </div>
        </div>

        <!-- Statistics Cards -->
        <div class="grid grid-cols-1 gap-4 sm:grid-cols-2 lg:grid-cols-4">
            <x-common.stat-card title="Total Requests" value="{{ $totalRequests ?? 0 }}" icon="bi bi-clipboard-check"
                variant="primary" />
            <x-common.stat-card title="Open Requests" value="{{ $openRequests ?? 0 }}" icon="bi bi-clock"
                variant="warning" />
            <x-common.stat-card title="Completed" value="{{ $completedRequests ?? 0 }}" icon="bi bi-check-circle"
                variant="success" />
            <x-common.stat-card title="My Requests" value="{{ $myRequests ?? 0 }}" icon="bi bi-person" variant="info" />
        </div>

        <!-- Filters Card -->
        <div class="rounded-2xl border border-gray-200 bg-white p-6 dark:border-gray-800 dark:bg-white/[0.03]">
            <h3 class="mb-4 text-sm font-semibold text-gray-800 dark:text-white/90">
                <i class="bi bi-funnel me-2"></i>Filter Requests
            </h3>

            <form action="{{ route('maintenance-requests.index') }}" method="GET">
                <div class="grid grid-cols-1 gap-5 md:grid-cols-2 lg:grid-cols-4">
                    <!-- Search -->
                    <div>
                        <label class="mb-2 block text-sm font-medium text-gray-700 dark:text-gray-300">
                            Search
                        </label>
                        <div class="relative">
                            <input type="text" name="search"
                                class="h-11 w-full rounded-lg border border-gray-300 bg-transparent px-4 py-2.5 pl-11 text-sm text-gray-800 shadow-theme-xs placeholder:text-gray-400 focus:border-blue-300 focus:outline-none focus:ring-2 focus:ring-blue-500/10 dark:border-gray-700 dark:bg-gray-900 dark:text-white/90 dark:placeholder:text-white/30 dark:focus:border-blue-800"
                                placeholder="Search by ticket or description..." value="{{ request('search') }}">
                            <i class="bi bi-search absolute left-3 top-1/2 -translate-y-1/2 text-gray-400"></i>
                        </div>
                    </div>

                    <!-- Status Filter -->
                    <div>
                        <label for="status" class="mb-2 block text-sm font-medium text-gray-700 dark:text-gray-300">
                            Status
                        </label>
                        <select name="status" id="status"
                            class="h-11 w-full rounded-lg border border-gray-300 bg-transparent px-4 py-2.5 text-sm text-gray-800 shadow-theme-xs focus:border-blue-300 focus:outline-none focus:ring-2 focus:ring-blue-500/10 dark:border-gray-700 dark:bg-gray-900 dark:text-white/90 dark:focus:border-blue-800">
                            <option value="">All Status</option>
                            @foreach (App\Models\MaintenanceRequest::getStatusOptions() as $key => $value)
                                <option value="{{ $key }}" {{ request('status') == $key ? 'selected' : '' }}>
                                    {{ $value }}
                                </option>
                            @endforeach
                        </select>
                    </div>

                    <!-- Priority Filter -->
                    <div>
                        <label for="priority" class="mb-2 block text-sm font-medium text-gray-700 dark:text-gray-300">
                            Priority
                        </label>
                        <select name="priority" id="priority"
                            class="h-11 w-full rounded-lg border border-gray-300 bg-transparent px-4 py-2.5 text-sm text-gray-800 shadow-theme-xs focus:border-blue-300 focus:outline-none focus:ring-2 focus:ring-blue-500/10 dark:border-gray-700 dark:bg-gray-900 dark:text-white/90 dark:focus:border-blue-800">
                            <option value="">All Priority</option>
                            @foreach (App\Models\MaintenanceRequest::getPriorityOptions() as $key => $value)
                                <option value="{{ $key }}" {{ request('priority') == $key ? 'selected' : '' }}>
                                    {{ $value }}
                                </option>
                            @endforeach
                        </select>
                    </div>

                    <!-- Actions -->
                    <div class="flex items-end">
                        <div class="flex w-full gap-2">
                            <button type="submit"
                                class="h-11 flex-1 rounded-lg bg-blue-500 px-4 py-2.5 text-sm font-medium text-white shadow-theme-xs hover:bg-blue-600 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:ring-offset-2 dark:focus:ring-offset-gray-900">
                                Apply Filters
                            </button>
                            <a href="{{ route('maintenance-requests.index') }}"
                                class="h-11 rounded-lg border border-gray-300 bg-white px-4 py-2.5 text-sm font-medium text-gray-700 shadow-theme-xs hover:bg-gray-50 dark:border-gray-700 dark:bg-gray-800 dark:text-gray-400 dark:hover:bg-white/[0.03]">
                                Clear
                            </a>
                        </div>
                    </div>
                </div>
            </form>
        </div>

        <div class="rounded-2xl border border-gray-200 bg-white p-6 dark:border-gray-800 dark:bg-white/[0.03]">
            <h3 class="mb-6 text-lg font-semibold text-gray-800 dark:text-white/90">
                <i class="bi bi-list-ul me-2"></i>Maintenance Requests
            </h3>

            @if ($requests->count())
                <div class="overflow-x-auto">
                    <table class="min-w-full border border-gray-200 dark:border-gray-800 rounded-lg">
                        <thead class="bg-gray-50 dark:bg-gray-800">
                            <tr>
                                <th class="px-4 py-3 text-left text-sm font-semibold">#</th>
                                <th class="px-4 py-3 text-left text-sm font-semibold">Ticket</th>
                                <th class="px-4 py-3 text-left text-sm font-semibold">Item</th>
                                <th class="px-4 py-3 text-left text-sm font-semibold">Issue Type</th>
                                <th class="px-4 py-3 text-left text-sm font-semibold">Priority</th>
                                <th class="px-4 py-3 text-left text-sm font-semibold">Status</th>
                                <th class="px-4 py-3 text-left text-sm font-semibold">Requested</th>
                                <th class="px-4 py-3 text-center text-sm font-semibold">Actions</th>
                            </tr>
                        </thead>

                        <tbody class="divide-y divide-gray-200 dark:divide-gray-700">
                            @foreach ($requests as $index => $request)
                                <tr class="hover:bg-gray-50 dark:hover:bg-gray-800/50">

                                    {{-- ORDER NUMBER (pagination safe) --}}
                                    <td class="px-4 py-3 text-sm text-gray-600 dark:text-gray-400">
                                        {{ $requests->firstItem() + $index }}
                                    </td>

                                    {{-- Ticket --}}
                                    <td class="px-4 py-3 font-medium text-gray-800 dark:text-white">
                                        {{ $request->ticket_number }}
                                    </td>

                                    {{-- Item --}}
                                    <td class="px-4 py-3 text-sm">
                                        {{ $request->item?->name ?? 'N/A' }}
                                    </td>

                                    {{-- Issue Type --}}
                                    <td class="px-4 py-3 text-sm">
                                        {{ $request->getIssueTypeText() }}
                                    </td>

                                    {{-- Priority --}}
                                    <td class="px-4 py-3">
                                        <span class="px-2 py-1 text-xs rounded {{ $request->getPriorityBadgeClass() }}">
                                            {{ $request->getPriorityText() }}
                                        </span>
                                    </td>

                                    {{-- Status --}}
                                    <td class="px-4 py-3">
                                        <span class="px-2 py-1 text-xs rounded {{ $request->getStatusBadgeClass() }}">
                                            {{ $request->getStatusText() }}
                                        </span>
                                    </td>

                                    {{-- Requested Date --}}
                                    <td class="px-4 py-3 text-sm text-gray-600 dark:text-gray-400">
                                        {{ $request->getRequestedDate() }}
                                    </td>

                                    {{-- Actions --}}
                                    <td class="px-4 py-3 text-center">
                                        <div class="flex justify-center gap-3">
                                            <a href="{{ route('maintenance-requests.show', $request) }}"
                                                class="text-blue-600 hover:underline text-sm">
                                                View
                                            </a>

                                            @can('update', $request)
                                                <a href="{{ route('maintenance-requests.edit', $request) }}"
                                                    class="text-yellow-600 hover:underline text-sm">
                                                    Edit
                                                </a>
                                            @endcan
                                        </div>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>

                {{-- Pagination --}}
                @if ($requests->hasPages())
                    <div class="mt-6">
                        {{ $requests->withQueryString()->links('vendor.pagination.dashboard') }}
                    </div>
                @endif
            @else
                {{-- Empty State --}}
                <div class="py-12 text-center">
                    <div
                        class="mx-auto mb-4 flex h-16 w-16 items-center justify-center rounded-full bg-gray-100 dark:bg-gray-800">
                        <i class="bi bi-tools text-2xl text-gray-400"></i>
                    </div>
                    <h3 class="mb-2 text-lg font-semibold text-gray-800 dark:text-white/90">
                        No Maintenance Requests
                    </h3>
                    <p class="text-gray-500 dark:text-gray-400">
                        No maintenance requests found.
                    </p>
                </div>
            @endif
        </div>

    </div>

    <!-- Success Message -->
    @if (session('success'))
        <div class="fixed bottom-4 right-4 z-50">
            <x-ui.alert variant="success" title="Success" :message="session('success')" />
        </div>
    @endif
@endsection
