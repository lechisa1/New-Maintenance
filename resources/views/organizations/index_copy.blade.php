@extends('layouts.app')

@section('content')
    <x-common.page-breadcrumb pageTitle="Organization Structure" />

    <div x-data="treeManager()" x-init="init()" class="max-w-7xl mx-auto">
        <!-- Header -->
        <div class="mb-6">
            <h1 class="text-2xl font-bold text-gray-900 dark:text-white mb-2">Organization Structure</h1>
            <p class="text-gray-600 dark:text-gray-400">Click on items to drill down through the hierarchy</p>
        </div>

        <!-- Main Container -->
        <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
            <!-- Left: Organizations -->
            <div class="bg-white dark:bg-gray-800 rounded-xl border border-gray-200 dark:border-gray-700 p-5">
                <div class="flex items-center justify-between mb-4">
                    <h2 class="text-lg font-semibold text-gray-900 dark:text-white">Organizations</h2>
                    <button @click="showOrgModal()"
                        class="text-sm bg-blue-600 text-white px-3 py-1.5 rounded-lg hover:bg-blue-700">
                        <i class="bi bi-plus-lg mr-1"></i>New
                    </button>
                </div>

                <div class="space-y-2 max-h-[500px] overflow-y-auto">
                    <template x-for="org in organizations" :key="org.id">
                        <div @click="selectOrg(org)"
                            :class="`p-3 rounded-lg border cursor-pointer transition-all ${selectedOrg?.id === org.id ? 'border-blue-500 bg-blue-50 dark:bg-blue-900/20' : 'border-gray-200 dark:border-gray-700 hover:bg-gray-50 dark:hover:bg-gray-700'}`">
                            <div class="flex items-center justify-between">
                                <div class="flex items-center">
                                    <i class="bi bi-building text-gray-500 mr-3"></i>
                                    <div>
                                        <h4 x-text="org.name" class="font-medium text-gray-900 dark:text-white"></h4>
                                        <p class="text-xs text-gray-500 mt-1">
                                            <span x-text="org.clusters_count || 0"></span> clusters •
                                            <span x-text="org.divisions_count || 0"></span> divisions
                                        </p>
                                    </div>
                                </div>
                                <div class="flex gap-1" @click.stop>
                                    <button @click="editOrg(org)" class="p-1 text-gray-400 hover:text-blue-600">
                                        <i class="bi bi-pencil"></i>
                                    </button>
                                    <button @click="deleteOrg(org)" class="p-1 text-gray-400 hover:text-red-600">
                                        <i class="bi bi-trash"></i>
                                    </button>
                                </div>
                            </div>
                        </div>
                    </template>

                    <div x-show="organizations.length === 0" class="text-center py-8">
                        <i class="bi bi-building text-3xl text-gray-300 dark:text-gray-600"></i>
                        <p class="text-gray-500 dark:text-gray-400 mt-2">No organizations yet</p>
                        <button @click="showOrgModal()"
                            class="mt-3 bg-blue-600 text-white px-3 py-1.5 rounded-lg text-sm hover:bg-blue-700">
                            Create First Organization
                        </button>
                    </div>
                </div>
            </div>

            <!-- Middle: Clusters -->
            <div class="bg-white dark:bg-gray-800 rounded-xl border border-gray-200 dark:border-gray-700 p-5">
                <div class="flex items-center justify-between mb-4">
                    <div>
                        <h2 class="text-lg font-semibold text-gray-900 dark:text-white">Clusters</h2>
                        <p x-show="selectedOrg" class="text-sm text-gray-500" x-text="'in ' + selectedOrg.name"></p>
                        <p x-show="!selectedOrg" class="text-sm text-gray-500">Select an organization first</p>
                    </div>
                    <button @click="showClusterModal()" :disabled="!selectedOrg"
                        :class="`text-sm px-3 py-1.5 rounded-lg ${selectedOrg ? 'bg-blue-600 text-white hover:bg-blue-700' : 'bg-gray-200 dark:bg-gray-700 text-gray-400 cursor-not-allowed'}`">
                        <i class="bi bi-plus-lg mr-1"></i>New
                    </button>
                </div>

                <div class="space-y-2 max-h-[500px] overflow-y-auto">
                    <template x-for="cluster in clusters" :key="cluster.id">
                        <div @click="selectCluster(cluster)"
                            :class="`p-3 rounded-lg border cursor-pointer transition-all ${selectedCluster?.id === cluster.id ? 'border-blue-500 bg-blue-50 dark:bg-blue-900/20' : 'border-gray-200 dark:border-gray-700 hover:bg-gray-50 dark:hover:bg-gray-700'}`">
                            <div class="flex items-center justify-between">
                                <div class="flex items-center">
                                    <i class="bi bi-diagram-3 text-gray-500 mr-3"></i>
                                    <div>
                                        <h4 x-text="cluster.name" class="font-medium text-gray-900 dark:text-white"></h4>
                                        <p class="text-xs text-gray-500 mt-1">
                                            <span x-text="cluster.divisions_count || 0"></span> divisions
                                        </p>
                                    </div>
                                </div>
                                <div class="flex gap-1" @click.stop>
                                    <button @click="editCluster(cluster)" class="p-1 text-gray-400 hover:text-blue-600">
                                        <i class="bi bi-pencil"></i>
                                    </button>
                                    <button @click="deleteCluster(cluster)" class="p-1 text-gray-400 hover:text-red-600">
                                        <i class="bi bi-trash"></i>
                                    </button>
                                </div>
                            </div>
                        </div>
                    </template>

                    <div x-show="!selectedOrg" class="text-center py-8">
                        <i class="bi bi-arrow-left text-3xl text-gray-300 dark:text-gray-600"></i>
                        <p class="text-gray-500 dark:text-gray-400 mt-2">Select an organization to view clusters</p>
                    </div>

                    <div x-show="selectedOrg && clusters.length === 0" class="text-center py-8">
                        <i class="bi bi-diagram-3 text-3xl text-gray-300 dark:text-gray-600"></i>
                        <p class="text-gray-500 dark:text-gray-400 mt-2">No clusters in this organization</p>
                        <button @click="showClusterModal()"
                            class="mt-3 bg-blue-600 text-white px-3 py-1.5 rounded-lg text-sm hover:bg-blue-700">
                            Create First Cluster
                        </button>
                    </div>
                </div>
            </div>

            <!-- Right: Divisions -->
            <div class="bg-white dark:bg-gray-800 rounded-xl border border-gray-200 dark:border-gray-700 p-5">
                <div class="flex items-center justify-between mb-4">
                    <div>
                        <h2 class="text-lg font-semibold text-gray-900 dark:text-white">Divisions</h2>
                        <template x-if="selectedCluster">
                            <p class="text-sm text-gray-500" x-text="'in ' + selectedCluster.name"></p>
                        </template>
                        <template x-if="!selectedCluster">
                            <p class="text-sm text-gray-500">Select a cluster first</p>
                        </template>
                    </div>
                    <button @click="showDivisionModal()" :disabled="!selectedCluster"
                        :class="`text-sm px-3 py-1.5 rounded-lg ${selectedCluster ? 'bg-blue-600 text-white hover:bg-blue-700' : 'bg-gray-200 dark:bg-gray-700 text-gray-400 cursor-not-allowed'}`">
                        <i class="bi bi-plus-lg mr-1"></i>New
                    </button>
                </div>

                <div class="space-y-2 max-h-[500px] overflow-y-auto">
                    <template x-for="division in divisions" :key="division.id">
                        <div
                            class="p-3 rounded-lg border border-gray-200 dark:border-gray-700 hover:bg-gray-50 dark:hover:bg-gray-700">
                            <div class="flex items-center justify-between">
                                <div class="flex items-center">
                                    <i class="bi bi-layers text-gray-500 mr-3"></i>
                                    <div>
                                        <h4 x-text="division.name" class="font-medium text-gray-900 dark:text-white"></h4>
                                        <template x-if="division.chairman">
                                            <p class="text-xs text-gray-500 mt-1"
                                                x-text="'Chairman: ' + division.chairman.name"></p>
                                        </template>
                                    </div>
                                </div>
                                <div class="flex gap-1">
                                    <button @click="editDivision(division)" class="p-1 text-gray-400 hover:text-blue-600">
                                        <i class="bi bi-pencil"></i>
                                    </button>
                                    <button @click="deleteDivision(division)"
                                        class="p-1 text-gray-400 hover:text-red-600">
                                        <i class="bi bi-trash"></i>
                                    </button>
                                </div>
                            </div>
                        </div>
                    </template>

                    <div x-show="!selectedCluster" class="text-center py-8">
                        <i class="bi bi-arrow-left text-3xl text-gray-300 dark:text-gray-600"></i>
                        <p class="text-gray-500 dark:text-gray-400 mt-2">Select a cluster to view divisions</p>
                    </div>

                    <div x-show="selectedCluster && divisions.length === 0" class="text-center py-8">
                        <i class="bi bi-layers text-3xl text-gray-300 dark:text-gray-600"></i>
                        <p class="text-gray-500 dark:text-gray-400 mt-2">No divisions in this cluster</p>
                        <button @click="showDivisionModal()"
                            class="mt-3 bg-blue-600 text-white px-3 py-1.5 rounded-lg text-sm hover:bg-blue-700">
                            Create First Division
                        </button>
                    </div>
                </div>
            </div>
        </div>

        <!-- Breadcrumb Navigation -->
        <div class="mt-6 flex items-center text-sm text-gray-600 dark:text-gray-400">
            <button @click="resetSelection()" class="flex items-center hover:text-blue-600">
                <i class="bi bi-house mr-1"></i>
                <span>All Organizations</span>
            </button>

            <template x-if="selectedOrg">
                <div class="flex items-center">
                    <i class="bi bi-chevron-right mx-2"></i>
                    <span x-text="selectedOrg.name" class="font-medium"></span>
                </div>
            </template>

            <template x-if="selectedCluster">
                <div class="flex items-center">
                    <i class="bi bi-chevron-right mx-2"></i>
                    <span x-text="selectedCluster.name" class="font-medium"></span>
                </div>
            </template>
        </div>

        <!-- Modals -->
        <!-- Organization Modal -->
        <div x-show="showOrgModalFlag" x-cloak
            class="fixed inset-0 z-50 flex items-center justify-center bg-black/50 p-4">
            <div class="w-full max-w-md rounded-xl bg-white dark:bg-gray-800 p-6" @click.away="closeModals()">
                <div class="flex items-center justify-between mb-4">
                    <h3 class="text-lg font-medium text-gray-900 dark:text-white">
                        <span x-text="editingOrg ? 'Edit Organization' : 'Create Organization'"></span>
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
                            <span x-text="editingOrg ? 'Update' : 'Create'"></span>
                        </button>
                    </div>
                </form>
            </div>
        </div>

        <!-- Cluster Modal -->
        <div x-show="showClusterModalFlag" x-cloak
            class="fixed inset-0 z-50 flex items-center justify-center bg-black/50 p-4">
            <div class="w-full max-w-md rounded-xl bg-white dark:bg-gray-800 p-6" @click.away="closeModals()">
                <div class="flex items-center justify-between mb-4">
                    <h3 class="text-lg font-medium text-gray-900 dark:text-white">
                        <span x-text="editingCluster ? 'Edit Cluster' : 'Create Cluster'"></span>
                    </h3>
                    <button @click="closeModals()" class="text-gray-400 hover:text-gray-600">
                        <i class="bi bi-x-lg"></i>
                    </button>
                </div>

                <form @submit.prevent="saveCluster()" class="space-y-4">
                    <div>
                        <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">
                            Organization
                        </label>
                        <div
                            class="px-3 py-2 bg-gray-50 dark:bg-gray-900 rounded-lg border border-gray-200 dark:border-gray-700">
                            <span x-text="selectedOrg?.name || 'Not selected'"
                                class="text-gray-900 dark:text-white"></span>
                        </div>
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">
                            Cluster Name <span class="text-red-500">*</span>
                        </label>
                        <input type="text" x-model="currentCluster.name" required
                            class="w-full px-3 py-2 border border-gray-300 dark:border-gray-700 rounded-lg bg-white dark:bg-gray-900">
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">
                            Cluster Chairman
                        </label>
                        <select x-model="currentCluster.cluster_chairman"
                            class="w-full px-3 py-2 border border-gray-300 dark:border-gray-700 rounded-lg bg-white dark:bg-gray-900">
                            <option value="">Select Chairman</option>
                            <template x-for="user in users" :key="user.id">
                                <option :value="user.id" x-text="user.name"></option>
                            </template>
                        </select>
                    </div>

                    <div class="flex justify-end gap-3 pt-4">
                        <button type="button" @click="closeModals()"
                            class="px-4 py-2 border border-gray-300 dark:border-gray-600 rounded-lg text-gray-700 dark:text-gray-300 hover:bg-gray-50 dark:hover:bg-gray-700">
                            Cancel
                        </button>
                        <button type="submit" class="px-4 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700">
                            <span x-text="editingCluster ? 'Update' : 'Create'"></span>
                        </button>
                    </div>
                </form>
            </div>
        </div>

        <!-- Division Modal -->
        <div x-show="showDivisionModalFlag" x-cloak
            class="fixed inset-0 z-50 flex items-center justify-center bg-black/50 p-4">
            <div class="w-full max-w-md rounded-xl bg-white dark:bg-gray-800 p-6" @click.away="closeModals()">
                <div class="flex items-center justify-between mb-4">
                    <h3 class="text-lg font-medium text-gray-900 dark:text-white">
                        <span x-text="editingDivision ? 'Edit Division' : 'Create Division'"></span>
                    </h3>
                    <button @click="closeModals()" class="text-gray-400 hover:text-gray-600">
                        <i class="bi bi-x-lg"></i>
                    </button>
                </div>

                <form @submit.prevent="saveDivision()" class="space-y-4">
                    <div class="grid grid-cols-2 gap-4">
                        <div>
                            <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">
                                Organization
                            </label>
                            <div
                                class="px-3 py-2 bg-gray-50 dark:bg-gray-900 rounded-lg border border-gray-200 dark:border-gray-700">
                                <span x-text="selectedOrg?.name || 'Not selected'"
                                    class="text-gray-900 dark:text-white"></span>
                            </div>
                        </div>

                        <div>
                            <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">
                                Cluster <span class="text-red-500">*</span>
                            </label>
                            <select x-model="currentDivision.cluster_id" required
                                class="w-full px-3 py-2 border border-gray-300 dark:border-gray-700 rounded-lg bg-white dark:bg-gray-900">
                                <option value="">Select Cluster</option>
                                <template x-for="cluster in clusters" :key="cluster.id">
                                    <option :value="cluster.id" x-text="cluster.name"></option>
                                </template>
                            </select>
                        </div>
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">
                            Division Name <span class="text-red-500">*</span>
                        </label>
                        <input type="text" x-model="currentDivision.name" required
                            class="w-full px-3 py-2 border border-gray-300 dark:border-gray-700 rounded-lg bg-white dark:bg-gray-900">
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">
                            Division Chairman
                        </label>
                        <select x-model="currentDivision.division_chairman"
                            class="w-full px-3 py-2 border border-gray-300 dark:border-gray-700 rounded-lg bg-white dark:bg-gray-900">
                            <option value="">Select Chairman</option>
                            <template x-for="user in users" :key="user.id">
                                <option :value="user.id" x-text="user.name"></option>
                            </template>
                        </select>
                    </div>

                    <div class="flex justify-end gap-3 pt-4">
                        <button type="button" @click="closeModals()"
                            class="px-4 py-2 border border-gray-300 dark:border-gray-600 rounded-lg text-gray-700 dark:text-gray-300 hover:bg-gray-50 dark:hover:bg-gray-700">
                            Cancel
                        </button>
                        <button type="submit" class="px-4 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700">
                            <span x-text="editingDivision ? 'Update' : 'Create'"></span>
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    @push('scripts')
        <script>
            function treeManager() {
                return {
                    // Data
                    organizations: @json($organizations),
                    clusters: [],
                    divisions: [],
                    users: @json($users),

                    // Selection state
                    selectedOrg: null,
                    selectedCluster: null,

                    // Modal states
                    showOrgModalFlag: false,
                    showClusterModalFlag: false,
                    showDivisionModalFlag: false,

                    // Editing states
                    editingOrg: null,
                    editingCluster: null,
                    editingDivision: null,

                    // Form data
                    currentOrg: {
                        name: ''
                    },
                    currentCluster: {
                        name: '',
                        cluster_chairman: ''
                    },
                    currentDivision: {
                        name: '',
                        cluster_id: '',
                        division_chairman: ''
                    },

                    // Methods
                    async init() {
                        // Initial load
                    },

                    // Selection handlers
                    async selectOrg(org) {
                        this.selectedOrg = org;
                        this.selectedCluster = null;
                        this.divisions = [];

                        try {
                            // Fetch clusters for this organization
                            const response = await fetch(`/organizations/${org.id}/clusters`);
                            this.clusters = await response.json();
                        } catch (error) {
                            console.error('Error loading clusters:', error);
                            this.clusters = [];
                        }
                    },

                    async selectCluster(cluster) {
                        this.selectedCluster = cluster;

                        try {
                            // Fetch divisions for this cluster
                            const response = await fetch(`/clusters/${cluster.id}/divisions`);
                            this.divisions = await response.json();
                        } catch (error) {
                            console.error('Error loading divisions:', error);
                            this.divisions = [];
                        }
                    },

                    resetSelection() {
                        this.selectedOrg = null;
                        this.selectedCluster = null;
                        this.clusters = [];
                        this.divisions = [];
                    },

                    // Modal handlers
                    showOrgModal(org = null) {
                        this.editingOrg = org;
                        this.currentOrg = org ? {
                            ...org
                        } : {
                            name: ''
                        };
                        this.showOrgModalFlag = true;
                    },

                    showClusterModal(cluster = null) {
                        if (!this.selectedOrg) return;
                        this.editingCluster = cluster;
                        this.currentCluster = cluster ? {
                            ...cluster
                        } : {
                            name: '',
                            cluster_chairman: ''
                        };
                        this.showClusterModalFlag = true;
                    },

                    showDivisionModal(division = null) {
                        if (!this.selectedCluster) return;
                        this.editingDivision = division;
                        this.currentDivision = division ? {
                            ...division
                        } : {
                            name: '',
                            cluster_id: this.selectedCluster.id,
                            division_chairman: ''
                        };
                        this.showDivisionModalFlag = true;
                    },

                    closeModals() {
                        this.showOrgModalFlag = false;
                        this.showClusterModalFlag = false;
                        this.showDivisionModalFlag = false;
                        this.editingOrg = null;
                        this.editingCluster = null;
                        this.editingDivision = null;
                    },

                    // CRUD operations
                    async saveOrg() {
                        try {
                            const url = this.editingOrg ?
                                `/organizations/${this.editingOrg.id}` :
                                '/organizations';

                            const method = this.editingOrg ? 'PUT' : 'POST';

                            const response = await fetch(url, {
                                method,
                                headers: {
                                    'Content-Type': 'application/json',
                                    'X-CSRF-TOKEN': '{{ csrf_token() }}'
                                },
                                body: JSON.stringify(this.currentOrg)
                            });

                            if (response.ok) {
                                await this.refreshOrgs();
                                this.closeModals();
                            }
                        } catch (error) {
                            console.error('Error saving organization:', error);
                        }
                    },

                    async saveCluster() {
                        try {
                            this.currentCluster.organization_id = this.selectedOrg.id;

                            const url = this.editingCluster ?
                                `/clusters/${this.editingCluster.id}` :
                                '/clusters';

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
                                await this.selectOrg(this.selectedOrg); // Refresh clusters
                                this.closeModals();
                            }
                        } catch (error) {
                            console.error('Error saving cluster:', error);
                        }
                    },

                    async saveDivision() {
                        try {
                            const url = this.editingDivision ?
                                `/divisions/${this.editingDivision.id}` :
                                '/divisions';

                            const method = this.editingDivision ? 'PUT' : 'POST';

                            const response = await fetch(url, {
                                method,
                                headers: {
                                    'Content-Type': 'application/json',
                                    'X-CSRF-TOKEN': '{{ csrf_token() }}'
                                },
                                body: JSON.stringify(this.currentDivision)
                            });

                            if (response.ok) {
                                await this.selectCluster(this.selectedCluster); // Refresh divisions
                                this.closeModals();
                            }
                        } catch (error) {
                            console.error('Error saving division:', error);
                        }
                    },

                    editOrg(org) {
                        this.showOrgModal(org);
                    },

                    editCluster(cluster) {
                        this.showClusterModal(cluster);
                    },

                    editDivision(division) {
                        this.showDivisionModal(division);
                    },

                    async deleteOrg(org) {
                        if (confirm(`Delete organization "${org.name}" and all its clusters and divisions?`)) {
                            try {
                                await fetch(`/organizations/${org.id}`, {
                                    method: 'DELETE',
                                    headers: {
                                        'X-CSRF-TOKEN': '{{ csrf_token() }}'
                                    }
                                });
                                await this.refreshOrgs();

                                if (this.selectedOrg?.id === org.id) {
                                    this.resetSelection();
                                }
                            } catch (error) {
                                console.error('Error deleting organization:', error);
                            }
                        }
                    },

                    async deleteCluster(cluster) {
                        if (confirm(`Delete cluster "${cluster.name}" and all its divisions?`)) {
                            try {
                                await fetch(`/clusters/${cluster.id}`, {
                                    method: 'DELETE',
                                    headers: {
                                        'X-CSRF-TOKEN': '{{ csrf_token() }}'
                                    }
                                });
                                await this.selectOrg(this.selectedOrg); // Refresh clusters

                                if (this.selectedCluster?.id === cluster.id) {
                                    this.selectedCluster = null;
                                    this.divisions = [];
                                }
                            } catch (error) {
                                console.error('Error deleting cluster:', error);
                            }
                        }
                    },

                    async deleteDivision(division) {
                        if (confirm(`Delete division "${division.name}"?`)) {
                            try {
                                await fetch(`/divisions/${division.id}`, {
                                    method: 'DELETE',
                                    headers: {
                                        'X-CSRF-TOKEN': '{{ csrf_token() }}'
                                    }
                                });
                                await this.selectCluster(this.selectedCluster); // Refresh divisions
                            } catch (error) {
                                console.error('Error deleting division:', error);
                            }
                        }
                    },

                    async refreshOrgs() {
                        try {
                            const response = await fetch('/organizations');
                            this.organizations = await response.json();
                        } catch (error) {
                            console.error('Error refreshing organizations:', error);
                        }
                    }
                };
            }
        </script>
    @endpush
@endsection
