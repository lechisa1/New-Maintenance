@extends('layouts.app')

@section('content')
    <x-common.page-breadcrumb pageTitle="Issue Types Management" />

    <div class="space-y-6">
        <!-- Header -->
        <div class="flex flex-col gap-4 sm:flex-row sm:items-center sm:justify-between">
            <div>
                <h2 class="text-xl font-semibold text-gray-800 dark:text-white/90">
                    <i class="bi bi-tags me-2"></i>Issue Types List
                </h2>
                <p class="mt-1 text-sm text-gray-500 dark:text-gray-400">
                    Manage all issue types for maintenance requests
                </p>
            </div>

            <div class="flex gap-2">
                <a href="{{ route('issue-types.create') }}"
                    class="rounded-lg bg-blue-500 px-4 py-2 text-sm font-medium text-white shadow-theme-xs hover:bg-blue-600 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:ring-offset-2 dark:focus:ring-offset-gray-900">
                    <i class="bi bi-plus-lg me-2"></i>Add Issue Type
                </a>
            </div>
        </div>

        <!-- Statistics Cards -->
        <div class="grid grid-cols-1 gap-4 sm:grid-cols-2 lg:grid-cols-4">
            <x-common.stat-card title="Total Types" value="{{ $issueTypes->total() }}" icon="bi bi-tags"
                variant="primary" />
            <x-common.stat-card title="Active" value="{{ \App\Models\IssueType::where('is_active', true)->count() }}"
                icon="bi bi-check-circle" variant="success" />
            <x-common.stat-card title="Need Approval"
                value="{{ \App\Models\IssueType::where('is_need_approval', true)->count() }}" icon="bi bi-hourglass-split"
                variant="warning" />
            <x-common.stat-card title="Inactive" value="{{ \App\Models\IssueType::where('is_active', false)->count() }}"
                icon="bi bi-x-circle" variant="danger" />
        </div>

        <!-- Filters Card -->
        <div class="rounded-2xl border border-gray-200 bg-white p-6 dark:border-gray-800 dark:bg-white/[0.03]">
            <h3 class="mb-4 text-sm font-semibold text-gray-800 dark:text-white/90">
                <i class="bi bi-funnel me-2"></i>Filter Issue Types
            </h3>

            <form action="{{ route('issue-types.index') }}" method="GET">
                <div class="grid grid-cols-1 gap-5 md:grid-cols-2 lg:grid-cols-4">
                    <!-- Search -->
                    <div>
                        <label class="mb-2 block text-sm font-medium text-gray-700 dark:text-gray-300">
                            Search
                        </label>
                        <div class="relative">
                            <input type="text" name="search"
                                class="h-11 w-full rounded-lg border border-gray-300 bg-transparent px-4 py-2.5 pl-11 text-sm text-gray-800 shadow-theme-xs placeholder:text-gray-400 focus:border-blue-300 focus:outline-none focus:ring-2 focus:ring-blue-500/10 dark:border-gray-700 dark:bg-gray-900 dark:text-white/90 dark:placeholder:text-white/30 dark:focus:border-blue-800"
                                placeholder="Search issue types..." value="{{ $search }}">
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
                            <option value="active" {{ $status == 'active' ? 'selected' : '' }}>Active</option>
                            <option value="inactive" {{ $status == 'inactive' ? 'selected' : '' }}>Inactive</option>
                            <option value="need_approval" {{ $status == 'need_approval' ? 'selected' : '' }}>Need Approval
                            </option>
                        </select>
                    </div>

                    <!-- Actions -->
                    <div class="flex items-end">
                        <div class="flex w-full gap-2">
                            <button type="submit"
                                class="h-11 flex-1 rounded-lg bg-blue-500 px-4 py-2.5 text-sm font-medium text-white shadow-theme-xs hover:bg-blue-600 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:ring-offset-2 dark:focus:ring-offset-gray-900">
                                Apply Filters
                            </button>
                            @if ($search || $status)
                                <a href="{{ route('issue-types.index') }}"
                                    class="h-11 rounded-lg border border-gray-300 bg-white px-4 py-2.5 text-sm font-medium text-gray-700 shadow-theme-xs hover:bg-gray-50 dark:border-gray-700 dark:bg-gray-800 dark:text-gray-400 dark:hover:bg-white/[0.03]">
                                    Clear
                                </a>
                            @endif
                        </div>
                    </div>
                </div>
            </form>
        </div>

        <!-- Issue Types Table -->
        <div class="rounded-2xl border border-gray-200 bg-white p-6 dark:border-gray-800 dark:bg-white/[0.03]">
            <div class="mb-6 flex items-center justify-between">
                <h3 class="text-lg font-semibold text-gray-800 dark:text-white/90">
                    <i class="bi bi-list-ul me-2"></i>Registered Issue Types
                </h3>

                <div class="text-sm text-gray-500 dark:text-gray-400">
                    Showing {{ $issueTypes->firstItem() ?? 0 }} to {{ $issueTypes->lastItem() ?? 0 }} of
                    {{ $issueTypes->total() }} entries
                </div>
            </div>

            @if ($issueTypes->count() > 0)
                <div class="overflow-x-auto">
                    <table class="min-w-full border border-gray-200 dark:border-gray-800 rounded-lg">
                        <thead class="bg-gray-50 dark:bg-gray-800">
                            <tr>
                                <th class="px-4 py-3 text-left text-sm font-semibold">#</th>
                                <th class="px-4 py-3 text-left text-sm font-semibold">Issue Type</th>
                                <th class="px-4 py-3 text-left text-sm font-semibold">Description</th>
                                <th class="px-4 py-3 text-left text-sm font-semibold">Status</th>
                                <th class="px-4 py-3 text-left text-sm font-semibold">Approval</th>
                                <th class="px-4 py-3 text-left text-sm font-semibold">Created</th>
                                <th class="px-4 py-3 text-center text-sm font-semibold">Actions</th>
                            </tr>
                        </thead>

                        <tbody class="divide-y divide-gray-200 dark:divide-gray-700">
                            @foreach ($issueTypes as $index => $issueType)
                                <tr class="hover:bg-gray-50 dark:hover:bg-gray-800/50">
                                    <td class="px-4 py-3 text-sm text-gray-600 dark:text-gray-400">
                                        {{ $issueTypes->firstItem() + $index }}
                                    </td>

                                    <!-- Name + Icon -->
                                    <td class="px-4 py-3">
                                        <div class="flex items-center gap-3">

                                            <div>
                                                <div class="font-medium text-gray-800 dark:text-white">
                                                    {{ $issueType->name }}
                                                </div>

                                            </div>
                                        </div>
                                    </td>

                                    <!-- Description -->
                                    <td class="px-4 py-3">
                                        <div class="max-w-xs text-sm text-gray-600 dark:text-gray-400 truncate">
                                            {{ $issueType->description ?: 'No description' }}
                                        </div>
                                    </td>

                                    <!-- Status -->
                                    <td class="px-4 py-3">
                                        <form method="POST" action="{{ route('issue-types.toggle-status', $issueType) }}">
                                            @csrf
                                            @method('POST')
                                            <button type="submit"
                                                class="px-3 py-1 text-xs font-medium rounded-full {{ $issueType->is_active ? 'bg-green-100 text-green-800 dark:bg-green-900/30 dark:text-green-300' : 'bg-red-100 text-red-800 dark:bg-red-900/30 dark:text-red-300' }}">
                                                <i class="bi bi-circle-fill me-1 text-[8px]"></i>
                                                {{ $issueType->is_active ? 'Active' : 'Inactive' }}
                                            </button>
                                        </form>
                                    </td>

                                    <!-- Approval -->
                                    <td class="px-4 py-3">
                                        <form method="POST"
                                            action="{{ route('issue-types.toggle-approval', $issueType) }}">
                                            @csrf
                                            @method('POST')
                                            <button type="submit"
                                                class="px-3 py-1 text-xs font-medium rounded-full {{ $issueType->is_need_approval ? 'bg-yellow-100 text-yellow-800 dark:bg-yellow-900/30 dark:text-yellow-300' : 'bg-gray-100 text-gray-800 dark:bg-gray-900/30 dark:text-gray-300' }}">
                                                <i
                                                    class="bi bi-{{ $issueType->is_need_approval ? 'shield-check' : 'shield' }} me-1 text-xs"></i>
                                                {{ $issueType->is_need_approval ? 'Needs Approval' : 'Auto Approve' }}
                                            </button>
                                        </form>
                                    </td>

                                    <!-- Date -->
                                    <td class="px-4 py-3 text-sm text-gray-600 dark:text-gray-400">
                                        {{ $issueType->created_at->format('M d, Y') }}
                                    </td>

                                    <!-- Actions -->
                                    <td class="px-4 py-3">
                                        <div class="flex justify-center gap-3">
                                            <a href="{{ route('issue-types.edit', $issueType) }}"
                                                class="text-blue-600 hover:text-blue-800 dark:text-blue-400" title="Edit">
                                                <i class="bi bi-pencil"></i>
                                            </a>

                                            <form method="POST" action="{{ route('issue-types.destroy', $issueType) }}"
                                                onsubmit="return confirm('Are you sure you want to delete this issue type?');">
                                                @csrf
                                                @method('DELETE')
                                                <button type="submit"
                                                    class="text-red-600 hover:text-red-800 dark:text-red-400"
                                                    title="Delete">
                                                    <i class="bi bi-trash"></i>
                                                </button>
                                            </form>
                                        </div>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>

                @if ($issueTypes->hasPages())
                    <div class="mt-6">
                        {{ $issueTypes->withQueryString()->links('vendor.pagination.dashboard') }}
                    </div>
                @endif
            @else
                <div class="py-12 text-center">
                    <div
                        class="mx-auto mb-4 flex h-16 w-16 items-center justify-center rounded-full bg-gray-100 dark:bg-gray-800">
                        <i class="bi bi-tags text-2xl text-gray-400 dark:text-gray-500"></i>
                    </div>
                    <h3 class="mb-2 text-lg font-semibold text-gray-800 dark:text-white/90">
                        @if ($search || $status)
                            No Issue Types Found
                        @else
                            No Issue Types Found
                        @endif
                    </h3>
                    <p class="text-gray-500 dark:text-gray-400">
                        @if ($search || $status)
                            No issue types match your filters. Try adjusting your search criteria.
                        @else
                            No issue types have been created yet. Start by adding your first issue type.
                        @endif
                    </p>
                    <a href="{{ route('issue-types.create') }}"
                        class="mt-4 inline-flex items-center rounded-lg bg-blue-500 px-4 py-2.5 text-sm font-medium text-white shadow-theme-xs hover:bg-blue-600 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:ring-offset-2 dark:focus:ring-offset-gray-900">
                        <i class="bi bi-plus-lg me-2"></i> Add First Issue Type
                    </a>
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

    <!-- Error Message -->
    @if (session('error'))
        <div class="fixed bottom-4 right-4 z-50">
            <x-ui.alert variant="error" title="Error" :message="session('error')" />
        </div>
    @endif
@endsection
