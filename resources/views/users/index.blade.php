@extends('layouts.app')
@php
    $breadcrumbs = [
        ['label' => 'Home', 'url' => url('/')],
        ['label' => 'Users Management'], // current page
    ];
@endphp
@section('content')
    <x-common.page-breadcrumb :breadcrumbs="$breadcrumbs" />
    @include('maintenance-requests.partials.alerts')
    <div class="space-y-6">

        <div class="rounded-2xl border border-gray-200 bg-white p-5 dark:border-gray-800 dark:bg-white/[0.03]">
            <form action="{{ route('users.index') }}" method="GET" id="filterForm" class="space-y-4">

                {{-- Top Row: Search and Actions --}}
                <div class="flex flex-wrap items-center justify-between gap-4">
                    <div class="relative w-full max-w-md">
                        <input type="text" name="search" id="searchInput"
                            class="h-11 w-full rounded-lg border border-gray-300 bg-transparent px-4 pl-11 text-sm text-gray-800 focus:border-blue-500 focus:ring-2 focus:ring-blue-500/10 dark:border-gray-700 dark:bg-gray-900 dark:text-white"
                            placeholder="Search name, email..." value="{{ request('search') }}">
                        <i class="bi bi-search absolute left-4 top-1/2 -translate-y-1/2 text-gray-400"></i>
                    </div>

                    <div class="flex items-center gap-3">
                        <a href="{{ route('users.export') }}"
                            class="inline-flex items-center rounded-lg border border-gray-300 bg-white px-4 py-2.5 text-sm font-medium text-gray-700 hover:bg-gray-50 dark:border-gray-700 dark:bg-gray-800 dark:text-gray-400">
                            <i class="bi bi-download me-2"></i> Export
                        </a>
                        <button type="button" onclick="openUserModal()"
                            class="rounded-lg bg-blue-600 px-4 py-2 text-sm font-bold text-white hover:bg-blue-700">
                            <i class="bi bi-person-plus mr-2"></i> Add User
                        </button>


                    </div>
                </div>

                {{-- Bottom Row: Filters --}}
                <div class="flex flex-wrap items-center gap-6 border-t border-gray-100 pt-4 dark:border-gray-800">

                    <div class="flex items-center gap-2">
                        <label class="text-xs font-semibold uppercase tracking-wider text-gray-500">Division:</label>
                        <select name="division_id"
                            class="filter-select h-9 rounded-md border-gray-300 bg-gray-50 py-1 text-xs dark:border-gray-700 dark:bg-gray-900 dark:text-gray-300">
                            <option value="">All Divisions</option>
                            @foreach ($divisions as $division)
                                <option value="{{ $division->id }}"
                                    {{ request('division_id') == $division->id ? 'selected' : '' }}>
                                    {{ $division->name }}
                                </option>
                            @endforeach
                        </select>
                    </div>

                    <div class="flex items-center gap-2">
                        <label class="text-xs font-semibold uppercase tracking-wider text-gray-500">Cluster:</label>
                        <select name="cluster_id"
                            class="filter-select h-9 rounded-md border-gray-300 bg-gray-50 py-1 text-xs dark:border-gray-700 dark:bg-gray-900 dark:text-gray-300">
                            <option value="">All Clusters</option>
                            @foreach ($clusters as $cluster)
                                <option value="{{ $cluster->id }}"
                                    {{ request('cluster_id') == $cluster->id ? 'selected' : '' }}>
                                    {{ $cluster->name }}
                                </option>
                            @endforeach
                        </select>
                    </div>

                    <div class="flex items-center gap-2">
                        <label class="text-xs font-semibold uppercase tracking-wider text-gray-500">Role:</label>
                        <select name="role"
                            class="filter-select h-9 rounded-md border-gray-300 bg-gray-50 py-1 text-xs dark:border-gray-700 dark:bg-gray-900 dark:text-gray-300">
                            <option value="">All Roles</option>
                            @foreach ($roles as $role)
                                <option value="{{ $role->name }}" {{ request('role') == $role->name ? 'selected' : '' }}>
                                    {{ $role->name }}
                                </option>
                            @endforeach
                        </select>
                    </div>

                    @if (request()->anyFilled(['search', 'division_id', 'cluster_id', 'role']))
                        <a href="{{ route('users.index') }}"
                            class="text-xs font-medium text-red-500 hover:text-red-600 transition-colors">
                            <i class="bi bi-arrow-counterclockwise me-1"></i> Reset Filters
                        </a>
                    @endif
                </div>
            </form>
        </div>

        <div class="rounded-2xl border border-gray-200 bg-white dark:border-gray-800 dark:bg-white/[0.03]">
            <div class="overflow-x-auto">
                <x-tables.users-table :users="$users" />
            </div>

            @if ($users->hasPages())
                <div class="border-t border-gray-100 p-6 dark:border-gray-800">
                    {{ $users->withQueryString()->links('vendor.pagination.dashboard') }}
                </div>
            @endif
        </div>
    </div>
    <!-- Delete Confirmation Modal -->
    <div id="deleteModal" class="fixed inset-0 z-50 hidden items-center justify-center bg-black/50">
        <div class="w-full max-w-md rounded-2xl bg-white p-6 shadow-xl dark:bg-gray-900">

            <div class="flex items-center gap-3 mb-4">
                <div
                    class="flex h-10 w-10 items-center justify-center rounded-full bg-red-100 text-red-600 dark:bg-red-500/10 dark:text-red-400">
                    <i class="bi bi-exclamation-triangle text-lg"></i>
                </div>
                <h3 class="text-lg font-semibold text-gray-800 dark:text-white">
                    Delete User
                </h3>
            </div>

            <p class="text-sm text-gray-600 dark:text-gray-400 mb-6">
                Are you sure you want to delete
                <span id="deleteUserName" class="font-semibold text-gray-800 dark:text-white"></span>?
                <br>
                This action cannot be undone.
            </p>

            <div class="flex justify-end gap-3">
                <button type="button" onclick="closeDeleteModal()"
                    class="rounded-lg border border-gray-300 px-4 py-2 text-sm font-medium text-gray-700 hover:bg-gray-50 dark:border-gray-700 dark:text-gray-300 dark:hover:bg-gray-800">
                    Cancel
                </button>

                <form id="deleteForm" method="POST">
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
    {{-- Create User Modal --}}
    <div id="userModal"
        class="fixed inset-0 z-50 hidden bg-black/40 backdrop-blur-sm flex items-center justify-center mt-5 dark:border-white">

        <div
            class="bg-white dark:bg-gray-900 rounded-2xl w-full max-w-5xl mx-4 shadow-xl overflow-y-auto max-h-[90vh] dark:border-gray-200">

            {{-- Modal Header --}}
            <div class="flex items-center justify-between p-6 border-b dark:border-gray-800">
                <h3 id="modalTitle" class="text-lg font-bold text-gray-800 dark:text-white">
                    <i id="modalIcon" class="bi bi-person-plus mr-2"></i>
                    <span id="modalText">Create New User</span>
                </h3>

                <button onclick="closeUserModal()" class="text-gray-500 hover:text-gray-700">
                    <i class="bi bi-x-lg dark:text-white"></i>
                </button>
            </div>

            {{-- Modal Body --}}
            <div class="p-6">
                @include('users.partials._form')
            </div>
        </div>
    </div>
    @if ($errors->any())
        <script>
            document.addEventListener('DOMContentLoaded', () => {
                openUserModal();
            });
        </script>
    @endif
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

            // Debounced auto-submit for search (600ms delay)
            let typingTimer;
            searchInput.addEventListener('input', () => {
                clearTimeout(typingTimer);
                typingTimer = setTimeout(() => {
                    filterForm.submit();
                }, 600);
            });
        });
    </script>
    <script>
        function confirmDelete(actionUrl, userName) {
            const modal = document.getElementById('deleteModal');
            const form = document.getElementById('deleteForm');
            const nameSpan = document.getElementById('deleteUserName');

            form.action = actionUrl;
            nameSpan.textContent = userName;

            modal.classList.remove('hidden');
            modal.classList.add('flex');
        }

        function closeDeleteModal() {
            const modal = document.getElementById('deleteModal');
            modal.classList.add('hidden');
            modal.classList.remove('flex');
        }

        // Close on backdrop click
        document.getElementById('deleteModal')?.addEventListener('click', function(e) {
            if (e.target === this) {
                closeDeleteModal();
            }
        });
        // Auto-hide success alert after 5 seconds
        const successAlert = document.getElementById('alert-success');
        if (successAlert) {
            setTimeout(() => {
                successAlert.style.transition = 'opacity 0.5s ease';
                successAlert.style.opacity = '0';
                setTimeout(() => successAlert.remove(), 500);
            }, 5000);
        }
    </script>
    <script>
        function openUserModal() {
            const form = document.getElementById('userForm');

            form.reset();
            form.action = "{{ route('users.store') }}";
            document.getElementById('formMethod').value = 'POST';

            document.getElementById('modalText').innerText = 'Create New User';
            document.getElementById('modalIcon').className = 'bi bi-person-plus mr-2';

            document.getElementById('userModal').classList.remove('hidden');
            document.body.classList.add('overflow-hidden');
        }


        function closeUserModal() {
            document.getElementById('userModal').classList.add('hidden');
            document.body.classList.remove('overflow-hidden');
        }

        // Close on ESC
        document.addEventListener('keydown', e => {
            if (e.key === 'Escape') closeUserModal();
        });
    </script>
    <script>
        function openEditUserModal(user) {
            const modal = document.getElementById('userModal');
            const form = document.getElementById('userForm');

            // Modal title
            document.getElementById('modalText').innerText = 'Edit User';
            document.getElementById('modalIcon').className = 'bi bi-pencil-square mr-2';

            // Form action & method
            form.action = `/users/${user.id}`;
            document.getElementById('formMethod').value = 'PUT';

            // Fill inputs
            form.querySelector('[name="full_name"]').value = user.full_name ?? '';
            form.querySelector('[name="email"]').value = user.email ?? '';
            form.querySelector('[name="phone"]').value = user.phone ?? '';

            // Role
            if (user.roles?.length) {
                form.querySelector('[name="roles"]').value = user.roles[0].name;
            }

            // Assignment logic
            if (user.division_id) {
                form.querySelector('[value="division"]').checked = true;
                document.querySelector('[name="division_id"]').value = user.division_id;
            } else {
                form.querySelector('[value="cluster"]').checked = true;
                document.querySelector('[name="cluster_id"]').value = user.cluster_id;
            }

            // Trigger toggle
            document.querySelector('input[name="assign_type"]:checked')
                .dispatchEvent(new Event('change'));

            modal.classList.remove('hidden');
            document.body.classList.add('overflow-hidden');
        }
    </script>
@endpush
