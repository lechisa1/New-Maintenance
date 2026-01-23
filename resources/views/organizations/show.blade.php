@extends('layouts.app')

@section('content')
    <x-common.page-breadcrumb pageTitle="Organization Detail" :links="[['label' => 'Organizations', 'url' => route('organizations.index')], ['label' => $organization->name]]" />

    <div class="grid grid-cols-1 gap-6">
        <div class="rounded-2xl border border-gray-200 bg-white p-6 dark:border-gray-800 dark:bg-white/[0.03]">
            <div class="flex items-center justify-between">
                <div>
                    <h2 class="text-xl font-bold text-gray-800 dark:text-white">
                        <i class="bi bi-building me-2 text-blue-500"></i>
                        {{ $organization->name }}
                    </h2>
                    <p class="text-sm text-gray-500">
                        Total Clusters: {{ $organization->clusters_count }}
                    </p>
                </div>

                <a href="{{ route('organizations.index') }}"
                    class="inline-flex items-center rounded-lg border px-4 py-2 text-sm font-medium text-gray-600 hover:bg-gray-50">
                    <i class="bi bi-arrow-left me-2"></i> Back
                </a>
            </div>
        </div>

        <div class="rounded-2xl border border-gray-200 bg-white p-6 dark:border-gray-800 dark:bg-white/[0.03]">
            <div class="mb-6 flex flex-col gap-4 sm:flex-row sm:items-center sm:justify-between">
                <h3 class="text-lg font-semibold text-gray-800 dark:text-white/90">
                    <i class="bi bi-diagram-3 me-2"></i>Clusters
                </h3>

                <div class="flex flex-1 items-center justify-end gap-3">
                    <form action="{{ route('organizations.show', $organization) }}" method="GET"
                        class="relative w-full max-w-sm">
                        <span class="absolute inset-y-0 left-0 flex items-center pl-3 text-gray-400">
                            <i class="bi bi-search"></i>
                        </span>
                        <input type="text" name="search" value="{{ request('search') }}"
                            placeholder="Search clusters or chairmen..."
                            class="h-10 w-full rounded-lg border border-gray-300 pl-10 pr-4 text-sm focus:border-blue-500 focus:ring-1 focus:ring-blue-500 dark:border-gray-700 dark:bg-gray-800 dark:text-white">

                        @if (request('search'))
                            <a href="{{ route('organizations.show', $organization) }}"
                                class="absolute inset-y-0 right-0 flex items-center pr-3 text-gray-400 hover:text-red-500">
                                <i class="bi bi-x-circle-fill"></i>
                            </a>
                        @endif
                    </form>

                    <button onclick="openClusterModal()"
                        class="inline-flex items-center whitespace-nowrap rounded-lg bg-blue-500 px-4 py-2 text-sm font-medium text-white hover:bg-blue-600">
                        <i class="bi bi-plus-lg me-2"></i> Add Cluster
                    </button>
                </div>
            </div>

            <div class="overflow-x-auto">

                <table class="min-w-full border border-gray-200 rounded-lg dark:border-gray-800">
                    <thead class="bg-gray-50 dark:bg-gray-800">
                        <tr>
                            <th class="px-4 py-3 text-left text-sm font-semibold">#</th>
                            <th class="px-4 py-3 text-left text-sm font-semibold">Cluster Name</th>
                            <th class="px-4 py-3 text-left text-sm font-semibold">Chairman</th>
                            <th class="px-4 py-3 text-left text-sm font-semibold">Divisions</th>

                            <th class="px-4 py-3 text-center text-sm font-semibold">Actions</th>
                        </tr>
                    </thead>

                    <tbody class="divide-y divide-gray-200 dark:divide-gray-700">
                        @forelse ($clusters as $index => $cluster)
                            <tr class="hover:bg-gray-50 dark:hover:bg-gray-800/50">
                                <td class="px-4 py-3">{{ $clusters->firstItem() + $index }}</td>
                                <td class="px-4 py-3 font-medium text-gray-800 dark:text-white">{{ $cluster->name }}</td>
                                <td class="px-4 py-3 text-sm text-gray-600 dark:text-gray-400">
                                    {{ $cluster->chairman->full_name ?? 'Not Assigned' }}
                                </td>
                                <td class="px-4 py-3">
                                    <span class="text-sm font-medium">{{ $cluster->divisions_count }}</span>
                                    <span class="text-xs text-gray-500">divisions</span>
                                </td>
                                <td class="px-4 py-3 text-center">
                                    <div class="flex justify-center gap-5">
                                        <a href="{{ route('clusters.divisions', $cluster) }}"
                                            class="text-blue-600 hover:text-blue-800">View Details</a>

                                        <button
                                            onclick="editCluster('{{ $cluster->id }}', '{{ addslashes($cluster->name) }}', '{{ $cluster->cluster_chairman }}')"
                                            class="text-yellow-600 hover:text-yellow-800">
                                            Edit
                                        </button>

                                        <button onclick="deleteCluster('{{ $cluster->id }}')"
                                            class="text-red-600 hover:text-red-800">Delete</button>
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="4" class="px-4 py-8 text-center text-gray-500">No clusters found</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            @if ($clusters->hasPages())
                <div class="mt-6">{{ $clusters->links('vendor.pagination.dashboard') }}</div>
            @endif
        </div>
    </div>

    <div id="clusterModal" class="fixed inset-0 z-50 hidden items-center justify-center bg-black/40 p-4">
        <div class="w-full max-w-md rounded-2xl bg-white p-6 shadow-xl dark:bg-gray-900">
            <div class="mb-4 flex items-center justify-between">
                <h3 id="modalTitle" class="text-lg font-semibold text-gray-800 dark:text-white">Add Cluster</h3>
                <button onclick="closeClusterModal()" class="text-gray-400 hover:text-gray-600"><i
                        class="bi bi-x-lg"></i></button>
            </div>

            <form id="clusterForm">
                @csrf
                <input type="hidden" name="id" id="clusterId">
                <input type="hidden" name="organization_id" value="{{ $organization->id }}">

                <div class="mb-4">
                    <label class="mb-2 block text-sm font-medium text-gray-700 dark:text-gray-300">Cluster Name *</label>
                    <input type="text" name="name" id="clusterName" required
                        class="h-11 w-full rounded-lg border border-gray-300 px-4 text-sm focus:border-blue-500 dark:border-gray-700 dark:bg-gray-800 dark:text-white">
                </div>
                <div class="mb-4">
                    <label class="mb-2 block text-sm font-medium text-gray-700 dark:text-gray-300">
                        Cluster Chairman (Optional)
                    </label>
                    <select name="cluster_chairman" id="clusterChairman"
                        class="h-11 w-full rounded-lg border border-gray-300 px-4 text-sm focus:border-blue-500 dark:border-gray-700 dark:bg-gray-800 dark:text-white">
                        <option value="">-- Select Chairman --</option>
                        @foreach ($users as $user)
                            <option value="{{ $user->id }}">{{ $user->name ?? $user->full_name }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="flex justify-end gap-3 pt-4">
                    <button type="button" onclick="closeClusterModal()"
                        class="rounded-lg border px-4 py-2.5 text-sm font-medium text-gray-700">Cancel</button>
                    <button type="submit" id="submitButton"
                        class="rounded-lg bg-blue-500 px-4 py-2.5 text-sm font-medium text-white hover:bg-blue-600">Save
                        Cluster</button>
                </div>
            </form>
        </div>
    </div>

    <script>
        let isEditMode = false;
        let currentClusterId = null;

        function openClusterModal(id = null, name = '', chairmanId = '') {
            isEditMode = !!id;
            currentClusterId = id;

            document.getElementById('clusterId').value = id || '';
            document.getElementById('clusterName').value = name || '';

            // Set the dropdown value correctly
            const chairmanSelect = document.getElementById('clusterChairman');
            if (chairmanSelect) {
                chairmanSelect.value = chairmanId || '';
            }

            document.getElementById('modalTitle').innerText = isEditMode ? 'Edit Cluster' : 'Add Cluster';
            document.getElementById('submitButton').innerText = isEditMode ? 'Update Cluster' : 'Create Cluster';

            document.getElementById('clusterModal').classList.remove('hidden');
            document.getElementById('clusterModal').classList.add('flex');
        }

        // Single Edit Function
        function editCluster(id, name, chairmanId) {
            openClusterModal(id, name, chairmanId);
        }

        function closeClusterModal() {
            document.getElementById('clusterModal').classList.add('hidden');
            document.getElementById('clusterModal').classList.remove('flex');
            document.getElementById('clusterForm').reset();
        }

        async function deleteCluster(id) {
            if (!confirm('Are you sure you want to delete this cluster?')) return;

            try {
                const response = await fetch(`/api/clusters/${id}`, {
                    method: 'DELETE',
                    headers: {
                        'X-CSRF-TOKEN': '{{ csrf_token() }}',
                        'Accept': 'application/json'
                    }
                });

                if (response.ok) {
                    window.location.reload();
                } else {
                    alert('Failed to delete cluster');
                }
            } catch (error) {
                console.error('Error:', error);
            }
        }

        document.getElementById('clusterForm').addEventListener('submit', async (e) => {
            e.preventDefault();
            const submitBtn = document.getElementById('submitButton');
            submitBtn.disabled = true;

            const formData = new FormData(e.target);
            const data = Object.fromEntries(formData.entries());

            const url = isEditMode ? `/api/clusters/${currentClusterId}` : '/api/clusters';
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

                if (response.ok) {
                    window.location.reload();
                } else {
                    const error = await response.json();
                    alert(error.message || 'Error occurred');
                    submitBtn.disabled = false;
                }
            } catch (error) {
                console.error('Error:', error);
                submitBtn.disabled = false;
            }
        });
    </script>
@endsection
