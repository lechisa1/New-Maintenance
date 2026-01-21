@extends('layouts.app')

@section('content')
    <x-common.page-breadcrumb pageTitle="Clusters - {{ $organization->name }}" />

    <div class="max-w-7xl mx-auto">
        <!-- Header with back button -->
        <div class="mb-6">
            <div class="flex items-center justify-between">
                <div class="flex items-center gap-4">
                    <a href="{{ route('organizations.index') }}" class="text-gray-600 dark:text-gray-400 hover:text-blue-600">
                        <i class="bi bi-arrow-left text-lg"></i>
                    </a>
                    <div>
                        <h1 class="text-2xl font-bold text-gray-900 dark:text-white">{{ $organization->name }}</h1>
                        <p class="text-gray-600 dark:text-gray-400 mt-1">Manage clusters in this organization</p>
                    </div>
                </div>
                <button @click="showClusterModal()" class="bg-blue-600 text-white px-4 py-2 rounded-lg hover:bg-blue-700">
                    <i class="bi bi-plus-lg mr-2"></i>New Cluster
                </button>
            </div>
        </div>

        <!-- Stats Cards -->
        <div class="grid grid-cols-1 md:grid-cols-3 gap-4 mb-6">
            <div class="bg-white dark:bg-gray-800 p-4 rounded-xl border border-gray-200 dark:border-gray-700">
                <div class="flex items-center">
                    <div class="bg-blue-100 dark:bg-blue-900 p-2 rounded-lg mr-3">
                        <i class="bi bi-diagram-3 text-blue-600 dark:text-blue-400"></i>
                    </div>
                    <div>
                        <p class="text-sm text-gray-500 dark:text-gray-400">Total Clusters</p>
                        <p x-text="clusters.length" class="text-2xl font-bold text-gray-900 dark:text-white"></p>
                    </div>
                </div>
            </div>

            <div class="bg-white dark:bg-gray-800 p-4 rounded-xl border border-gray-200 dark:border-gray-700">
                <div class="flex items-center">
                    <div class="bg-green-100 dark:bg-green-900 p-2 rounded-lg mr-3">
                        <i class="bi bi-layers text-green-600 dark:text-green-400"></i>
                    </div>
                    <div>
                        <p class="text-sm text-gray-500 dark:text-gray-400">Total Divisions</p>
                        <p x-text="totalDivisions" class="text-2xl font-bold text-gray-900 dark:text-white"></p>
                    </div>
                </div>
            </div>

            <div class="bg-white dark:bg-gray-800 p-4 rounded-xl border border-gray-200 dark:border-gray-700">
                <div class="flex items-center">
                    <div class="bg-purple-100 dark:bg-purple-900 p-2 rounded-lg mr-3">
                        <i class="bi bi-people text-purple-600 dark:text-purple-400"></i>
                    </div>
                    <div>
                        <p class="text-sm text-gray-500 dark:text-gray-400">Chairmen Assigned</p>
                        <p x-text="chairmenCount" class="text-2xl font-bold text-gray-900 dark:text-white"></p>
                    </div>
                </div>
            </div>
        </div>

        <!-- Clusters Table -->
        <div class="bg-white dark:bg-gray-800 rounded-xl border border-gray-200 dark:border-gray-700 overflow-hidden">
            <table class="min-w-full">
                <thead class="bg-gray-50 dark:bg-gray-900">
                    <tr>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase">Name
                        </th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase">
                            Chairman</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase">
                            Divisions</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase">
                            Actions</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-200 dark:divide-gray-700">
                    <template x-for="cluster in clusters" :key="cluster.id">
                        <tr class="hover:bg-gray-50 dark:hover:bg-gray-700">
                            <td class="px-6 py-4">
                                <a :href="`{{ route('clusters.divisions', '') }}/${cluster.id}`"
                                    class="text-blue-600 dark:text-blue-400 hover:underline font-medium">
                                    <span x-text="cluster.name"></span>
                                </a>
                            </td>
                            <td class="px-6 py-4">
                                <template x-if="cluster.chairman">
                                    <span x-text="cluster.chairman.name" class="text-gray-900 dark:text-white"></span>
                                </template>
                                <template x-if="!cluster.chairman">
                                    <span class="text-gray-400">Not assigned</span>
                                </template>
                            </td>
                            <td class="px-6 py-4">
                                <a :href="`{{ route('clusters.divisions', '') }}/${cluster.id}`"
                                    class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-green-100 text-green-800 dark:bg-green-900 dark:text-green-300">
                                    <span x-text="cluster.divisions_count || 0"></span> divisions
                                </a>
                            </td>
                            <td class="px-6 py-4">
                                <div class="flex items-center gap-2">
                                    <a :href="`{{ route('clusters.divisions', '') }}/${cluster.id}`"
                                        class="text-blue-600 dark:text-blue-400 hover:text-blue-800">
                                        <i class="bi bi-eye"></i>
                                    </a>
                                    <button @click="editCluster(cluster)"
                                        class="text-gray-600 dark:text-gray-400 hover:text-blue-600">
                                        <i class="bi bi-pencil"></i>
                                    </button>
                                    <button @click="deleteCluster(cluster)"
                                        class="text-gray-600 dark:text-gray-400 hover:text-red-600">
                                        <i class="bi bi-trash"></i>
                                    </button>
                                </div>
                            </td>
                        </tr>
                    </template>
                </tbody>
            </table>

            <!-- Empty State -->
            <div x-show="clusters.length === 0" class="text-center py-12">
                <i class="bi bi-diagram-3 text-4xl text-gray-300 dark:text-gray-600"></i>
                <p class="text-gray-500 dark:text-gray-400 mt-3">No clusters in this organization</p>
                <button @click="showClusterModal()"
                    class="mt-4 bg-blue-600 text-white px-4 py-2 rounded-lg hover:bg-blue-700">
                    Create First Cluster
                </button>
            </div>
        </div>
    </div>

    <!-- Cluster Modal -->
    <div x-data="clusterManager()" x-init="init()">
        <!-- Organization Modal (for organizations.index) -->
        <div x-show="showOrgModalFlag" x-cloak class="fixed inset-0 z-50 flex items-center justify-center bg-black/50 p-4">
            <div class="w-full max-w-md rounded-xl bg-white dark:bg-gray-800 p-6" @click.away="closeModals()">
                <div class="flex items-center justify-between mb-4">
                    <h3 class="text-lg font-medium text-gray-900 dark:text-white">
                        <template x-if="editingOrg">Edit Organization</template>
                        <template x-if="!editingOrg">Create Organization</template>
                    </h3>
                    <button @click="closeModals()" class="text-gray-400 hover:text-gray-600">
                        <i class="bi bi-x-lg"></i>
                    </button>
                </div>

                <form @submit.prevent="saveOrg()" class="space-y-4">
                    <div>
                        <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">
                            Organization Name <span class="text-red-500">*</span>
                        </label>
                        <input type="text" x-model="currentOrg.name" required
                            class="w-full px-3 py-2 border border-gray-300 dark:border-gray-700 rounded-lg bg-white dark:bg-gray-900">
                    </div>

                    <div class="flex justify-end gap-3 pt-4">
                        <button type="button" @click="closeModals()"
                            class="px-4 py-2 border border-gray-300 dark:border-gray-600 rounded-lg text-gray-700 dark:text-gray-300 hover:bg-gray-50 dark:hover:bg-gray-700">
                            Cancel
                        </button>
                        <button type="submit" class="px-4 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700">
                            <template x-if="editingOrg">Update</template>
                            <template x-if="!editingOrg">Create</template>
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <script>
        function clusterManager() {
            return {
                organization: @json($organization),
                clusters: [],
                users: @json($users),
                showClusterModalFlag: false,
                editingCluster: null,
                currentCluster: {
                    name: '',
                    cluster_chairman: ''
                },

                get totalDivisions() {
                    return this.clusters.reduce((sum, cluster) => sum + (cluster.divisions_count || 0), 0);
                },

                get chairmenCount() {
                    return this.clusters.filter(c => c.chairman).length;
                },

                async init() {
                    await this.loadClusters();
                },

                async loadClusters() {
                    try {
                        const response = await fetch(`/api/organizations/${this.organization.id}/clusters`);
                        this.clusters = await response.json();
                    } catch (error) {
                        console.error('Error loading clusters:', error);
                    }
                },

                showClusterModal(cluster = null) {
                    this.editingCluster = cluster;
                    this.currentCluster = cluster ? {
                        ...cluster
                    } : {
                        name: '',
                        cluster_chairman: ''
                    };
                    this.showClusterModalFlag = true;
                },

                closeModals() {
                    this.showClusterModalFlag = false;
                    this.editingCluster = null;
                },

                async saveCluster() {
                    try {
                        this.currentCluster.organization_id = this.organization.id;

                        const url = this.editingCluster ?
                            `/api/clusters/${this.editingCluster.id}` :
                            '/api/clusters';

                        const method = this.editingCluster ? 'PUT' : 'POST';

                        const response = await fetch(url, {
                            method,
                            headers: {
                                'Content-Type': 'application/json',
                                'X-CSRF-TOKEN': '{{ csrf_token() }}'
                            },
                            body: JSON.stringify(this.currentCluster)
                        });

                        if (response.ok) {
                            await this.loadClusters();
                            this.closeModals();
                        }
                    } catch (error) {
                        console.error('Error:', error);
                    }
                },

                async deleteCluster(cluster) {
                    if (confirm(`Delete cluster "${cluster.name}"?`)) {
                        try {
                            await fetch(`/api/clusters/${cluster.id}`, {
                                method: 'DELETE',
                                headers: {
                                    'X-CSRF-TOKEN': '{{ csrf_token() }}'
                                }
                            });
                            await this.loadClusters();
                        } catch (error) {
                            console.error('Error:', error);
                        }
                    }
                },

                editCluster(cluster) {
                    this.showClusterModal(cluster);
                }
            };
        }
    </script>
@endsection
