@extends('layouts.app')

@section('content')
    @php
        $cluster = \App\Models\Cluster::with(['organization', 'chairman'])->findOrFail($clusterId);
    @endphp

    <x-common.page-breadcrumb pageTitle="Divisions - {{ $cluster->name }}" />

    <div class="max-w-7xl mx-auto">
        <!-- Header with breadcrumb -->
        <div class="mb-6">
            <div class="flex items-center justify-between">
                <div class="flex items-center gap-4">
                    <div class="flex items-center gap-2">
                        <a href="{{ route('organizations.index') }}"
                            class="text-gray-600 dark:text-gray-400 hover:text-blue-600">
                            <i class="bi bi-building"></i>
                        </a>
                        <i class="bi bi-chevron-right text-gray-400"></i>
                        <a href="{{ route('organizations.clusters', $cluster->organization) }}"
                            class="text-gray-600 dark:text-gray-400 hover:text-blue-600">
                            {{ $cluster->organization->name }}
                        </a>
                        <i class="bi bi-chevron-right text-gray-400"></i>
                        <span class="font-medium text-gray-900 dark:text-white">{{ $cluster->name }}</span>
                    </div>
                </div>
                <button @click="showDivisionModal()" class="bg-blue-600 text-white px-4 py-2 rounded-lg hover:bg-blue-700">
                    <i class="bi bi-plus-lg mr-2"></i>New Division
                </button>
            </div>

            <!-- Cluster Info -->
            <div class="mt-4 bg-white dark:bg-gray-800 p-4 rounded-xl border border-gray-200 dark:border-gray-700">
                <div class="flex items-center justify-between">
                    <div>
                        <h2 class="text-lg font-semibold text-gray-900 dark:text-white">{{ $cluster->name }}</h2>
                        <div class="mt-2 flex items-center gap-4 text-sm">
                            <span class="text-gray-600 dark:text-gray-400">
                                <i class="bi bi-building mr-1"></i>
                                {{ $cluster->organization->name }}
                            </span>
                            @if ($cluster->chairman)
                                <span class="text-gray-600 dark:text-gray-400">
                                    <i class="bi bi-person mr-1"></i>
                                    Chairman: {{ $cluster->chairman->name }}
                                </span>
                            @endif
                        </div>
                    </div>
                    <span
                        class="px-3 py-1 rounded-full text-sm font-medium bg-blue-100 text-blue-800 dark:bg-blue-900 dark:text-blue-300">
                        <span x-text="divisions.length"></span> divisions
                    </span>
                </div>
            </div>
        </div>

        <!-- Divisions Table -->
        <div class="bg-white dark:bg-gray-800 rounded-xl border border-gray-200 dark:border-gray-700 overflow-hidden">
            <table class="min-w-full">
                <thead class="bg-gray-50 dark:bg-gray-900">
                    <tr>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase">Name
                        </th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase">
                            Chairman</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase">
                            Created</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase">
                            Actions</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-200 dark:divide-gray-700">
                    <template x-for="division in divisions" :key="division.id">
                        <tr class="hover:bg-gray-50 dark:hover:bg-gray-700">
                            <td class="px-6 py-4">
                                <span x-text="division.name" class="font-medium text-gray-900 dark:text-white"></span>
                            </td>
                            <td class="px-6 py-4">
                                <template x-if="division.chairman">
                                    <div>
                                        <span x-text="division.chairman.name" class="text-gray-900 dark:text-white"></span>
                                    </div>
                                </template>
                                <template x-if="!division.chairman">
                                    <span class="text-gray-400">Not assigned</span>
                                </template>
                            </td>
                            <td class="px-6 py-4 text-sm text-gray-500 dark:text-gray-400">
                                <span x-text="formatDate(division.created_at)"></span>
                            </td>
                            <td class="px-6 py-4">
                                <div class="flex items-center gap-2">
                                    <button @click="editDivision(division)"
                                        class="text-gray-600 dark:text-gray-400 hover:text-blue-600">
                                        <i class="bi bi-pencil"></i>
                                    </button>
                                    <button @click="deleteDivision(division)"
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
            <div x-show="divisions.length === 0" class="text-center py-12">
                <i class="bi bi-layers text-4xl text-gray-300 dark:text-gray-600"></i>
                <p class="text-gray-500 dark:text-gray-400 mt-3">No divisions in this cluster</p>
                <button @click="showDivisionModal()"
                    class="mt-4 bg-blue-600 text-white px-4 py-2 rounded-lg hover:bg-blue-700">
                    Create First Division
                </button>
            </div>
        </div>
    </div>

    <!-- Division Modal -->
    <div x-data="divisionManager()" x-init="init()">
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
        function divisionManager() {
            return {
                cluster: @json($cluster),
                divisions: [],
                users: @json($users),
                showDivisionModalFlag: false,
                editingDivision: null,
                currentDivision: {
                    name: '',
                    cluster_id: '',
                    division_chairman: ''
                },

                async init() {
                    await this.loadDivisions();
                    this.currentDivision.cluster_id = this.cluster.id;
                },

                async loadDivisions() {
                    try {
                        const response = await fetch(`/api/organizations/${this.cluster.organization_id}/divisions`);
                        const allDivisions = await response.json();
                        this.divisions = allDivisions.filter(d => d.cluster_id === this.cluster.id);
                    } catch (error) {
                        console.error('Error loading divisions:', error);
                    }
                },

                formatDate(dateString) {
                    return new Date(dateString).toLocaleDateString('en-US', {
                        year: 'numeric',
                        month: 'short',
                        day: 'numeric'
                    });
                },

                showDivisionModal(division = null) {
                    this.editingDivision = division;
                    this.currentDivision = division ? {
                        ...division
                    } : {
                        name: '',
                        cluster_id: this.cluster.id,
                        division_chairman: ''
                    };
                    this.showDivisionModalFlag = true;
                },

                closeModals() {
                    this.showDivisionModalFlag = false;
                    this.editingDivision = null;
                },

                async saveDivision() {
                    try {
                        const url = this.editingDivision ?
                            `/api/divisions/${this.editingDivision.id}` :
                            '/api/divisions';

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
                            await this.loadDivisions();
                            this.closeModals();
                        }
                    } catch (error) {
                        console.error('Error:', error);
                    }
                },

                async deleteDivision(division) {
                    if (confirm(`Delete division "${division.name}"?`)) {
                        try {
                            await fetch(`/api/divisions/${division.id}`, {
                                method: 'DELETE',
                                headers: {
                                    'X-CSRF-TOKEN': '{{ csrf_token() }}'
                                }
                            });
                            await this.loadDivisions();
                        } catch (error) {
                            console.error('Error:', error);
                        }
                    }
                },

                editDivision(division) {
                    this.showDivisionModal(division);
                }
            };
        }
    </script>
@endsection
