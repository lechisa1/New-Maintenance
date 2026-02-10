@extends('layouts.app')
@php
    $breadcrumbs = [
        ['label' => 'Home', 'url' => url('/')],
        ['label' => 'Role Management'], // active page
    ];
@endphp

@section('content')
    <x-common.page-breadcrumb :breadcrumbs="$breadcrumbs" />


    <div class="space-y-6">


        <div class="rounded-2xl border border-gray-200 bg-white p-5 dark:border-gray-800 dark:bg-white/[0.03]">
            <form action="{{ route('roles.index') }}" method="GET" id="filterForm" class="space-y-4">
                <div class="flex flex-wrap items-center justify-between gap-4">
                    <div class="relative w-full max-w-sm">
                        <input type="text" name="search" id="searchInput"
                            class="h-11 w-full rounded-lg border border-gray-300 bg-transparent px-4 pl-11 text-sm text-gray-800 focus:border-blue-500 focus:ring-2 focus:ring-blue-500/10 dark:border-gray-700 dark:bg-gray-900 dark:text-white"
                            placeholder="Search roles..." value="{{ request('search') }}">
                        <i class="bi bi-search absolute left-4 top-1/2 -translate-y-1/2 text-gray-400"></i>
                    </div>

                    <div class="flex items-center gap-3">
                        <a href="{{ route('roles.create') }}"
                            class="inline-flex items-center rounded-lg bg-blue-600 px-4 py-2.5 text-sm font-medium text-white hover:bg-blue-700 transition shadow-sm">
                            <i class="bi bi-plus-lg me-2"></i> Create Role
                        </a>
                    </div>
                </div>

                <div class="flex flex-wrap items-center gap-4 border-t border-gray-100 pt-4 dark:border-gray-800">
                    <div class="flex items-center gap-2">
                        <label class="text-xs font-semibold uppercase tracking-wider text-gray-500">Status:</label>
                        <select name="status"
                            class="filter-select h-9 rounded-md border-gray-300 bg-gray-50 py-1 text-xs dark:border-gray-700 dark:bg-gray-900 dark:text-gray-300">
                            <option value="">All Statuses</option>
                            <option value="1" {{ request('status') == '1' ? 'selected' : '' }}>Active Only</option>
                            <option value="0" {{ request('status') == '0' ? 'selected' : '' }}>Inactive Only</option>
                        </select>
                    </div>

                    <div class="flex items-center gap-2">
                        <label class="text-xs font-semibold uppercase tracking-wider text-gray-500">Show:</label>
                        <select name="per_page"
                            class="filter-select h-9 rounded-md border-gray-300 bg-gray-50 py-1 text-xs dark:border-gray-700 dark:bg-gray-900 dark:text-gray-300">
                            @foreach ([5, 10, 25, 50] as $size)
                                <option value="{{ $size }}"
                                    {{ request('per_page', 10) == $size ? 'selected' : '' }}>{{ $size }}</option>
                            @endforeach
                        </select>
                    </div>

                    @if (request()->anyFilled(['search', 'status']))
                        <a href="{{ route('roles.index') }}" class="text-xs font-medium text-red-500 hover:text-red-600">
                            <i class="bi bi-x-circle me-1"></i> Reset Filters
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
                            <th class="px-6 py-4 font-semibold text-gray-700 dark:text-gray-300">Role Name</th>
                            <th class="px-6 py-4 font-semibold text-gray-700 dark:text-gray-300">Permissions</th>
                            <th class="px-6 py-4 font-semibold text-gray-700 dark:text-gray-300">Users</th>
                            <th class="px-6 py-4 font-semibold text-gray-700 dark:text-gray-300">Status</th>
                            <th class="px-6 py-4 text-right font-semibold text-gray-700 dark:text-gray-300">Actions</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-100 dark:divide-gray-800">
                        @forelse($roles as $index => $role)
                            <tr class="hover:bg-gray-50/50 transition dark:hover:bg-gray-800/40">
                                <td class="px-6 py-4 text-gray-500">{{ $roles->firstItem() + $index }}</td>
                                <td class="px-6 py-4">
                                    <span class="font-semibold text-gray-800 dark:text-white">
                                        {{ $role->display_name ?? ucwords(str_replace(['-', '_'], ' ', $role->name)) }}
                                    </span>
                                </td>
                                <td class="px-6 py-4">
                                    <span
                                        class="inline-flex items-center gap-1.5 rounded-full bg-blue-50 px-2 py-1 text-xs font-medium text-blue-700 dark:bg-blue-400/10 dark:text-blue-400">
                                        {{ $role->permissions_count ?? 0 }} perms
                                    </span>
                                </td>
                                <td class="px-6 py-4 text-gray-600 dark:text-gray-400">
                                    {{ $role->users_count ?? 0 }} users
                                </td>
                                <td class="px-6 py-4">
                                    @if ($role->is_active)
                                        <span class="inline-flex items-center gap-1 text-green-600 dark:text-green-400">
                                            <span class="h-1.5 w-1.5 rounded-full bg-green-600"></span> Active
                                        </span>
                                    @else
                                        <span class="inline-flex items-center gap-1 text-gray-400">
                                            <span class="h-1.5 w-1.5 rounded-full bg-gray-400"></span> Inactive
                                        </span>
                                    @endif
                                </td>

                                <td class="px-6 py-4 text-right">
                                    <div class="flex justify-end items-center gap-2">
                                        <a href="{{ route('roles.show', $role) }}"
                                            class="flex h-8 w-8 items-center justify-center rounded-lg bg-blue-50 text-blue-600 transition-colors hover:bg-blue-600 hover:text-white dark:bg-blue-500/10 dark:text-blue-400 dark:hover:bg-blue-600 dark:hover:text-white"
                                            title="View Details">
                                            <i class="bi bi-eye text-base"></i>
                                        </a>

                                        <a href="{{ route('roles.edit', $role) }}"
                                            class="flex h-8 w-8 items-center justify-center rounded-lg bg-amber-50 text-amber-600 transition-colors hover:bg-amber-600 hover:text-white dark:bg-amber-500/10 dark:text-amber-400 dark:hover:bg-amber-600 dark:hover:text-white"
                                            title="Edit Role">
                                            <i class="bi bi-pencil text-base"></i>
                                        </a>

                                        <button type="button"
                                            onclick="confirmRoleDelete('{{ route('roles.destroy', $role) }}', '{{ $role->display_name ?? $role->name }}')"
                                            class="flex h-8 w-8 items-center justify-center rounded-lg bg-red-50 text-red-600 transition-colors hover:bg-red-600 hover:text-white dark:bg-red-500/10 dark:text-red-400 dark:hover:bg-red-600 dark:hover:text-white"
                                            title="Delete Role">
                                            <i class="bi bi-trash text-base"></i>
                                        </button>

                                    </div>


                                </td>
                            </tr>
                        @empty
                        @endforelse
                    </tbody>
                </table>
            </div>

            @if ($roles->hasPages())
                <div class="border-t border-gray-100 p-6 dark:border-gray-800">
                    {{ $roles->withQueryString()->links('vendor.pagination.dashboard') }}
                </div>
            @endif
        </div>
    </div>
    <!-- Delete Role Modal -->
    <div id="deleteRoleModal" class="fixed inset-0 z-50 hidden items-center justify-center bg-black/50">
        <div class="w-full max-w-md rounded-2xl bg-white p-6 shadow-xl dark:bg-gray-900">

            <div class="flex items-center gap-3 mb-4">
                <div
                    class="flex h-10 w-10 items-center justify-center rounded-full bg-red-100 text-red-600 dark:bg-red-500/10 dark:text-red-400">
                    <i class="bi bi-exclamation-triangle text-lg"></i>
                </div>
                <h3 class="text-lg font-semibold text-gray-800 dark:text-white">
                    Delete Role
                </h3>
            </div>

            <p class="text-sm text-gray-600 dark:text-gray-400 mb-6">
                Are you sure you want to delete
                <span id="deleteRoleName" class="font-semibold text-gray-800 dark:text-white"></span>?
                <br>
                This action cannot be undone.
            </p>

            <div class="flex justify-end gap-3">
                <button type="button" onclick="closeRoleDeleteModal()"
                    class="rounded-lg border border-gray-300 px-4 py-2 text-sm font-medium text-gray-700 hover:bg-gray-50 dark:border-gray-700 dark:text-gray-300 dark:hover:bg-gray-800">
                    Cancel
                </button>

                <form id="deleteRoleForm" method="POST">
                    @csrf
                    @method('DELETE')
                    <button type="submit"
                        class="rounded-lg bg-red-600 px-4 py-2 text-sm font-medium text-white hover:bg-red-700">
                        Delete
                    </button>
                </form>
            </div>
        </div>
    </div>
@endsection

@push('scripts')
    <script>
        // Optional: Add some interactivity if needed
        document.addEventListener('DOMContentLoaded', function() {

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
    <script>
        function confirmRoleDelete(actionUrl, roleName) {
            const modal = document.getElementById('deleteRoleModal');
            const form = document.getElementById('deleteRoleForm');
            const nameSpan = document.getElementById('deleteRoleName');

            form.action = actionUrl;
            nameSpan.textContent = roleName;

            modal.classList.remove('hidden');
            modal.classList.add('flex');
        }

        function closeRoleDeleteModal() {
            const modal = document.getElementById('deleteRoleModal');
            modal.classList.add('hidden');
            modal.classList.remove('flex');
        }

        // Close modal when clicking backdrop
        document.getElementById('deleteRoleModal')?.addEventListener('click', function(e) {
            if (e.target === this) {
                closeRoleDeleteModal();
            }
        });
    </script>
@endpush
