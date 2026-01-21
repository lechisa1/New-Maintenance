@extends('layouts.app')

@section('content')
    <x-common.page-breadcrumb pageTitle="Role Management" />

    <div class="grid grid-cols-1 gap-6">
        <!-- Statistics Cards -->
        <div class="grid grid-cols-1 gap-4 sm:grid-cols-2 lg:grid-cols-4">
            <x-common.stat-card title="Total Roles" value="{{ $roles->total() }}" icon="bi bi-shield-lock" variant="primary" />
            <x-common.stat-card title="System Roles" value="{{ $systemRolesCount ?? 0 }}" icon="bi bi-shield-check"
                variant="warning" />
            <x-common.stat-card title="Custom Roles" value="{{ $customRolesCount ?? 0 }}" icon="bi bi-shield-plus"
                variant="success" />
            <x-common.stat-card title="Total Users" value="{{ $totalUsers ?? 0 }}" icon="bi bi-people" variant="info" />
        </div>

        <!-- Filters Card -->
        <div class="rounded-2xl border border-gray-200 bg-white p-6 dark:border-gray-800 dark:bg-white/[0.03]">
            <div class="mb-4 flex items-center justify-between">
                <h3 class="text-lg font-semibold text-gray-800 dark:text-white/90">
                    <i class="bi bi-funnel me-2"></i>Filter Roles
                </h3>
                <a href="{{ route('roles.index') }}" class="text-sm font-medium text-blue-500 hover:text-blue-600">
                    Reset All
                </a>
            </div>

            <form action="{{ route('roles.index') }}" method="GET" id="filterForm">
                <div class="grid grid-cols-1 gap-5 md:grid-cols-2 lg:grid-cols-4">
                    <div>
                        <label class="mb-2 block text-sm font-medium text-gray-700 dark:text-gray-300">Search</label>
                        <div class="relative">
                            <input type="text" name="search" id="searchInput"
                                class="h-11 w-full rounded-lg border border-gray-300 bg-transparent px-4 py-2.5 pl-11 text-sm text-gray-800 focus:border-blue-500 focus:ring-2 focus:ring-blue-500/10 dark:border-gray-700 dark:bg-gray-900 dark:text-white"
                                placeholder="Name or description..." value="{{ request('search') }}">
                            <i class="bi bi-search absolute left-3 top-1/2 -translate-y-1/2 text-gray-400"></i>
                        </div>
                    </div>

                    <div>
                        <label class="mb-2 block text-sm font-medium text-gray-700 dark:text-gray-300">Guard</label>
                        <select name="guard_name"
                            class="filter-select h-11 w-full rounded-lg border border-gray-300 bg-transparent px-4 py-2.5 text-sm dark:border-gray-700 dark:bg-gray-900 dark:text-white">
                            <option value="">All Guards</option>
                            @foreach ($guards as $guard)
                                <option value="{{ $guard }}" {{ request('guard_name') == $guard ? 'selected' : '' }}>
                                    {{ ucfirst($guard) }}
                                </option>
                            @endforeach
                        </select>
                    </div>

                    <div>
                        <label class="mb-2 block text-sm font-medium text-gray-700 dark:text-gray-300">Role Type</label>
                        <select name="type"
                            class="filter-select h-11 w-full rounded-lg border border-gray-300 bg-transparent px-4 py-2.5 text-sm dark:border-gray-700 dark:bg-gray-900 dark:text-white">
                            <option value="">All Types</option>
                            <option value="system" {{ request('type') == 'system' ? 'selected' : '' }}>System Roles
                            </option>
                            <option value="custom" {{ request('type') == 'custom' ? 'selected' : '' }}>Custom Roles
                            </option>
                        </select>
                    </div>

                    <div>
                        <label class="mb-2 block text-sm font-medium text-gray-700 dark:text-gray-300">Per Page</label>
                        <select name="per_page"
                            class="filter-select h-11 w-full rounded-lg border border-gray-300 bg-transparent px-4 py-2.5 text-sm dark:border-gray-700 dark:bg-gray-900 dark:text-white">
                            @foreach ([10, 25, 50, 100] as $size)
                                <option value="{{ $size }}"
                                    {{ request('per_page', 10) == $size ? 'selected' : '' }}>{{ $size }} items
                                </option>
                            @endforeach
                        </select>
                    </div>
                </div>
            </form>
        </div>
        <!-- Roles Table -->
        <div class="rounded-2xl border border-gray-200 bg-white p-6 dark:border-gray-800 dark:bg-white/[0.03]">
            <div class="mb-6 flex items-center justify-between">
                <h3 class="text-lg font-semibold text-gray-800 dark:text-white/90">
                    <i class="bi bi-shield-lock me-2"></i>Roles List
                </h3>
                <div class="flex space-x-3">
                    <a href="{{ route('roles.create') }}"
                        class="inline-flex items-center rounded-lg bg-blue-500 px-4 py-2.5 text-sm font-medium text-white shadow-theme-xs hover:bg-blue-600 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:ring-offset-2 dark:focus:ring-offset-gray-900">
                        <i class="bi bi-plus-lg me-2"></i> Create Role
                    </a>
                </div>
            </div>

            <!-- Roles Table -->
            <div class="overflow-x-auto">
                <table class="min-w-full border border-gray-200 dark:border-gray-800 rounded-lg">
                    <thead class="bg-gray-50 dark:bg-gray-800">
                        <tr>
                            <th class="px-4 py-3 text-left text-sm font-semibold text-gray-800 dark:text-white/90">Id</th>
                            <th class="px-4 py-3 text-left text-sm font-semibold text-gray-800 dark:text-white/90">Role</th>

                            </th>
                            <th class="px-4 py-3 text-left text-sm font-semibold text-gray-800 dark:text-white/90">
                                Permissions</th>
                            <th class="px-4 py-3 text-left text-sm font-semibold text-gray-800 dark:text-white/90">Users
                            </th>

                            </th>
                            <th class="px-4 py-3 text-center text-sm font-semibold text-gray-800 dark:text-white/90">Actions
                            </th>
                        </tr>
                    </thead>

                    <tbody class="divide-y divide-gray-200 dark:divide-gray-700">
                        @forelse($roles as $index => $role)
                            <tr class="hover:bg-gray-50 dark:hover:bg-gray-800/50">
                                <!-- Role Name & Info -->
                                <td class="px-4 py-3">
                                    <div class="font-medium text-gray-800 dark:text-white">
                                        {{ $index + 1 }}
                                    </div>
                                </td>
                                <td class="px-4 py-3">
                                    <div class="flex items-center gap-3">

                                        <div>
                                            <div class="flex items-center gap-2">
                                                <div class="font-medium text-gray-800 dark:text-white">
                                                    {{ $role->display_name ?? ucwords(str_replace(['-', '_'], ' ', $role->name)) }}
                                                </div>

                                            </div>


                                        </div>
                                    </div>
                                </td>



                                <!-- Permissions Count -->
                                <td class="px-4 py-3">
                                    <div class="flex items-center gap-2">
                                        <span class="text-sm font-medium text-gray-700 dark:text-gray-300">
                                            {{ $role->permissions_count ?? 0 }}
                                        </span>
                                        <span class="text-xs text-gray-500 dark:text-gray-400">
                                            permissions
                                        </span>
                                    </div>
                                </td>

                                <!-- Users Count -->
                                <td class="px-4 py-3">
                                    <div class="flex items-center gap-2">
                                        <span class="text-sm font-medium text-gray-700 dark:text-gray-300">
                                            {{ $role->users_count ?? 0 }}
                                        </span>
                                        <span class="text-xs text-gray-500 dark:text-gray-400">
                                            users
                                        </span>
                                    </div>
                                </td>



                                <!-- Actions -->
                                <td class="px-4 py-3 text-center">
                                    <div class="flex justify-center gap-5">
                                        <a href="{{ route('roles.show', $role) }}"
                                            class="text-blue-600 hover:text-blue-800 dark:text-blue-400 dark:hover:text-blue-300 text-sm font-medium">
                                            View
                                        </a>

                                        <a href="{{ route('roles.edit', $role) }}"
                                            class="text-yellow-600 hover:text-yellow-800 dark:text-yellow-400 dark:hover:text-yellow-300 text-sm font-medium ">
                                            Edit
                                        </a>

                                        <form action="{{ route('roles.destroy', $role) }}" method="POST"
                                            onsubmit="return confirm('Are you sure you want to delete this role?')">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit"
                                                class="text-red-600 hover:text-red-800 dark:text-red-400 dark:hover:text-red-300 text-sm font-medium ">
                                                Delete
                                            </button>
                                        </form>
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="6" class="px-4 py-6 text-center text-gray-500 dark:text-gray-400">
                                    <div class="py-8">
                                        <i class="bi bi-shield-slash text-4xl text-gray-300 dark:text-gray-600"></i>
                                        <p class="mt-2 text-sm">No roles found</p>
                                        @if (request()->hasAny(['search', 'guard_name', 'type']))
                                            <a href="{{ route('roles.index') }}"
                                                class="mt-3 inline-flex items-center text-sm text-blue-500 hover:text-blue-600 dark:text-blue-400">
                                                Clear filters to see all roles
                                            </a>
                                        @else
                                            <a href="{{ route('roles.create') }}"
                                                class="mt-3 inline-flex items-center text-sm text-blue-500 hover:text-blue-600 dark:text-blue-400">
                                                <i class="bi bi-plus-circle me-1"></i> Create your first role
                                            </a>
                                        @endif
                                    </div>
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            <!-- Pagination Links -->
            @if ($roles->hasPages())
                <div class="mt-6">
                    {{ $roles->withQueryString()->links('vendor.pagination.dashboard') }}
                </div>
            @endif
        </div>
    </div>
@endsection

@push('scripts')
    <script>
        // Optional: Add some interactivity if needed
        document.addEventListener('DOMContentLoaded', function() {
            // Add confirmation for deleting system roles
            const deleteForms = document.querySelectorAll('form[action*="/roles/"]');
            deleteForms.forEach(form => {
                form.addEventListener('submit', function(e) {
                    const roleName = this.closest('tr').querySelector('.font-medium').textContent
                        .trim();
                    if (!confirm(
                            `Are you sure you want to delete the role "${roleName}"? This action cannot be undone.`
                        )) {
                        e.preventDefault();
                    }
                });
            });
        });
        document.addEventListener('DOMContentLoaded', function() {
            const filterForm = document.getElementById('filterForm');
            const searchInput = document.getElementById('searchInput');
            const selects = document.querySelectorAll('.filter-select');

            // 1. Auto-submit when dropdowns change
            selects.forEach(select => {
                select.addEventListener('change', () => {
                    filterForm.submit();
                });
            });

            // 2. Auto-submit when typing (with Debounce)
            let typingTimer;
            const doneTypingInterval = 600; // Wait 600ms after user stops typing

            searchInput.addEventListener('input', () => {
                clearTimeout(typingTimer);
                typingTimer = setTimeout(() => {
                    filterForm.submit();
                }, doneTypingInterval);
            });
        });
    </script>
@endpush
