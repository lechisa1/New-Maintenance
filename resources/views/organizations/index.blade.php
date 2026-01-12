@extends('layouts.app')

@section('content')
<x-common.page-breadcrumb pageTitle="Organization Management" />

<!-- WRAP EVERYTHING IN ONE ALPINE.JS COMPONENT -->
<div x-data="organizationManager()" x-init="init()">
    <div class="grid grid-cols-1 gap-6 lg:grid-cols-3">
        <!-- Organizations List -->
        <div class="rounded-2xl border border-gray-200 bg-white p-6 dark:border-gray-800 dark:bg-white/[0.03]">
            <div class="mb-6 flex items-center justify-between">
                <h3 class="text-lg font-semibold text-gray-800 dark:text-white/90">
                    <i class="bi bi-building me-2"></i>Organizations
                </h3>
                <button @click="showCreateOrganizationModal = true" 
                    class="inline-flex items-center justify-center rounded-lg bg-blue-500 px-3 py-2 text-xs font-semibold text-white shadow-theme-xs hover:bg-blue-600">
                    <i class="bi bi-plus-lg me-1"></i>New Organization
                </button>
            </div>

            <!-- Search -->
            <div class="mb-4">
                <div class="relative">
                    <input type="text" x-model="search" placeholder="Search organizations..."
                        class="h-10 w-full rounded-lg border border-gray-300 bg-transparent px-3 py-2 pl-9 text-sm text-gray-800 shadow-theme-xs placeholder:text-gray-400 focus:border-blue-300 focus:outline-none focus:ring-2 focus:ring-blue-500/10 dark:border-gray-700 dark:bg-gray-900 dark:text-white/90 dark:placeholder:text-white/30 dark:focus:border-blue-800">
                    <svg class="absolute left-3 top-1/2 -translate-y-1/2 fill-gray-500 dark:fill-gray-400" width="16" height="16" viewBox="0 0 16 16">
                        <path fill-rule="evenodd" clip-rule="evenodd" d="M7.333 3a4.333 4.333 0 100 8.667 4.333 4.333 0 000-8.667zM2 7.333a5.333 5.333 0 1110.667 0A5.333 5.333 0 012 7.333z" fill=""></path>
                        <path fill-rule="evenodd" clip-rule="evenodd" d="M10.628 10.628a.667.667 0 011.06.837l1.333 2a.667.667 0 11-1.087.773l-1.333-2a.667.667 0 01.837-1.06z" fill=""></path>
                    </svg>
                </div>
            </div>

            <!-- Organizations List -->
            <div class="space-y-3 max-h-[400px] overflow-y-auto pr-2">
                <template x-for="org in filteredOrganizations" :key="org.id">
                    <div class="rounded-lg border border-gray-200 p-3 hover:border-blue-300 hover:bg-blue-50/50 dark:border-gray-700 dark:hover:border-blue-800 dark:hover:bg-blue-900/20"
                         @click="selectOrganization(org)">
                        <div class="flex items-start justify-between">
                            <div class="flex-1">
                                <div class="flex items-center gap-2">
                                    <h4 class="font-medium text-gray-800 dark:text-white/90" x-text="org.name"></h4>
                                    <span x-show="org.clusters_count > 0" 
                                          class="rounded-full bg-blue-100 px-2 py-0.5 text-xs font-medium text-blue-800 dark:bg-blue-900 dark:text-blue-200">
                                        <span x-text="org.clusters_count"></span> clusters
                                    </span>
                                </div>
                                <div class="mt-2 flex items-center gap-3">
                                    <span class="text-xs text-gray-500 dark:text-gray-400" 
                                          x-text="formatDate(org.created_at)"></span>
                                </div>
                            </div>
                            <div class="ml-2 flex gap-1">
                                <button @click.stop="showEditOrganizationModal(org)"
                                    class="rounded p-1.5 text-gray-400 hover:bg-gray-100 hover:text-blue-600 dark:hover:bg-gray-800 dark:hover:text-blue-500">
                                    <i class="bi bi-pencil text-xs"></i>
                                </button>
                                <button @click.stop="confirmDeleteOrganization(org)"
                                    class="rounded p-1.5 text-gray-400 hover:bg-gray-100 hover:text-red-600 dark:hover:bg-gray-800 dark:hover:text-red-500">
                                    <i class="bi bi-trash text-xs"></i>
                                </button>
                            </div>
                        </div>
                    </div>
                </template>
                
                <div x-show="organizations.length === 0" class="text-center py-8">
                    <i class="bi bi-building text-3xl text-gray-300 dark:text-gray-600"></i>
                    <p class="mt-2 text-gray-500 dark:text-gray-400">No organizations found</p>
                    <button @click="showCreateOrganizationModal = true" 
                        class="mt-3 inline-flex items-center rounded-lg bg-blue-500 px-3 py-1.5 text-xs font-semibold text-white hover:bg-blue-600">
                        <i class="bi bi-plus me-1"></i>Create First Organization
                    </button>
                </div>
            </div>
        </div>

        <!-- Main Content Area -->
        <div class="lg:col-span-2">
            <!-- Tabs Navigation -->
            <div class="mb-6 border-b border-gray-200 dark:border-gray-700">
                <nav class="-mb-px flex space-x-6">
                    <button @click="activeTab = 'overview'" 
                        :class="activeTab === 'overview' ? 'border-blue-500 text-blue-600 dark:border-blue-500 dark:text-blue-400' : 'border-transparent text-gray-500 hover:border-gray-300 hover:text-gray-700 dark:text-gray-400 dark:hover:text-gray-300'"
                        class="whitespace-nowrap border-b-2 px-1 py-3 text-sm font-medium">
                        <i class="bi bi-eye me-2"></i>Overview
                    </button>
                    <button @click="activeTab = 'clusters'" 
                        :class="activeTab === 'clusters' ? 'border-blue-500 text-blue-600 dark:border-blue-500 dark:text-blue-400' : 'border-transparent text-gray-500 hover:border-gray-300 hover:text-gray-700 dark:text-gray-400 dark:hover:text-gray-300'"
                        class="whitespace-nowrap border-b-2 px-1 py-3 text-sm font-medium">
                        <i class="bi bi-diagram-3 me-2"></i>Clusters
                    </button>
                    <button @click="activeTab = 'divisions'" 
                        :class="activeTab === 'divisions' ? 'border-blue-500 text-blue-600 dark:border-blue-500 dark:text-blue-400' : 'border-transparent text-gray-500 hover:border-gray-300 hover:text-gray-700 dark:text-gray-400 dark:hover:text-gray-300'"
                        class="whitespace-nowrap border-b-2 px-1 py-3 text-sm font-medium">
                        <i class="bi bi-layers me-2"></i>Divisions
                    </button>
                </nav>
            </div>

            <!-- Overview Tab -->
            <div x-show="activeTab === 'overview'" class="space-y-6">
                <template x-if="selectedOrganization">
                    <div class="rounded-2xl border border-gray-200 bg-white p-6 dark:border-gray-800 dark:bg-white/[0.03]">
                        <div class="mb-6 flex items-start justify-between">
                            <div>
                                <h3 class="text-lg font-semibold text-gray-800 dark:text-white/90" 
                                    x-text="selectedOrganization.name"></h3>
                                <div class="mt-2 flex items-center gap-3">
                                    <span class="text-sm text-gray-500 dark:text-gray-400">
                                        Created: <span x-text="formatDate(selectedOrganization.created_at)"></span>
                                    </span>
                                    <span class="text-sm text-gray-500 dark:text-gray-400">
                                        Updated: <span x-text="formatDate(selectedOrganization.updated_at)"></span>
                                    </span>
                                </div>
                            </div>
                            <button @click="showEditOrganizationModal(selectedOrganization)"
                                class="rounded-lg border border-gray-300 bg-white px-4 py-2 text-sm font-medium text-gray-700 shadow-theme-xs hover:bg-gray-50 dark:border-gray-700 dark:bg-gray-800 dark:text-gray-400 dark:hover:bg-white/[0.03]">
                                <i class="bi bi-pencil me-2"></i>Edit
                            </button>
                        </div>

                        <!-- Stats Cards -->
                        <div class="grid grid-cols-1 gap-4 md:grid-cols-3">
                            <div class="rounded-lg border border-gray-200 bg-white p-4 dark:border-gray-700 dark:bg-gray-800">
                                <div class="flex items-center">
                                    <div class="rounded-lg bg-blue-100 p-2 dark:bg-blue-900">
                                        <i class="bi bi-diagram-3 text-blue-600 dark:text-blue-400"></i>
                                    </div>
                                    <div class="ml-3">
                                        <p class="text-sm text-gray-500 dark:text-gray-400">Total Clusters</p>
                                        <h4 class="text-xl font-semibold text-gray-800 dark:text-white/90" 
                                            x-text="selectedOrganization.clusters_count || 0"></h4>
                                    </div>
                                </div>
                            </div>
                            
                            <div class="rounded-lg border border-gray-200 bg-white p-4 dark:border-gray-700 dark:bg-gray-800">
                                <div class="flex items-center">
                                    <div class="rounded-lg bg-green-100 p-2 dark:bg-green-900">
                                        <i class="bi bi-layers text-green-600 dark:text-green-400"></i>
                                    </div>
                                    <div class="ml-3">
                                        <p class="text-sm text-gray-500 dark:text-gray-400">Total Divisions</p>
                                        <h4 class="text-xl font-semibold text-gray-800 dark:text-white/90" 
                                            x-text="selectedOrganization.divisions_count || 0"></h4>
                                    </div>
                                </div>
                            </div>
                            
                            <div class="rounded-lg border border-gray-200 bg-white p-4 dark:border-gray-700 dark:bg-gray-800">
                                <div class="flex items-center">
                                    <div class="rounded-lg bg-purple-100 p-2 dark:bg-purple-900">
                                        <i class="bi bi-people text-purple-600 dark:text-purple-400"></i>
                                    </div>
                                    <div class="ml-3">
                                        <p class="text-sm text-gray-500 dark:text-gray-400">Total Users</p>
                                        <h4 class="text-xl font-semibold text-gray-800 dark:text-white/90" 
                                            x-text="selectedOrganization.users_count || 0"></h4>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </template>
                
                <template x-if="!selectedOrganization">
                    <div class="rounded-2xl border border-gray-200 bg-white p-12 text-center dark:border-gray-800 dark:bg-white/[0.03]">
                        <i class="bi bi-building text-4xl text-gray-300 dark:text-gray-600"></i>
                        <p class="mt-4 text-gray-500 dark:text-gray-400">Select an organization to view details</p>
                    </div>
                </template>
            </div>

            <!-- Clusters Tab -->
            <div x-show="activeTab === 'clusters'" class="space-y-6">
                <div class="rounded-2xl border border-gray-200 bg-white p-6 dark:border-gray-800 dark:bg-white/[0.03]">
                    <div class="mb-6 flex items-center justify-between">
                        <h3 class="text-lg font-semibold text-gray-800 dark:text-white/90">
                            <i class="bi bi-diagram-3 me-2"></i>
                            <template x-if="selectedOrganization">
                                <span x-text="selectedOrganization.name + ' Clusters'"></span>
                            </template>
                            <template x-if="!selectedOrganization">
                                <span>Clusters</span>
                            </template>
                        </h3>
                        <button @click="showCreateClusterModal = true" 
                            :disabled="!selectedOrganization"
                            :class="!selectedOrganization ? 'opacity-50 cursor-not-allowed' : ''"
                            class="inline-flex items-center justify-center rounded-lg bg-blue-500 px-3 py-2 text-xs font-semibold text-white shadow-theme-xs hover:bg-blue-600 disabled:hover:bg-blue-500">
                            <i class="bi bi-plus-lg me-1"></i>New Cluster
                        </button>
                    </div>

                    <!-- Clusters Grid -->
                    <div class="grid grid-cols-1 gap-4 md:grid-cols-2">
                        <template x-for="cluster in clusters" :key="cluster.id">
                            <div class="rounded-lg border border-gray-200 p-4 hover:border-blue-300 hover:bg-blue-50/50 dark:border-gray-700 dark:hover:border-blue-800 dark:hover:bg-blue-900/20">
                                <div class="flex items-start justify-between">
                                    <div class="flex-1">
                                        <h4 class="font-medium text-gray-800 dark:text-white/90" 
                                            x-text="cluster.name"></h4>
                                        
                                        <!-- Cluster Chairman -->
                                        <div class="mt-2 flex items-center gap-2">
                                            <i class="bi bi-person text-gray-400"></i>
                                            <span class="text-sm text-gray-600 dark:text-gray-400">
                                                Chairman: 
                                                <template x-if="cluster.chairman">
                                                    <span x-text="cluster.chairman.name" class="font-medium"></span>
                                                </template>
                                                <template x-if="!cluster.chairman">
                                                    <span class="text-gray-400">Not assigned</span>
                                                </template>
                                            </span>
                                        </div>
                                        
                                        <!-- Division Count -->
                                        <div class="mt-2 flex items-center gap-2">
                                            <i class="bi bi-layers text-gray-400"></i>
                                            <span class="text-sm text-gray-600 dark:text-gray-400">
                                                <span x-text="cluster.divisions_count || 0"></span> divisions
                                            </span>
                                        </div>
                                    </div>
                                    <div class="ml-2 flex gap-1">
                                        <button @click="showEditClusterModal(cluster)"
                                            class="rounded p-1.5 text-gray-400 hover:bg-gray-100 hover:text-blue-600 dark:hover:bg-gray-800 dark:hover:text-blue-500">
                                            <i class="bi bi-pencil text-xs"></i>
                                        </button>
                                        <button @click="confirmDeleteCluster(cluster)"
                                            class="rounded p-1.5 text-gray-400 hover:bg-gray-100 hover:text-red-600 dark:hover:bg-gray-800 dark:hover:text-red-500">
                                            <i class="bi bi-trash text-xs"></i>
                                        </button>
                                    </div>
                                </div>
                            </div>
                        </template>
                        
                        <div x-show="clusters.length === 0" class="col-span-2 py-8 text-center">
                            <i class="bi bi-diagram-3 text-3xl text-gray-300 dark:text-gray-600"></i>
                            <p class="mt-2 text-gray-500 dark:text-gray-400">No clusters found</p>
                            <template x-if="selectedOrganization">
                                <button @click="showCreateClusterModal = true" 
                                    class="mt-3 inline-flex items-center rounded-lg bg-blue-500 px-3 py-1.5 text-xs font-semibold text-white hover:bg-blue-600">
                                    <i class="bi bi-plus me-1"></i>Create First Cluster
                                </button>
                            </template>
                            <template x-if="!selectedOrganization">
                                <p class="mt-2 text-sm text-gray-400">Select an organization to create clusters</p>
                            </template>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Divisions Tab -->
            <div x-show="activeTab === 'divisions'" class="space-y-6">
                <div class="rounded-2xl border border-gray-200 bg-white p-6 dark:border-gray-800 dark:bg-white/[0.03]">
                    <div class="mb-6 flex items-center justify-between">
                        <h3 class="text-lg font-semibold text-gray-800 dark:text-white/90">
                            <i class="bi bi-layers me-2"></i>
                            <template x-if="selectedOrganization">
                                <span x-text="selectedOrganization.name + ' Divisions'"></span>
                            </template>
                            <template x-if="!selectedOrganization">
                                <span>Divisions</span>
                            </template>
                        </h3>
                        <button @click="showCreateDivisionModal = true" 
                            :disabled="!selectedOrganization || clusters.length === 0"
                            :class="!selectedOrganization || clusters.length === 0 ? 'opacity-50 cursor-not-allowed' : ''"
                            class="inline-flex items-center justify-center rounded-lg bg-blue-500 px-3 py-2 text-xs font-semibold text-white shadow-theme-xs hover:bg-blue-600 disabled:hover:bg-blue-500">
                            <i class="bi bi-plus-lg me-1"></i>New Division
                        </button>
                    </div>

                    <!-- Divisions Grid -->
                    <div class="grid grid-cols-1 gap-4 md:grid-cols-2 lg:grid-cols-3">
                        <template x-for="division in divisions" :key="division.id">
                            <div class="rounded-lg border border-gray-200 p-4 hover:border-blue-300 hover:bg-blue-50/50 dark:border-gray-700 dark:hover:border-blue-800 dark:hover:bg-blue-900/20">
                                <div class="flex items-start justify-between">
                                    <div class="flex-1">
                                        <h4 class="font-medium text-gray-800 dark:text-white/90" 
                                            x-text="division.name"></h4>
                                        
                                        <!-- Cluster Name -->
                                        <div class="mt-1">
                                            <span class="text-xs text-gray-500 dark:text-gray-400">
                                                Cluster: <span x-text="division.cluster_name" class="font-medium"></span>
                                            </span>
                                        </div>
                                        
                                        <!-- Division Chairman -->
                                        <div class="mt-2 flex items-center gap-2">
                                            <i class="bi bi-person text-gray-400"></i>
                                            <span class="text-sm text-gray-600 dark:text-gray-400">
                                                Chairman: 
                                                <template x-if="division.chairman">
                                                    <span x-text="division.chairman.name" class="font-medium"></span>
                                                </template>
                                                <template x-if="!division.chairman">
                                                    <span class="text-gray-400">Not assigned</span>
                                                </template>
                                            </span>
                                        </div>
                                    </div>
                                    <div class="ml-2 flex gap-1">
                                        <button @click="showEditDivisionModal(division)"
                                            class="rounded p-1.5 text-gray-400 hover:bg-gray-100 hover:text-blue-600 dark:hover:bg-gray-800 dark:hover:text-blue-500">
                                            <i class="bi bi-pencil text-xs"></i>
                                        </button>
                                        <button @click="confirmDeleteDivision(division)"
                                            class="rounded p-1.5 text-gray-400 hover:bg-gray-100 hover:text-red-600 dark:hover:bg-gray-800 dark:hover:text-red-500">
                                            <i class="bi bi-trash text-xs"></i>
                                        </button>
                                    </div>
                                </div>
                            </div>
                        </template>
                        
                        <div x-show="divisions.length === 0" class="col-span-3 py-8 text-center">
                            <i class="bi bi-layers text-3xl text-gray-300 dark:text-gray-600"></i>
                            <p class="mt-2 text-gray-500 dark:text-gray-400">No divisions found</p>
                            <template x-if="selectedOrganization && clusters.length > 0">
                                <button @click="showCreateDivisionModal = true" 
                                    class="mt-3 inline-flex items-center rounded-lg bg-blue-500 px-3 py-1.5 text-xs font-semibold text-white hover:bg-blue-600">
                                    <i class="bi bi-plus me-1"></i>Create First Division
                                </button>
                            </template>
                            <template x-if="selectedOrganization && clusters.length === 0">
                                <p class="mt-2 text-sm text-gray-400">Create a cluster first to add divisions</p>
                            </template>
                            <template x-if="!selectedOrganization">
                                <p class="mt-2 text-sm text-gray-400">Select an organization to view divisions</p>
                            </template>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- MODALS - NOW INSIDE THE SAME ALPINE.JS COMPONENT -->

    <!-- Create Organization Modal -->
    <div x-show="showCreateOrganizationModal" x-cloak class="fixed inset-0 z-50 flex items-center justify-center bg-black/50 p-4">
        <div class="w-full max-w-md rounded-2xl bg-white p-6 dark:bg-gray-800" @click.away="showCreateOrganizationModal = false">
            <h3 class="mb-6 text-lg font-semibold text-gray-800 dark:text-white/90">
                <i class="bi bi-plus-circle me-2"></i>Create New Organization
            </h3>
            
            <form @submit.prevent="createOrganization">
                <div class="space-y-4">
                    <div>
                        <label class="mb-2 block text-sm font-medium text-gray-700 dark:text-gray-300">
                            Organization Name <span class="text-red-500">*</span>
                        </label>
                        <input type="text" x-model="newOrganization.name" required
                            class="h-11 w-full rounded-lg border border-gray-300 bg-transparent px-4 py-2.5 text-sm text-gray-800 shadow-theme-xs placeholder:text-gray-400 focus:border-blue-300 focus:outline-none focus:ring-2 focus:ring-blue-500/10 dark:border-gray-700 dark:bg-gray-900 dark:text-white/90 dark:placeholder:text-white/30 dark:focus:border-blue-800"
                            placeholder="e.g., Ministry of Education">
                    </div>
                    
                    <div class="flex justify-end gap-3 pt-4">
                        <button type="button" @click="showCreateOrganizationModal = false"
                            class="rounded-lg border border-gray-300 bg-white px-6 py-3 text-sm font-semibold text-gray-700 shadow-theme-xs hover:bg-gray-50 dark:border-gray-700 dark:bg-gray-800 dark:text-gray-400 dark:hover:bg-white/[0.03]">
                            Cancel
                        </button>
                        <button type="submit"
                            class="rounded-lg bg-blue-500 px-6 py-3 text-sm font-semibold text-white shadow-theme-xs hover:bg-blue-600">
                            Create Organization
                        </button>
                    </div>
                </div>
            </form>
        </div>
    </div>

    <!-- Edit Organization Modal -->
    <div x-show="showEditOrganizationModal" x-cloak class="fixed inset-0 z-50 flex items-center justify-center bg-black/50 p-4">
        <div class="w-full max-w-md rounded-2xl bg-white p-6 dark:bg-gray-800" @click.away="showEditOrganizationModal = false">
            <h3 class="mb-6 text-lg font-semibold text-gray-800 dark:text-white/90">
                <i class="bi bi-pencil me-2"></i>Edit Organization
            </h3>
            
            <form @submit.prevent="updateOrganization">
                <div class="space-y-4">
                    <div>
                        <label class="mb-2 block text-sm font-medium text-gray-700 dark:text-gray-300">
                            Organization Name <span class="text-red-500">*</span>
                        </label>
                        <input type="text" x-model="editOrganization.name" required
                            class="h-11 w-full rounded-lg border border-gray-300 bg-transparent px-4 py-2.5 text-sm text-gray-800 shadow-theme-xs placeholder:text-gray-400 focus:border-blue-300 focus:outline-none focus:ring-2 focus:ring-blue-500/10 dark:border-gray-700 dark:bg-gray-900 dark:text-white/90 dark:placeholder:text-white/30 dark:focus:border-blue-800">
                    </div>
                    
                    <div class="flex justify-end gap-3 pt-4">
                        <button type="button" @click="showEditOrganizationModal = false"
                            class="rounded-lg border border-gray-300 bg-white px-6 py-3 text-sm font-semibold text-gray-700 shadow-theme-xs hover:bg-gray-50 dark:border-gray-700 dark:bg-gray-800 dark:text-gray-400 dark:hover:bg-white/[0.03]">
                            Cancel
                        </button>
                        <button type="submit"
                            class="rounded-lg bg-blue-500 px-6 py-3 text-sm font-semibold text-white shadow-theme-xs hover:bg-blue-600">
                            Update Organization
                        </button>
                    </div>
                </div>
            </form>
        </div>
    </div>

    <!-- Create Cluster Modal -->
    <div x-show="showCreateClusterModal" x-cloak class="fixed inset-0 z-50 flex items-center justify-center bg-black/50 p-4">
        <div class="w-full max-w-md rounded-2xl bg-white p-6 dark:bg-gray-800" @click.away="showCreateClusterModal = false">
            <h3 class="mb-6 text-lg font-semibold text-gray-800 dark:text-white/90">
                <i class="bi bi-diagram-3 me-2"></i>Create New Cluster
            </h3>
            
            <form @submit.prevent="createCluster">
                <div class="space-y-4">
                    <div>
                        <label class="mb-2 block text-sm font-medium text-gray-700 dark:text-gray-300">
                            Organization
                        </label>
                        <div class="rounded-lg border border-gray-300 bg-gray-50 p-3 dark:border-gray-700 dark:bg-gray-800">
                            <div class="font-medium text-gray-800 dark:text-white/90" 
                                 x-text="selectedOrganization ? selectedOrganization.name : 'No organization selected'"></div>
                        </div>
                    </div>
                    
                    <div>
                        <label class="mb-2 block text-sm font-medium text-gray-700 dark:text-gray-300">
                            Cluster Name <span class="text-red-500">*</span>
                        </label>
                        <input type="text" x-model="newCluster.name" required
                            class="h-11 w-full rounded-lg border border-gray-300 bg-transparent px-4 py-2.5 text-sm text-gray-800 shadow-theme-xs placeholder:text-gray-400 focus:border-blue-300 focus:outline-none focus:ring-2 focus:ring-blue-500/10 dark:border-gray-700 dark:bg-gray-900 dark:text-white/90 dark:placeholder:text-white/30 dark:focus:border-blue-800"
                            placeholder="e.g., Technology Cluster">
                    </div>
                    
                    <div>
                        <label class="mb-2 block text-sm font-medium text-gray-700 dark:text-gray-300">
                            Cluster Chairman
                        </label>
                        <select x-model="newCluster.cluster_chairman" 
                            class="h-11 w-full rounded-lg border border-gray-300 bg-transparent px-4 py-2.5 text-sm text-gray-800 shadow-theme-xs focus:border-blue-300 focus:outline-none focus:ring-2 focus:ring-blue-500/10 dark:border-gray-700 dark:bg-gray-900 dark:text-white/90 dark:focus:border-blue-800">
                            <option value="">Select Chairman</option>
                            <template x-for="user in users" :key="user.id">
                                <option :value="user.id" x-text="user.name"></option>
                            </template>
                        </select>
                    </div>
                    
                    <div class="flex justify-end gap-3 pt-4">
                        <button type="button" @click="showCreateClusterModal = false"
                            class="rounded-lg border border-gray-300 bg-white px-6 py-3 text-sm font-semibold text-gray-700 shadow-theme-xs hover:bg-gray-50 dark:border-gray-700 dark:bg-gray-800 dark:text-gray-400 dark:hover:bg-white/[0.03]">
                            Cancel
                        </button>
                        <button type="submit"
                            class="rounded-lg bg-blue-500 px-6 py-3 text-sm font-semibold text-white shadow-theme-xs hover:bg-blue-600">
                            Create Cluster
                        </button>
                    </div>
                </div>
            </form>
        </div>
    </div>

    <!-- Edit Cluster Modal -->
    <div x-show="showEditClusterModal" x-cloak class="fixed inset-0 z-50 flex items-center justify-center bg-black/50 p-4">
        <div class="w-full max-w-md rounded-2xl bg-white p-6 dark:bg-gray-800" @click.away="showEditClusterModal = false">
            <h3 class="mb-6 text-lg font-semibold text-gray-800 dark:text-white/90">
                <i class="bi bi-pencil me-2"></i>Edit Cluster
            </h3>
            
            <form @submit.prevent="updateCluster">
                <div class="space-y-4">
                    <div>
                        <label class="mb-2 block text-sm font-medium text-gray-700 dark:text-gray-300">
                            Cluster Name <span class="text-red-500">*</span>
                        </label>
                        <input type="text" x-model="editCluster.name" required
                            class="h-11 w-full rounded-lg border border-gray-300 bg-transparent px-4 py-2.5 text-sm text-gray-800 shadow-theme-xs placeholder:text-gray-400 focus:border-blue-300 focus:outline-none focus:ring-2 focus:ring-blue-500/10 dark:border-gray-700 dark:bg-gray-900 dark:text-white/90 dark:placeholder:text-white/30 dark:focus:border-blue-800">
                    </div>
                    
                    <div>
                        <label class="mb-2 block text-sm font-medium text-gray-700 dark:text-gray-300">
                            Cluster Chairman
                        </label>
                        <select x-model="editCluster.cluster_chairman" 
                            class="h-11 w-full rounded-lg border border-gray-300 bg-transparent px-4 py-2.5 text-sm text-gray-800 shadow-theme-xs focus:border-blue-300 focus:outline-none focus:ring-2 focus:ring-blue-500/10 dark:border-gray-700 dark:bg-gray-900 dark:text-white/90 dark:focus:border-blue-800">
                            <option value="">Select Chairman</option>
                            <template x-for="user in users" :key="user.id">
                                <option :value="user.id" x-text="user.name"></option>
                            </template>
                        </select>
                    </div>
                    
                    <div class="flex justify-end gap-3 pt-4">
                        <button type="button" @click="showEditClusterModal = false"
                            class="rounded-lg border border-gray-300 bg-white px-6 py-3 text-sm font-semibold text-gray-700 shadow-theme-xs hover:bg-gray-50 dark:border-gray-700 dark:bg-gray-800 dark:text-gray-400 dark:hover:bg-white/[0.03]">
                            Cancel
                        </button>
                        <button type="submit"
                            class="rounded-lg bg-blue-500 px-6 py-3 text-sm font-semibold text-white shadow-theme-xs hover:bg-blue-600">
                            Update Cluster
                        </button>
                    </div>
                </div>
            </form>
        </div>
    </div>

    <!-- Create Division Modal -->
    <div x-show="showCreateDivisionModal" x-cloak class="fixed inset-0 z-50 flex items-center justify-center bg-black/50 p-4">
        <div class="w-full max-w-md rounded-2xl bg-white p-6 dark:bg-gray-800" @click.away="showCreateDivisionModal = false">
            <h3 class="mb-6 text-lg font-semibold text-gray-800 dark:text-white/90">
                <i class="bi bi-layers me-2"></i>Create New Division
            </h3>
            
            <form @submit.prevent="createDivision">
                <div class="space-y-4">
                    <div>
                        <label class="mb-2 block text-sm font-medium text-gray-700 dark:text-gray-300">
                            Organization
                        </label>
                        <div class="rounded-lg border border-gray-300 bg-gray-50 p-3 dark:border-gray-700 dark:bg-gray-800">
                            <div class="font-medium text-gray-800 dark:text-white/90" 
                                 x-text="selectedOrganization ? selectedOrganization.name : 'No organization selected'"></div>
                        </div>
                    </div>
                    
                    <div>
                        <label class="mb-2 block text-sm font-medium text-gray-700 dark:text-gray-300">
                            Cluster <span class="text-red-500">*</span>
                        </label>
                        <select x-model="newDivision.cluster_id" required
                            class="h-11 w-full rounded-lg border border-gray-300 bg-transparent px-4 py-2.5 text-sm text-gray-800 shadow-theme-xs focus:border-blue-300 focus:outline-none focus:ring-2 focus:ring-blue-500/10 dark:border-gray-700 dark:bg-gray-900 dark:text-white/90 dark:focus:border-blue-800">
                            <option value="">Select Cluster</option>
                            <template x-for="cluster in clusters" :key="cluster.id">
                                <option :value="cluster.id" x-text="cluster.name"></option>
                            </template>
                        </select>
                    </div>
                    
                    <div>
                        <label class="mb-2 block text-sm font-medium text-gray-700 dark:text-gray-300">
                            Division Name <span class="text-red-500">*</span>
                        </label>
                        <input type="text" x-model="newDivision.name" required
                            class="h-11 w-full rounded-lg border border-gray-300 bg-transparent px-4 py-2.5 text-sm text-gray-800 shadow-theme-xs placeholder:text-gray-400 focus:border-blue-300 focus:outline-none focus:ring-2 focus:ring-blue-500/10 dark:border-gray-700 dark:bg-gray-900 dark:text-white/90 dark:placeholder:text-white/30 dark:focus:border-blue-800"
                            placeholder="e.g., Software Development Division">
                    </div>
                    
                    <div>
                        <label class="mb-2 block text-sm font-medium text-gray-700 dark:text-gray-300">
                            Division Chairman
                        </label>
                        <select x-model="newDivision.division_chairman" 
                            class="h-11 w-full rounded-lg border border-gray-300 bg-transparent px-4 py-2.5 text-sm text-gray-800 shadow-theme-xs focus:border-blue-300 focus:outline-none focus:ring-2 focus:ring-blue-500/10 dark:border-gray-700 dark:bg-gray-900 dark:text-white/90 dark:focus:border-blue-800">
                            <option value="">Select Chairman</option>
                            <template x-for="user in users" :key="user.id">
                                <option :value="user.id" x-text="user.name"></option>
                            </template>
                        </select>
                    </div>
                    
                    <div class="flex justify-end gap-3 pt-4">
                        <button type="button" @click="showCreateDivisionModal = false"
                            class="rounded-lg border border-gray-300 bg-white px-6 py-3 text-sm font-semibold text-gray-700 shadow-theme-xs hover:bg-gray-50 dark:border-gray-700 dark:bg-gray-800 dark:text-gray-400 dark:hover:bg-white/[0.03]">
                            Cancel
                        </button>
                        <button type="submit"
                            class="rounded-lg bg-blue-500 px-6 py-3 text-sm font-semibold text-white shadow-theme-xs hover:bg-blue-600">
                            Create Division
                        </button>
                    </div>
                </div>
            </form>
        </div>
    </div>

    <!-- Edit Division Modal -->
    <div x-show="showEditDivisionModal" x-cloak class="fixed inset-0 z-50 flex items-center justify-center bg-black/50 p-4">
        <div class="w-full max-w-md rounded-2xl bg-white p-6 dark:bg-gray-800" @click.away="showEditDivisionModal = false">
            <h3 class="mb-6 text-lg font-semibold text-gray-800 dark:text-white/90">
                <i class="bi bi-pencil me-2"></i>Edit Division
            </h3>
            
            <form @submit.prevent="updateDivision">
                <div class="space-y-4">
                    <div>
                        <label class="mb-2 block text-sm font-medium text-gray-700 dark:text-gray-300">
                            Cluster
                        </label>
                        <select x-model="editDivision.cluster_id" required
                            class="h-11 w-full rounded-lg border border-gray-300 bg-transparent px-4 py-2.5 text-sm text-gray-800 shadow-theme-xs focus:border-blue-300 focus:outline-none focus:ring-2 focus:ring-blue-500/10 dark:border-gray-700 dark:bg-gray-900 dark:text-white/90 dark:focus:border-blue-800">
                            <option value="">Select Cluster</option>
                            <template x-for="cluster in clusters" :key="cluster.id">
                                <option :value="cluster.id" x-text="cluster.name"></option>
                            </template>
                        </select>
                    </div>
                    
                    <div>
                        <label class="mb-2 block text-sm font-medium text-gray-700 dark:text-gray-300">
                            Division Name <span class="text-red-500">*</span>
                        </label>
                        <input type="text" x-model="editDivision.name" required
                            class="h-11 w-full rounded-lg border border-gray-300 bg-transparent px-4 py-2.5 text-sm text-gray-800 shadow-theme-xs placeholder:text-gray-400 focus:border-blue-300 focus:outline-none focus:ring-2 focus:ring-blue-500/10 dark:border-gray-700 dark:bg-gray-900 dark:text-white/90 dark:placeholder:text-white/30 dark:focus:border-blue-800">
                    </div>
                    
                    <div>
                        <label class="mb-2 block text-sm font-medium text-gray-700 dark:text-gray-300">
                            Division Chairman
                        </label>
                        <select x-model="editDivision.division_chairman" 
                            class="h-11 w-full rounded-lg border border-gray-300 bg-transparent px-4 py-2.5 text-sm text-gray-800 shadow-theme-xs focus:border-blue-300 focus:outline-none focus:ring-2 focus:ring-blue-500/10 dark:border-gray-700 dark:bg-gray-900 dark:text-white/90 dark:focus:border-blue-800">
                            <option value="">Select Chairman</option>
                            <template x-for="user in users" :key="user.id">
                                <option :value="user.id" x-text="user.name"></option>
                            </template>
                        </select>
                    </div>
                    
                    <div class="flex justify-end gap-3 pt-4">
                        <button type="button" @click="showEditDivisionModal = false"
                            class="rounded-lg border border-gray-300 bg-white px-6 py-3 text-sm font-semibold text-gray-700 shadow-theme-xs hover:bg-gray-50 dark:border-gray-700 dark:bg-gray-800 dark:text-gray-400 dark:hover:bg-white/[0.03]">
                            Cancel
                        </button>
                        <button type="submit"
                            class="rounded-lg bg-blue-500 px-6 py-3 text-sm font-semibold text-white shadow-theme-xs hover:bg-blue-600">
                            Update Division
                        </button>
                    </div>
                </div>
            </form>
        </div>
    </div>

    <!-- Delete Confirmation Modal -->
    <div x-show="showDeleteModal" x-cloak class="fixed inset-0 z-50 flex items-center justify-center bg-black/50 p-4">
        <div class="w-full max-w-md rounded-2xl bg-white p-6 dark:bg-gray-800" @click.away="showDeleteModal = false">
            <div class="text-center">
                <i class="bi bi-exclamation-triangle text-4xl text-red-500"></i>
                <h3 class="mt-4 text-lg font-semibold text-gray-800 dark:text-white/90">
                    Confirm Delete
                </h3>
                <p class="mt-2 text-gray-600 dark:text-gray-400" x-text="deleteMessage"></p>
                
                <div class="mt-6 flex justify-center gap-3">
                    <button type="button" @click="showDeleteModal = false"
                        class="rounded-lg border border-gray-300 bg-white px-6 py-3 text-sm font-semibold text-gray-700 shadow-theme-xs hover:bg-gray-50 dark:border-gray-700 dark:bg-gray-800 dark:text-gray-400 dark:hover:bg-white/[0.03]">
                        Cancel
                    </button>
                    <button type="button" @click="executeDelete"
                        class="rounded-lg bg-red-500 px-6 py-3 text-sm font-semibold text-white shadow-theme-xs hover:bg-red-600">
                        Delete
                    </button>
                </div>
            </div>
        </div>
    </div>
</div> <!-- END OF ALPINE.JS COMPONENT -->

<script>
// Make sure function is globally available
window.organizationManager = function() {
    console.log('✅ organizationManager initialized');
    
    return {
        // State
        search: '',
        activeTab: 'overview',
        selectedOrganization: null,
        
        // Data Collections
        organizations: @json($organizations),
        clusters: [],
        divisions: [],
        users: @json($users ?? []),
        
        // Modal States
        showCreateOrganizationModal: false,
        showEditOrganizationModal: false,
        showCreateClusterModal: false,
        showEditClusterModal: false,
        showCreateDivisionModal: false,
        showEditDivisionModal: false,
        showDeleteModal: false,
        
        // Form Data
        newOrganization: { name: '' },
        editOrganization: { id: null, name: '' },
        newCluster: { name: '', cluster_chairman: '', organization_id: null },
        editCluster: { id: null, name: '', cluster_chairman: '' },
        newDivision: { name: '', division_chairman: '', cluster_id: '' },
        editDivision: { id: null, name: '', division_chairman: '', cluster_id: '' },
        
        // Delete State
        deleteType: '',
        deleteId: null,
        deleteMessage: '',
        
        // Computed
        get filteredOrganizations() {
            if (!this.search) return this.organizations;
            return this.organizations.filter(org => 
                org.name.toLowerCase().includes(this.search.toLowerCase())
            );
        },
        
        // Methods
        init() {
            console.log('🎯 init() called');
            console.log('Organizations:', this.organizations);
            console.log('Users:', this.users);
            
            if (this.organizations.length > 0) {
                console.log('Selecting first organization');
                this.selectOrganization(this.organizations[0]);
            }
        },
        
        selectOrganization(org) {
            console.log('Selecting organization:', org.name);
            this.selectedOrganization = org;
            this.activeTab = 'overview';
            this.loadClusters(org.id);
            this.loadDivisions(org.id);
        },
        
        async loadClusters(organizationId) {
            console.log('Loading clusters for organization:', organizationId);
            try {
                const response = await fetch(`/api/organizations/${organizationId}/clusters`);
                console.log('Clusters response status:', response.status);
                if (response.ok) {
                    this.clusters = await response.json();
                    console.log('Clusters loaded:', this.clusters.length);
                } else {
                    console.error('Failed to load clusters');
                    this.clusters = [];
                }
            } catch (error) {
                console.error('Error loading clusters:', error);
                this.clusters = [];
            }
        },
        
        async loadDivisions(organizationId) {
            console.log('Loading divisions for organization:', organizationId);
            try {
                const response = await fetch(`/api/organizations/${organizationId}/divisions`);
                console.log('Divisions response status:', response.status);
                if (response.ok) {
                    this.divisions = await response.json();
                    console.log('Divisions loaded:', this.divisions.length);
                } else {
                    console.error('Failed to load divisions');
                    this.divisions = [];
                }
            } catch (error) {
                console.error('Error loading divisions:', error);
                this.divisions = [];
            }
        },
        
        formatDate(dateString) {
            if (!dateString) return 'N/A';
            const date = new Date(dateString);
            return date.toLocaleDateString('en-US', {
                year: 'numeric',
                month: 'short',
                day: 'numeric'
            });
        },
        
        // Organization CRUD
        async createOrganization() {
            console.log('Creating organization:', this.newOrganization);
            try {
                const response = await fetch('/api/organizations', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
                    },
                    body: JSON.stringify(this.newOrganization)
                });
                
                console.log('Create response status:', response.status);
                
                if (response.ok) {
                    const newOrg = await response.json();
                    console.log('Organization created:', newOrg);
                    this.organizations.push(newOrg);
                    this.selectOrganization(newOrg);
                    this.showCreateOrganizationModal = false;
                    this.newOrganization = { name: '' };
                    alert('✅ Organization created successfully!');
                } else {
                    console.error('Failed to create organization');
                    alert('❌ Error creating organization');
                }
            } catch (error) {
                console.error('Error creating organization:', error);
                alert('❌ Error creating organization');
            }
        },
        
        showEditOrganizationModal(org) {
            console.log('Editing organization:', org);
            this.editOrganization = { id: org.id, name: org.name };
            this.showEditOrganizationModal = true;
        },
        
        async updateOrganization() {
            console.log('Updating organization:', this.editOrganization);
            try {
                const response = await fetch(`/api/organizations/${this.editOrganization.id}`, {
                    method: 'PUT',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
                    },
                    body: JSON.stringify(this.editOrganization)
                });
                
                console.log('Update response status:', response.status);
                
                if (response.ok) {
                    const updatedOrg = await response.json();
                    console.log('Organization updated:', updatedOrg);
                    const index = this.organizations.findIndex(o => o.id === updatedOrg.id);
                    if (index !== -1) {
                        this.organizations[index] = updatedOrg;
                        this.selectedOrganization = updatedOrg;
                    }
                    this.showEditOrganizationModal = false;
                    alert('✅ Organization updated successfully!');
                } else {
                    console.error('Failed to update organization');
                    alert('❌ Error updating organization');
                }
            } catch (error) {
                console.error('Error updating organization:', error);
                alert('❌ Error updating organization');
            }
        },
        
        confirmDeleteOrganization(org) {
            console.log('Confirm delete organization:', org);
            this.deleteType = 'organization';
            this.deleteId = org.id;
            this.deleteMessage = `Are you sure you want to delete "${org.name}"? This will also delete all associated clusters and divisions.`;
            this.showDeleteModal = true;
        },
        
        // Cluster CRUD
        async createCluster() {
            if (!this.selectedOrganization) {
                alert('Please select an organization first');
                return;
            }
            
            console.log('Creating cluster:', this.newCluster);
            this.newCluster.organization_id = this.selectedOrganization.id;
            
            try {
                const response = await fetch('/api/clusters', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
                    },
                    body: JSON.stringify(this.newCluster)
                });
                
                console.log('Create cluster response status:', response.status);
                
                if (response.ok) {
                    const newCluster = await response.json();
                    console.log('Cluster created:', newCluster);
                    this.clusters.push(newCluster);
                    this.showCreateClusterModal = false;
                    this.newCluster = { name: '', cluster_chairman: '', organization_id: null };
                    alert('✅ Cluster created successfully!');
                } else {
                    console.error('Failed to create cluster');
                    alert('❌ Error creating cluster');
                }
            } catch (error) {
                console.error('Error creating cluster:', error);
                alert('❌ Error creating cluster');
            }
        },
        
        showEditClusterModal(cluster) {
            console.log('Editing cluster:', cluster);
            this.editCluster = { 
                id: cluster.id, 
                name: cluster.name, 
                cluster_chairman: cluster.cluster_chairman 
            };
            this.showEditClusterModal = true;
        },
        
        async updateCluster() {
            console.log('Updating cluster:', this.editCluster);
            try {
                const response = await fetch(`/api/clusters/${this.editCluster.id}`, {
                    method: 'PUT',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
                    },
                    body: JSON.stringify(this.editCluster)
                });
                
                console.log('Update cluster response status:', response.status);
                
                if (response.ok) {
                    const updatedCluster = await response.json();
                    console.log('Cluster updated:', updatedCluster);
                    const index = this.clusters.findIndex(c => c.id === updatedCluster.id);
                    if (index !== -1) {
                        this.clusters[index] = updatedCluster;
                    }
                    this.showEditClusterModal = false;
                    alert('✅ Cluster updated successfully!');
                } else {
                    console.error('Failed to update cluster');
                    alert('❌ Error updating cluster');
                }
            } catch (error) {
                console.error('Error updating cluster:', error);
                alert('❌ Error updating cluster');
            }
        },
        
        confirmDeleteCluster(cluster) {
            console.log('Confirm delete cluster:', cluster);
            this.deleteType = 'cluster';
            this.deleteId = cluster.id;
            this.deleteMessage = `Are you sure you want to delete "${cluster.name}"? This will also delete all associated divisions.`;
            this.showDeleteModal = true;
        },
        
        // Division CRUD
        async createDivision() {
            if (!this.newDivision.cluster_id) {
                alert('Please select a cluster first');
                return;
            }
            
            console.log('Creating division:', this.newDivision);
            
            try {
                const response = await fetch('/api/divisions', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
                    },
                    body: JSON.stringify(this.newDivision)
                });
                
                console.log('Create division response status:', response.status);
                
                if (response.ok) {
                    const newDivision = await response.json();
                    console.log('Division created:', newDivision);
                    this.divisions.push(newDivision);
                    this.showCreateDivisionModal = false;
                    this.newDivision = { name: '', division_chairman: '', cluster_id: '' };
                    alert('✅ Division created successfully!');
                } else {
                    console.error('Failed to create division');
                    alert('❌ Error creating division');
                }
            } catch (error) {
                console.error('Error creating division:', error);
                alert('❌ Error creating division');
            }
        },
        
        showEditDivisionModal(division) {
            console.log('Editing division:', division);
            this.editDivision = { 
                id: division.id, 
                name: division.name, 
                division_chairman: division.division_chairman,
                cluster_id: division.cluster_id
            };
            this.showEditDivisionModal = true;
        },
        
        async updateDivision() {
            console.log('Updating division:', this.editDivision);
            try {
                const response = await fetch(`/api/divisions/${this.editDivision.id}`, {
                    method: 'PUT',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
                    },
                    body: JSON.stringify(this.editDivision)
                });
                
                console.log('Update division response status:', response.status);
                
                if (response.ok) {
                    const updatedDivision = await response.json();
                    console.log('Division updated:', updatedDivision);
                    const index = this.divisions.findIndex(d => d.id === updatedDivision.id);
                    if (index !== -1) {
                        this.divisions[index] = updatedDivision;
                    }
                    this.showEditDivisionModal = false;
                    alert('✅ Division updated successfully!');
                } else {
                    console.error('Failed to update division');
                    alert('❌ Error updating division');
                }
            } catch (error) {
                console.error('Error updating division:', error);
                alert('❌ Error updating division');
            }
        },
        
        confirmDeleteDivision(division) {
            console.log('Confirm delete division:', division);
            this.deleteType = 'division';
            this.deleteId = division.id;
            this.deleteMessage = `Are you sure you want to delete "${division.name}"?`;
            this.showDeleteModal = true;
        },
        
        // Delete Execution
        async executeDelete() {
            console.log('Executing delete:', this.deleteType, this.deleteId);
            
            let endpoint, successMessage;
            
            switch (this.deleteType) {
                case 'organization':
                    endpoint = `/api/organizations/${this.deleteId}`;
                    successMessage = 'Organization deleted successfully!';
                    break;
                case 'cluster':
                    endpoint = `/api/clusters/${this.deleteId}`;
                    successMessage = 'Cluster deleted successfully!';
                    break;
                case 'division':
                    endpoint = `/api/divisions/${this.deleteId}`;
                    successMessage = 'Division deleted successfully!';
                    break;
                default:
                    console.error('Unknown delete type:', this.deleteType);
                    return;
            }
            
            try {
                const response = await fetch(endpoint, {
                    method: 'DELETE',
                    headers: {
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
                    }
                });
                
                console.log('Delete response status:', response.status);
                
                if (response.ok) {
                    // Update local state
                    switch (this.deleteType) {
                        case 'organization':
                            this.organizations = this.organizations.filter(o => o.id !== this.deleteId);
                            if (this.selectedOrganization && this.selectedOrganization.id === this.deleteId) {
                                this.selectedOrganization = this.organizations.length > 0 ? this.organizations[0] : null;
                                if (this.selectedOrganization) {
                                    this.loadClusters(this.selectedOrganization.id);
                                    this.loadDivisions(this.selectedOrganization.id);
                                } else {
                                    this.clusters = [];
                                    this.divisions = [];
                                }
                            }
                            break;
                        case 'cluster':
                            this.clusters = this.clusters.filter(c => c.id !== this.deleteId);
                            this.loadDivisions(this.selectedOrganization.id);
                            break;
                        case 'division':
                            this.divisions = this.divisions.filter(d => d.id !== this.deleteId);
                            break;
                    }
                    
                    this.showDeleteModal = false;
                    alert('✅ ' + successMessage);
                } else {
                    console.error('Failed to delete');
                    alert('❌ Error deleting');
                }
            } catch (error) {
                console.error('Error deleting:', error);
                alert('❌ Error deleting');
            }
        }
    };
};

// Debug
console.log('organizationManager available:', typeof window.organizationManager);
</script>

<style>
[x-cloak] { display: none !important; }
</style>
@endsection