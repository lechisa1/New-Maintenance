@extends('layouts.app')
@php
    $breadcrumbs = [
        ['label' => 'Home', 'url' => url('/')],
        ['label' => 'Organization Management'], // current page
    ];
@endphp



@section('content')
    <x-common.page-breadcrumb :breadcrumbs="$breadcrumbs" />
    @include('maintenance-requests.partials.alerts')
    <div class="space-y-6">
        {{-- Professional Filter Card --}}
        <div class="rounded-2xl border border-gray-200 bg-white p-5 dark:border-gray-800 dark:bg-white/[0.03]">
            <form action="{{ route('organizations.index') }}" method="GET" id="filterForm" class="space-y-4">

                {{-- Top Row: Search and Actions --}}
                <div class="flex flex-wrap items-center justify-between gap-4">
                    <div class="relative w-full max-w-md">
                        <input type="text" name="search" id="searchInput"
                            class="h-11 w-full rounded-lg border border-gray-300 bg-transparent px-4 pl-11 text-sm text-gray-800 focus:border-blue-500 focus:ring-2 focus:ring-blue-500/10 dark:border-gray-700 dark:bg-gray-900 dark:text-white"
                            placeholder="Search organizations..." value="{{ request('search') }}">
                        <i class="bi bi-search absolute left-4 top-1/2 -translate-y-1/2 text-gray-400"></i>
                    </div>

                    <div class="flex items-center gap-3">
                        @if (request()->filled('search'))
                            <a href="{{ route('organizations.index') }}"
                                class="text-xs font-medium text-red-500 hover:text-red-600 transition-colors mr-2">
                                <i class="bi bi-arrow-counterclockwise me-1"></i> Reset Search
                            </a>
                        @endif
                        <button type="button" onclick="openOrganizationModal()"
                            class="inline-flex items-center rounded-lg bg-blue-600 px-4 py-2.5 text-sm font-medium text-white hover:bg-blue-700 transition shadow-sm">
                            <i class="bi bi-plus-lg me-2"></i> Add Organization
                        </button>
                    </div>
                </div>
            </form>
        </div>

        {{-- Table Container --}}
        <div class="rounded-2xl border border-gray-200 bg-white dark:border-gray-800 dark:bg-white/[0.03]">
            <div class="overflow-x-auto">
                <table class="min-w-full text-left">
                    <thead class="bg-gray-50/50 dark:bg-gray-800/50 ">
                        <tr class="border-b border-gray-200 dark:border-gray-700">
                            <th class="px-6 py-4 text-xs font-bold uppercase text-gray-500 dark:text-white">#</th>
                            <th class="px-6 py-4 text-xs font-bold uppercase text-gray-500 dark:text-white">Organization
                                Name</th>
                            <th class="px-6 py-4 text-xs font-bold uppercase text-gray-500 dark:text-white">Clusters</th>
                            <th class="px-6 py-4 text-xs font-bold uppercase text-gray-500 text-right dark:text-white">
                                Actions</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-100 dark:divide-gray-800">
                        @forelse ($organizations as $index => $organization)
                            <tr class="hover:bg-gray-50/50 dark:hover:bg-white/[0.02]">
                                <td class="px-6 py-4 text-sm text-gray-500">
                                    {{ ($organizations->currentPage() - 1) * $organizations->perPage() + $index + 1 }}
                                </td>
                                <td class="px-6 py-4">
                                    <span
                                        class="font-medium text-gray-800 dark:text-gray-200">{{ $organization->name }}</span>
                                </td>
                                <td class="px-6 py-4 dark:text-white">
                                    <div class="flex items-center gap-2">
                                        <span
                                            class="inline-flex items-center rounded-md bg-blue-50 px-2 py-1 text-xs font-medium text-blue-700 ring-1 ring-inset ring-blue-700/10 dark:bg-blue-400/10 dark:text-blue-400">
                                            {{ $organization->clusters_count }}
                                        </span>
                                        <span class="text-sm text-gray-500 dark:text-white">Clusters</span>
                                    </div>
                                </td>
                                <td class="px-6 py-4 text-right">
                                    <div class="flex justify-end gap-2">
                                        <a href="{{ route('organizations.show', $organization) }}"
                                            class="p-2 text-blue-500 hover:bg-blue-50 rounded-lg transition dark:hover:bg-blue-500/10"
                                            title="View Details">
                                            <i class="bi bi-eye"></i>
                                        </a>
                                        <button type="button"
                                            onclick="editOrganization('{{ $organization->id }}', '{{ $organization->name }}')"
                                            class="p-2 text-amber-500 hover:bg-amber-50 rounded-lg transition dark:hover:bg-amber-500/10"
                                            title="Edit">
                                            <i class="bi bi-pencil"></i>
                                        </button>
                                        <button type="button"
                                            onclick="confirmDelete('{{ $organization->id }}', '{{ $organization->name }}')"
                                            class="p-2 text-red-500 hover:bg-red-50 rounded-lg transition dark:hover:bg-red-500/10"
                                            title="Delete">
                                            <i class="bi bi-trash"></i>
                                        </button>
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="4" class="px-6 py-12 text-center text-gray-500 dark:text-gray-400">
                                    <i class="bi bi-building-x text-4xl mb-3 block opacity-20"></i>
                                    No organizations found matching your search.
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            @if ($organizations->hasPages())
                <div class="border-t border-gray-100 p-6 dark:border-gray-800">
                    {{ $organizations->withQueryString()->links('vendor.pagination.dashboard') }}
                </div>
            @endif
        </div>
    </div>

    {{-- Organization Add/Edit Modal --}}
    <div id="organizationModal" class="fixed inset-0 z-50 hidden items-center justify-center bg-black/50">
        <div class="w-full max-w-md rounded-2xl bg-white p-6 shadow-xl dark:bg-gray-900">
            <div class="mb-6 flex items-center justify-between">
                <h3 id="modalTitle" class="text-lg font-semibold text-gray-800 dark:text-white">Add Organization</h3>
                <button onclick="closeOrganizationModal()"
                    class="text-gray-400 hover:text-gray-600 dark:hover:text-gray-300">
                    <i class="bi bi-x-lg"></i>
                </button>
            </div>

            <form id="organizationForm">
                @csrf
                <div class="mb-6">
                    <label class="mb-2 block text-sm font-medium text-gray-700 dark:text-gray-300">
                        Organization Name
                    </label>
                    <input type="text" name="name" id="orgNameInput" required
                        class="h-11 w-full rounded-lg border border-gray-300 bg-transparent px-4 text-sm focus:border-blue-500 focus:ring-2 focus:ring-blue-500/10 dark:border-gray-700 dark:bg-gray-800 dark:text-white"
                        placeholder="Enter full organization name">
                </div>

                <div class="flex justify-end gap-3">
                    <button type="button" onclick="closeOrganizationModal()"
                        class="rounded-lg border border-gray-300 px-4 py-2.5 text-sm font-medium text-gray-700 hover:bg-gray-50 dark:border-gray-700 dark:text-gray-300 dark:hover:bg-gray-800">
                        Cancel
                    </button>
                    <button type="submit"
                        class="rounded-lg bg-blue-600 px-6 py-2.5 text-sm font-medium text-white hover:bg-blue-700 shadow-sm transition">
                        Save Organization
                    </button>
                </div>
            </form>
        </div>
    </div>

    {{-- Standardized Delete Confirmation Modal --}}
    <div id="deleteModal" class="fixed inset-0 z-50 hidden items-center justify-center bg-black/50">
        <div class="w-full max-w-md rounded-2xl bg-white p-6 shadow-xl dark:bg-gray-900">
            <div class="flex items-center gap-3 mb-4">
                <div
                    class="flex h-10 w-10 items-center justify-center rounded-full bg-red-100 text-red-600 dark:bg-red-500/10 dark:text-red-400">
                    <i class="bi bi-exclamation-triangle text-lg"></i>
                </div>
                <h3 class="text-lg font-semibold text-gray-800 dark:text-white">Delete Organization</h3>
            </div>
            <p class="text-sm text-gray-600 dark:text-gray-400 mb-6">
                Are you sure you want to delete <span id="deleteOrgName"
                    class="font-semibold text-gray-800 dark:text-white"></span>? This action cannot be undone and may
                affect
                related clusters.
            </p>
            <div class="flex justify-end gap-3">
                <button type="button" onclick="closeDeleteModal()"
                    class="rounded-lg border border-gray-300 px-4 py-2 text-sm font-medium text-gray-700 hover:bg-gray-50 dark:border-gray-700 dark:text-gray-300 dark:hover:bg-gray-800">Cancel</button>
                <button type="button" id="confirmDeleteBtn"
                    class="rounded-lg bg-red-600 px-4 py-2 text-sm font-medium text-white hover:bg-red-700">Delete
                    Permanently</button>
            </div>
        </div>
    </div>
@endsection

@push('scripts')
    <script>
        let isEditMode = false;
        let currentOrgId = null;

        document.addEventListener('DOMContentLoaded', function() {
            const filterForm = document.getElementById('filterForm');
            const searchInput = document.getElementById('searchInput');

            // Search auto-submit (Debounced 600ms)
            let typingTimer;
            searchInput.addEventListener('input', () => {
                clearTimeout(typingTimer);
                typingTimer = setTimeout(() => {
                    filterForm.submit();
                }, 600);
            });
        });

        function openOrganizationModal(id = null, name = '') {
            isEditMode = !!id;
            currentOrgId = id;
            const modal = document.getElementById('organizationModal');
            const modalTitle = document.getElementById('modalTitle');
            const nameInput = document.getElementById('orgNameInput');

            nameInput.value = name;
            modalTitle.innerText = isEditMode ? 'Edit Organization' : 'Add Organization';

            modal.classList.remove('hidden');
            modal.classList.add('flex');
        }

        function closeOrganizationModal() {
            const modal = document.getElementById('organizationModal');
            modal.classList.add('hidden');
            modal.classList.remove('flex');
            document.getElementById('organizationForm').reset();
            isEditMode = false;
            currentOrgId = null;
        }

        function editOrganization(id, name) {
            openOrganizationModal(id, name);
        }

        // Standardized Delete Modal Logic
        function confirmDelete(id, name) {
            currentOrgId = id;
            const modal = document.getElementById('deleteModal');
            document.getElementById('deleteOrgName').textContent = name;
            modal.classList.remove('hidden');
            modal.classList.add('flex');

            document.getElementById('confirmDeleteBtn').onclick = async () => {
                try {
                    const response = await fetch(`/api/organizations/${currentOrgId}`, {
                        method: 'DELETE',
                        headers: {
                            'X-CSRF-TOKEN': '{{ csrf_token() }}',
                            'Accept': 'application/json'
                        }
                    });
                    if (response.ok) window.location.reload();
                    else alert('Failed to delete organization');
                } catch (error) {
                    console.error('Delete error:', error);
                }
            };
        }

        function closeDeleteModal() {
            const modal = document.getElementById('deleteModal');
            modal.classList.add('hidden');
            modal.classList.remove('flex');
        }

        // Form Submission
        document.getElementById('organizationForm').addEventListener('submit', async (e) => {
            e.preventDefault();
            const formData = new FormData(e.target);
            const data = Object.fromEntries(formData.entries());

            const url = isEditMode ? `/api/organizations/${currentOrgId}` : '/api/organizations';
            const method = isEditMode ? 'PUT' : 'POST';

            try {
                const response = await fetch(url, {
                    method: method,
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': '{{ csrf_token() }}',
                        'Accept': 'application/json'
                    },
                    body: JSON.stringify(data)
                });

                if (response.ok) window.location.reload();
                else {
                    const errorData = await response.json();
                    alert('Error: ' + (errorData.message || 'Action failed'));
                }
            } catch (error) {
                console.error('Error:', error);
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
@endpush
