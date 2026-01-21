@extends('layouts.app')

@section('content')
    <x-common.page-breadcrumb pageTitle="Organization Management" />

    <div class="grid grid-cols-1 gap-6">

        <!-- Statistics Cards -->

        <div class="mb-4 flex items-center justify-between">
            <div class="flex gap-2">
                <input type="text" id="searchInput" name="search" placeholder="Search organizations..."
                    value="{{ request('search') }}"
                    class="h-10 rounded-lg border border-gray-300 px-4 text-sm focus:border-blue-500 focus:ring-2 focus:ring-blue-500/20 dark:border-gray-700 dark:bg-gray-800 dark:text-white">

                <button onclick="applySearch()"
                    class="rounded-lg bg-blue-500 px-4 py-2 text-sm font-medium text-white hover:bg-blue-600">
                    Search
                </button>
            </div>

            <button onclick="openOrganizationModal()"
                class="inline-flex items-center rounded-lg bg-blue-500 px-4 py-2.5 text-sm font-medium text-white hover:bg-blue-600">
                <i class="bi bi-plus-lg me-2"></i> Add Organization
            </button>
        </div>

        <!-- Organizations Table -->
        <div class="rounded-2xl border border-gray-200 bg-white p-6 dark:border-gray-800 dark:bg-white/[0.03]">
            <div class="mb-6 flex items-center justify-between">
                <h3 class="text-lg font-semibold text-gray-800 dark:text-white/90">
                    <i class="bi bi-building me-2"></i>Organizations List
                </h3>



            </div>

            <div class="overflow-x-auto">
                <table class="min-w-full border border-gray-200 rounded-lg dark:border-gray-800">
                    <thead class="bg-gray-50 dark:bg-gray-800">
                        <tr>
                            <th class="px-4 py-3 text-left text-sm font-semibold">#</th>
                            <th class="px-4 py-3 text-left text-sm font-semibold">Organization Name</th>
                            <th class="px-4 py-3 text-left text-sm font-semibold">Clusters</th>
                            <th class="px-4 py-3 text-center text-sm font-semibold">Actions</th>
                        </tr>
                    </thead>

                    <tbody class="divide-y divide-gray-200 dark:divide-gray-700">
                        @forelse ($organizations as $index => $organization)
                            <tr class="hover:bg-gray-50 dark:hover:bg-gray-800/50">
                                <td class="px-4 py-3">
                                    {{ $index + 1 }}
                                </td>

                                <td class="px-4 py-3 font-medium text-gray-800 dark:text-white">
                                    {{ $organization->name }}
                                </td>

                                <td class="px-4 py-3">
                                    <span class="text-sm font-medium text-gray-700 dark:text-gray-300">
                                        {{ $organization->clusters_count }}
                                    </span>
                                    <span class="text-xs text-gray-500">clusters</span>
                                </td>

                                <td class="px-4 py-3 text-center">
                                    <div class="flex justify-center gap-5">
                                        <a href="{{ route('organizations.show', $organization) }}"
                                            class="text-blue-600 hover:text-blue-800">
                                            View Details
                                        </a>

                                        <button
                                            onclick="editOrganization('{{ $organization->id }}', '{{ $organization->name }}')"
                                            class="text-yellow-600 hover:text-yellow-800 dark:text-yellow-400">
                                            Edit
                                        </button>

                                        <button onclick="deleteOrganization('{{ $organization->id }}')"
                                            class="text-red-600 hover:text-red-800 dark:text-red-400">
                                            Delete
                                        </button>
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="4" class="px-4 py-8 text-center text-gray-500">
                                    <i class="bi bi-building-x text-4xl mb-2"></i>
                                    <p>No organizations found</p>
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            @if ($organizations->hasPages())
                <div class="mt-6">
                    {{ $organizations->links('vendor.pagination.dashboard') }}
                </div>
            @endif

        </div>
    </div>
@endsection
<!-- Add Organization Modal -->
<div id="organizationModal" class="fixed inset-0 z-50 hidden items-center justify-center bg-black/40">

    <div class="w-full max-w-md rounded-2xl bg-white p-6 shadow-lg dark:bg-gray-900">
        <div class="mb-4 flex items-center justify-between">
            <h3 id="modalTitle" class="text-lg font-semibold text-gray-800 dark:text-white">
                Add Organization
            </h3>
            <button onclick="closeOrganizationModal()" class="text-gray-400 hover:text-gray-600">
                <i class="bi bi-x-lg"></i>
            </button>
        </div>

        <form id="organizationForm">
            @csrf

            <div class="mb-4">
                <label class="mb-2 block text-sm font-medium text-gray-700 dark:text-gray-300">
                    Organization Name
                </label>
                <input type="text" name="name" required
                    class="h-11 w-full rounded-lg border border-gray-300 px-4 text-sm focus:border-blue-500 focus:ring-2 focus:ring-blue-500/20 dark:border-gray-700 dark:bg-gray-800 dark:text-white">
            </div>

            <div class="flex justify-end gap-3">
                <button type="button" onclick="closeOrganizationModal()" class="rounded-lg border px-4 py-2 text-sm">
                    Cancel
                </button>

                <button type="submit"
                    class="rounded-lg bg-blue-500 px-4 py-2 text-sm font-medium text-white hover:bg-blue-600">
                    Save
                </button>
            </div>
        </form>
    </div>
</div>
<script>
    let isEditMode = false;
    let currentOrgId = null;

    function openOrganizationModal(id = null, name = '') {
        isEditMode = !!id;
        currentOrgId = id;

        const modal = document.getElementById('organizationModal');
        const modalTitle = document.getElementById('modalTitle');
        const nameInput = document.querySelector('input[name="name"]');

        // 1. Set the Input Value (This is the "Old Data")
        nameInput.value = name;

        // 2. Update Title
        if (modalTitle) {
            modalTitle.innerText = isEditMode ? 'Edit Organization' : 'Add Organization';
        }

        // 3. Show Modal
        modal.classList.remove('hidden');
        modal.classList.add('flex');
    }

    function applySearch() {
        const searchValue = document.getElementById('searchInput').value.trim();
        const url = new URL(window.location.href);
        if (searchValue) {
            url.searchParams.set('search', searchValue);
        } else {
            url.searchParams.delete('search');
        }
        window.location.href = url.toString();
    }

    function closeOrganizationModal() {
        const modal = document.getElementById('organizationModal');
        modal.classList.add('hidden');
        modal.classList.remove('flex');

        document.getElementById('organizationForm').reset();
        isEditMode = false;
        currentOrgId = null;
    }

    // EDIT Trigger
    function editOrganization(id, name) {
        openOrganizationModal(id, name);
    }

    // DELETE Logic
    async function deleteOrganization(id) {
        if (!confirm('Are you sure you want to delete this organization? This action cannot be undone.')) return;

        try {
            const response = await fetch(`/api/organizations/${id}`, {
                method: 'DELETE',
                headers: {
                    'X-CSRF-TOKEN': document.querySelector('input[name="_token"]').value,
                    'Accept': 'application/json'
                }
            });

            if (response.ok) {
                window.location.reload();
            } else {
                alert('Failed to delete organization');
            }
        } catch (error) {
            console.error('Delete error:', error);
        }
    }

    // Updated Form Submission (Handles both POST and PUT)
    document.getElementById('organizationForm').addEventListener('submit', async (e) => {
        e.preventDefault();

        const formData = new FormData(e.target);
        const data = Object.fromEntries(formData.entries());

        // Determine URL and Method
        const url = isEditMode ? `/api/organizations/${currentOrgId}` : '/api/organizations';
        const method = isEditMode ? 'PUT' : 'POST';

        try {
            const response = await fetch(url, {
                method: method,
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': document.querySelector('input[name="_token"]').value,
                    'Accept': 'application/json'
                },
                body: JSON.stringify(data)
            });

            if (response.ok) {
                window.location.reload();
            } else {
                const errorData = await response.json();
                alert('Error: ' + (errorData.message || 'Validation failed'));
            }
        } catch (error) {
            console.error('Error:', error);
        }
    });
</script>
