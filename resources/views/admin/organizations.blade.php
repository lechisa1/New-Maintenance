@extends('layouts.app')

@section('content')
<x-common.page-breadcrumb pageTitle="Organization Management" />

<div class="grid grid-cols-1 gap-6 lg:grid-cols-3">
    <!-- Organizations List -->
    <div class="rounded-2xl border border-gray-200 bg-white p-6 dark:border-gray-800 dark:bg-white/[0.03]">
        <div class="mb-6 flex items-center justify-between">
            <h3 class="text-lg font-semibold text-gray-800 dark:text-white/90">
                <i class="bi bi-building me-2"></i>Organizations
            </h3>
            <button @click="activeTab = 'createOrg'" 
                class="inline-flex items-center justify-center rounded-lg bg-blue-500 px-3 py-2 text-xs font-semibold text-white shadow-theme-xs hover:bg-blue-600">
                <i class="bi bi-plus-lg me-1"></i>New
            </button>
        </div>

        <div x-data="{
            organizations: [
                {
                    id: 1,
                    name: 'TechCorp Solutions',
                    code: 'TCS',
                    description: 'Leading technology solutions provider',
                    sectors_count: 3,
                    is_active: true,
                    created_at: '2024-01-15'
                },
                {
                    id: 2,
                    name: 'Global Finance Group',
                    code: 'GFG',
                    description: 'International financial services',
                    sectors_count: 4,
                    is_active: true,
                    created_at: '2024-02-10'
                },
                {
                    id: 3,
                    name: 'HealthCare United',
                    code: 'HCU',
                    description: 'Healthcare and medical services',
                    sectors_count: 2,
                    is_active: true,
                    created_at: '2024-02-20'
                },
                {
                    id: 4,
                    name: 'EduFuture Academy',
                    code: 'EFA',
                    description: 'Educational services and training',
                    sectors_count: 3,
                    is_active: false,
                    created_at: '2024-03-05'
                }
            ],
            search: '',
            get filteredOrgs() {
                if (!this.search) return this.organizations;
                return this.organizations.filter(org => 
                    org.name.toLowerCase().includes(this.search.toLowerCase()) ||
                    org.code.toLowerCase().includes(this.search.toLowerCase()) ||
                    org.description.toLowerCase().includes(this.search.toLowerCase())
                );
            }
        }">
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
                <template x-for="org in filteredOrgs" :key="org.id">
                    <div class="rounded-lg border border-gray-200 p-3 hover:border-blue-300 hover:bg-blue-50/50 dark:border-gray-700 dark:hover:border-blue-800 dark:hover:bg-blue-900/20"
                         @click="activeTab = 'viewOrg'; selectedOrg = org; loadSectors(org.id)">
                        <div class="flex items-start justify-between">
                            <div class="flex-1">
                                <div class="flex items-center gap-2">
                                    <h4 class="font-medium text-gray-800 dark:text-white/90" x-text="org.name"></h4>
                                    <span class="rounded-full bg-gray-100 px-2 py-0.5 text-xs font-medium text-gray-600 dark:bg-gray-800 dark:text-gray-400" 
                                          x-text="org.code"></span>
                                    <span x-show="org.is_active" 
                                          class="rounded-full bg-green-100 px-2 py-0.5 text-xs font-medium text-green-800 dark:bg-green-900 dark:text-green-200">
                                        Active
                                    </span>
                                    <span x-show="!org.is_active" 
                                          class="rounded-full bg-red-100 px-2 py-0.5 text-xs font-medium text-red-800 dark:bg-red-900 dark:text-red-200">
                                        Inactive
                                    </span>
                                </div>
                                <p class="mt-1 text-sm text-gray-500 dark:text-gray-400" 
                                   x-text="org.description"></p>
                                <div class="mt-2 flex items-center gap-3">
                                    <span class="flex items-center gap-1 text-xs text-gray-500 dark:text-gray-400">
                                        <i class="bi bi-diagram-3"></i>
                                        <span x-text="org.sectors_count"></span> sectors
                                    </span>
                                    <span class="text-xs text-gray-500 dark:text-gray-400" 
                                          x-text="new Date(org.created_at).toLocaleDateString()"></span>
                                </div>
                            </div>
                            <div class="ml-2 flex gap-1">
                                <button @click.stop="activeTab = 'editOrg'; editOrganization(org)"
                                    class="rounded p-1.5 text-gray-400 hover:bg-gray-100 hover:text-blue-600 dark:hover:bg-gray-800 dark:hover:text-blue-500">
                                    <i class="bi bi-pencil text-xs"></i>
                                </button>
                                <button @click.stop="if(confirm('Delete ' + org.name + '?')) deleteOrganization(org.id)"
                                    class="rounded p-1.5 text-gray-400 hover:bg-gray-100 hover:text-red-600 dark:hover:bg-gray-800 dark:hover:text-red-500">
                                    <i class="bi bi-trash text-xs"></i>
                                </button>
                            </div>
                        </div>
                    </div>
                </template>
            </div>
        </div>
    </div>

    <!-- Main Content Area -->
    <div class="lg:col-span-2">
        <div x-data="{
            activeTab: 'viewOrg',
            selectedOrg: null,
            sectors: [],
            divisions: [],
            selectedSector: null,
            
            // Sample data for forms
            formData: {
                org: { name: '', code: '', description: '', is_active: true },
                sector: { name: '', description: '', org_id: '' },
                division: { name: '', description: '', sector_id: '' }
            },
            
            init() {
                // Initialize with first organization
                if (this.organizations && this.organizations.length > 0) {
                    this.selectedOrg = this.organizations[0];
                    this.loadSectors(this.selectedOrg.id);
                }
            },
            
            loadSectors(orgId) {
                // Sample sectors data
                this.sectors = [
                    { id: 1, org_id: 1, name: 'Technology', description: 'IT and Software Development', divisions_count: 3 },
                    { id: 2, org_id: 1, name: 'Operations', description: 'Business operations and support', divisions_count: 2 },
                    { id: 3, org_id: 1, name: 'Sales & Marketing', description: 'Sales and marketing activities', divisions_count: 2 },
                    { id: 4, org_id: 2, name: 'Investment Banking', description: 'Investment and banking services', divisions_count: 3 },
                    { id: 5, org_id: 2, name: 'Retail Banking', description: 'Customer banking services', divisions_count: 2 }
                ].filter(s => s.org_id == orgId);
            },
            
            loadDivisions(sectorId) {
                // Sample divisions data
                this.divisions = [
                    { id: 1, sector_id: 1, name: 'Software Development', description: 'Application development team' },
                    { id: 2, sector_id: 1, name: 'IT Infrastructure', description: 'Network and server management' },
                    { id: 3, sector_id: 1, name: 'Quality Assurance', description: 'Testing and quality control' },
                    { id: 4, sector_id: 2, name: 'Customer Support', description: 'Customer service and support' },
                    { id: 5, sector_id: 2, name: 'Logistics', description: 'Supply chain and logistics' },
                    { id: 6, sector_id: 3, name: 'Digital Marketing', description: 'Online marketing campaigns' },
                    { id: 7, sector_id: 3, name: 'Sales Team', description: 'Sales and business development' }
                ].filter(d => d.sector_id == sectorId);
            },
            
            selectSector(sector) {
                this.selectedSector = sector;
                this.loadDivisions(sector.id);
            },
            
            createOrganization() {
                const org = {
                    id: Date.now(),
                    ...this.formData.org,
                    sectors_count: 0,
                    created_at: new Date().toISOString().split('T')[0]
                };
                this.organizations.push(org);
                this.selectedOrg = org;
                this.formData.org = { name: '', code: '', description: '', is_active: true };
                this.activeTab = 'viewOrg';
                alert('Organization created successfully!');
            },
            
            editOrganization(org) {
                this.formData.org = { ...org };
                this.activeTab = 'editOrg';
            },
            
            updateOrganization() {
                const index = this.organizations.findIndex(o => o.id === this.selectedOrg.id);
                if (index !== -1) {
                    this.organizations[index] = { ...this.organizations[index], ...this.formData.org };
                    this.selectedOrg = this.organizations[index];
                    alert('Organization updated successfully!');
                    this.activeTab = 'viewOrg';
                }
            },
            
            deleteOrganization(orgId) {
                this.organizations = this.organizations.filter(o => o.id !== orgId);
                if (this.selectedOrg && this.selectedOrg.id === orgId) {
                    this.selectedOrg = this.organizations.length > 0 ? this.organizations[0] : null;
                    if (this.selectedOrg) this.loadSectors(this.selectedOrg.id);
                }
                alert('Organization deleted!');
            },
            
            createSector() {
                if (!this.selectedOrg) {
                    alert('Please select an organization first!');
                    return;
                }
                
                const sector = {
                    id: Date.now(),
                    org_id: this.selectedOrg.id,
                    ...this.formData.sector,
                    divisions_count: 0
                };
                this.sectors.push(sector);
                this.formData.sector = { name: '', description: '', org_id: this.selectedOrg.id };
                this.selectedOrg.sectors_count++;
                alert('Sector created successfully!');
            },
            
            createDivision() {
                if (!this.selectedSector) {
                    alert('Please select a sector first!');
                    return;
                }
                
                const division = {
                    id: Date.now(),
                    sector_id: this.selectedSector.id,
                    ...this.formData.division
                };
                this.divisions.push(division);
                this.formData.division = { name: '', description: '', sector_id: this.selectedSector.id };
                this.selectedSector.divisions_count++;
                alert('Division created successfully!');
            }
        }">
            <!-- Tabs Navigation -->
            <div class="mb-6 border-b border-gray-200 dark:border-gray-700">
                <nav class="-mb-px flex space-x-6">
                    <button @click="activeTab = 'viewOrg'" 
                        :class="activeTab === 'viewOrg' ? 'border-blue-500 text-blue-600 dark:border-blue-500 dark:text-blue-400' : 'border-transparent text-gray-500 hover:border-gray-300 hover:text-gray-700 dark:text-gray-400 dark:hover:text-gray-300'"
                        class="whitespace-nowrap border-b-2 px-1 py-3 text-sm font-medium">
                        <i class="bi bi-eye me-2"></i>View Organization
                    </button>
                    <button @click="activeTab = 'createOrg'; formData.org = { name: '', code: '', description: '', is_active: true }" 
                        :class="activeTab === 'createOrg' ? 'border-blue-500 text-blue-600 dark:border-blue-500 dark:text-blue-400' : 'border-transparent text-gray-500 hover:border-gray-300 hover:text-gray-700 dark:text-gray-400 dark:hover:text-gray-300'"
                        class="whitespace-nowrap border-b-2 px-1 py-3 text-sm font-medium">
                        <i class="bi bi-plus-circle me-2"></i>Create Organization
                    </button>
                    <button @click="activeTab = 'createSector'; if(selectedOrg) formData.sector.org_id = selectedOrg.id" 
                        :class="activeTab === 'createSector' ? 'border-blue-500 text-blue-600 dark:border-blue-500 dark:text-blue-400' : 'border-transparent text-gray-500 hover:border-gray-300 hover:text-gray-700 dark:text-gray-400 dark:hover:text-gray-300'"
                        class="whitespace-nowrap border-b-2 px-1 py-3 text-sm font-medium">
                        <i class="bi bi-diagram-3 me-2"></i>Create Sector
                    </button>
                    <button @click="activeTab = 'createDivision'; if(selectedSector) formData.division.sector_id = selectedSector.id" 
                        :class="activeTab === 'createDivision' ? 'border-blue-500 text-blue-600 dark:border-blue-500 dark:text-blue-400' : 'border-transparent text-gray-500 hover:border-gray-300 hover:text-gray-700 dark:text-gray-400 dark:hover:text-gray-300'"
                        class="whitespace-nowrap border-b-2 px-1 py-3 text-sm font-medium">
                        <i class="bi bi-layers me-2"></i>Create Division
                    </button>
                </nav>
            </div>

            <!-- View Organization Tab -->
            <div x-show="activeTab === 'viewOrg'" class="space-y-6">
                <template x-if="selectedOrg">
                    <div class="rounded-2xl border border-gray-200 bg-white p-6 dark:border-gray-800 dark:bg-white/[0.03]">
                        <div class="mb-6 flex items-start justify-between">
                            <div>
                                <h3 class="text-lg font-semibold text-gray-800 dark:text-white/90" 
                                    x-text="selectedOrg.name"></h3>
                                <div class="mt-2 flex items-center gap-3">
                                    <span class="rounded-full bg-gray-100 px-3 py-1 text-sm font-medium text-gray-600 dark:bg-gray-800 dark:text-gray-400"
                                          x-text="selectedOrg.code"></span>
                                    <span :class="selectedOrg.is_active ? 'bg-green-100 text-green-800 dark:bg-green-900 dark:text-green-200' : 'bg-red-100 text-red-800 dark:bg-red-900 dark:text-red-200'"
                                          class="rounded-full px-3 py-1 text-sm font-medium"
                                          x-text="selectedOrg.is_active ? 'Active' : 'Inactive'"></span>
                                </div>
                                <p class="mt-3 text-gray-600 dark:text-gray-400" 
                                   x-text="selectedOrg.description"></p>
                            </div>
                            <button @click="activeTab = 'editOrg'; editOrganization(selectedOrg)"
                                class="rounded-lg border border-gray-300 bg-white px-4 py-2 text-sm font-medium text-gray-700 shadow-theme-xs hover:bg-gray-50 dark:border-gray-700 dark:bg-gray-800 dark:text-gray-400 dark:hover:bg-white/[0.03]">
                                <i class="bi bi-pencil me-2"></i>Edit
                            </button>
                        </div>

                        <!-- Sectors -->
                        <div>
                            <div class="mb-4 flex items-center justify-between">
                                <h4 class="text-sm font-semibold text-gray-800 dark:text-white/90">Sectors</h4>
                                <button @click="activeTab = 'createSector'; formData.sector.org_id = selectedOrg.id"
                                    class="rounded-lg border border-gray-300 bg-white px-3 py-1.5 text-xs font-medium text-gray-700 shadow-theme-xs hover:bg-gray-50 dark:border-gray-700 dark:bg-gray-800 dark:text-gray-400 dark:hover:bg-white/[0.03]">
                                    <i class="bi bi-plus me-1"></i>Add Sector
                                </button>
                            </div>
                            
                            <div class="grid grid-cols-1 gap-3 md:grid-cols-2">
                                <template x-for="sector in sectors" :key="sector.id">
                                    <div class="rounded-lg border border-gray-200 p-4 hover:border-blue-300 hover:bg-blue-50/50 dark:border-gray-700 dark:hover:border-blue-800 dark:hover:bg-blue-900/20"
                                         @click="selectSector(sector)">
                                        <div class="flex items-start justify-between">
                                            <div>
                                                <h5 class="font-medium text-gray-800 dark:text-white/90" 
                                                    x-text="sector.name"></h5>
                                                <p class="mt-1 text-sm text-gray-500 dark:text-gray-400" 
                                                   x-text="sector.description"></p>
                                                <div class="mt-2 flex items-center gap-2">
                                                    <span class="text-xs text-gray-500 dark:text-gray-400">
                                                        <i class="bi bi-layers me-1"></i>
                                                        <span x-text="sector.divisions_count"></span> divisions
                                                    </span>
                                                </div>
                                            </div>
                                            <i class="bi bi-chevron-right text-gray-400"></i>
                                        </div>
                                    </div>
                                </template>
                            </div>
                        </div>

                        <!-- Selected Sector Divisions -->
                        <template x-if="selectedSector">
                            <div class="mt-8">
                                <div class="mb-4 flex items-center justify-between">
                                    <div>
                                        <h4 class="text-sm font-semibold text-gray-800 dark:text-white/90">
                                            Divisions in <span x-text="selectedSector.name"></span>
                                        </h4>
                                        <p class="mt-1 text-sm text-gray-500 dark:text-gray-400" 
                                           x-text="selectedSector.description"></p>
                                    </div>
                                    <button @click="activeTab = 'createDivision'; formData.division.sector_id = selectedSector.id"
                                        class="rounded-lg border border-gray-300 bg-white px-3 py-1.5 text-xs font-medium text-gray-700 shadow-theme-xs hover:bg-gray-50 dark:border-gray-700 dark:bg-gray-800 dark:text-gray-400 dark:hover:bg-white/[0.03]">
                                        <i class="bi bi-plus me-1"></i>Add Division
                                    </button>
                                </div>
                                
                                <div class="grid grid-cols-1 gap-3">
                                    <template x-for="division in divisions" :key="division.id">
                                        <div class="rounded-lg border border-gray-200 p-3 dark:border-gray-700">
                                            <h6 class="font-medium text-gray-800 dark:text-white/90" 
                                                x-text="division.name"></h6>
                                            <p class="mt-1 text-sm text-gray-500 dark:text-gray-400" 
                                               x-text="division.description"></p>
                                        </div>
                                    </template>
                                </div>
                            </div>
                        </template>
                    </div>
                </template>
                
                <template x-if="!selectedOrg">
                    <div class="rounded-2xl border border-gray-200 bg-white p-12 text-center dark:border-gray-800 dark:bg-white/[0.03]">
                        <i class="bi bi-building text-4xl text-gray-300 dark:text-gray-600"></i>
                        <p class="mt-4 text-gray-500 dark:text-gray-400">Select an organization to view details</p>
                    </div>
                </template>
            </div>

            <!-- Create/Edit Organization Tab -->
            <div x-show="activeTab === 'createOrg' || activeTab === 'editOrg'" 
                 class="rounded-2xl border border-gray-200 bg-white p-6 dark:border-gray-800 dark:bg-white/[0.03]">
                <h3 class="mb-6 text-lg font-semibold text-gray-800 dark:text-white/90">
                    <i class="bi" :class="activeTab === 'createOrg' ? 'bi-plus-circle' : 'bi-pencil'"></i>
                    <span x-text="activeTab === 'createOrg' ? 'Create New Organization' : 'Edit Organization'"></span>
                </h3>
                
                <div class="space-y-5">
                    <div class="grid grid-cols-1 gap-5 md:grid-cols-2">
                        <div>
                            <label class="mb-2 block text-sm font-medium text-gray-700 dark:text-gray-300">
                                Organization Name <span class="text-red-500">*</span>
                            </label>
                            <input type="text" x-model="formData.org.name" required
                                class="h-11 w-full rounded-lg border border-gray-300 bg-transparent px-4 py-2.5 text-sm text-gray-800 shadow-theme-xs placeholder:text-gray-400 focus:border-blue-300 focus:outline-none focus:ring-2 focus:ring-blue-500/10 dark:border-gray-700 dark:bg-gray-900 dark:text-white/90 dark:placeholder:text-white/30 dark:focus:border-blue-800"
                                placeholder="e.g., TechCorp Solutions">
                        </div>
                        
                        <div>
                            <label class="mb-2 block text-sm font-medium text-gray-700 dark:text-gray-300">
                                Organization Code <span class="text-red-500">*</span>
                            </label>
                            <input type="text" x-model="formData.org.code" required
                                class="h-11 w-full rounded-lg border border-gray-300 bg-transparent px-4 py-2.5 text-sm text-gray-800 shadow-theme-xs placeholder:text-gray-400 focus:border-blue-300 focus:outline-none focus:ring-2 focus:ring-blue-500/10 dark:border-gray-700 dark:bg-gray-900 dark:text-white/90 dark:placeholder:text-white/30 dark:focus:border-blue-800"
                                placeholder="e.g., TCS">
                        </div>
                    </div>
                    
                    <div>
                        <label class="mb-2 block text-sm font-medium text-gray-700 dark:text-gray-300">
                            Description
                        </label>
                        <textarea x-model="formData.org.description" rows="3"
                            class="w-full rounded-lg border border-gray-300 bg-transparent px-4 py-2.5 text-sm text-gray-800 shadow-theme-xs placeholder:text-gray-400 focus:border-blue-300 focus:outline-none focus:ring-2 focus:ring-blue-500/10 dark:border-gray-700 dark:bg-gray-900 dark:text-white/90 dark:placeholder:text-white/30 dark:focus:border-blue-800"
                            placeholder="Describe the organization..."></textarea>
                    </div>
                    
                    <div class="flex items-center">
                        <input type="checkbox" x-model="formData.org.is_active" id="org_active"
                            class="h-4 w-4 rounded border-gray-300 text-blue-600 focus:ring-blue-500 dark:border-gray-600 dark:bg-gray-800 dark:focus:ring-blue-600">
                        <label for="org_active" class="ml-2 text-sm text-gray-700 dark:text-gray-300">
                            Organization is active
                        </label>
                    </div>
                    
                    <div class="flex justify-end gap-3 pt-4">
                        <button type="button" @click="activeTab = 'viewOrg'"
                            class="rounded-lg border border-gray-300 bg-white px-6 py-3 text-sm font-semibold text-gray-700 shadow-theme-xs hover:bg-gray-50 dark:border-gray-700 dark:bg-gray-800 dark:text-gray-400 dark:hover:bg-white/[0.03]">
                            Cancel
                        </button>
                        <button type="button" @click="activeTab === 'createOrg' ? createOrganization() : updateOrganization()"
                            class="rounded-lg bg-blue-500 px-6 py-3 text-sm font-semibold text-white shadow-theme-xs hover:bg-blue-600">
                            <span x-text="activeTab === 'createOrg' ? 'Create Organization' : 'Update Organization'"></span>
                        </button>
                    </div>
                </div>
            </div>

            <!-- Create Sector Tab -->
            <div x-show="activeTab === 'createSector'" 
                 class="rounded-2xl border border-gray-200 bg-white p-6 dark:border-gray-800 dark:bg-white/[0.03]">
                <h3 class="mb-6 text-lg font-semibold text-gray-800 dark:text-white/90">
                    <i class="bi bi-diagram-3 me-2"></i>Create New Sector
                </h3>
                
                <div class="space-y-5">
                    <div>
                        <label class="mb-2 block text-sm font-medium text-gray-700 dark:text-gray-300">
                            Organization
                        </label>
                        <div class="rounded-lg border border-gray-300 bg-gray-50 p-3 dark:border-gray-700 dark:bg-gray-800">
                            <template x-if="selectedOrg">
                                <div>
                                    <div class="font-medium text-gray-800 dark:text-white/90" 
                                         x-text="selectedOrg.name"></div>
                                    <div class="text-sm text-gray-500 dark:text-gray-400" 
                                         x-text="selectedOrg.code"></div>
                                </div>
                            </template>
                            <template x-if="!selectedOrg">
                                <div class="text-gray-500 dark:text-gray-400">No organization selected</div>
                            </template>
                        </div>
                    </div>
                    
                    <div>
                        <label class="mb-2 block text-sm font-medium text-gray-700 dark:text-gray-300">
                            Sector Name <span class="text-red-500">*</span>
                        </label>
                        <input type="text" x-model="formData.sector.name" required
                            class="h-11 w-full rounded-lg border border-gray-300 bg-transparent px-4 py-2.5 text-sm text-gray-800 shadow-theme-xs placeholder:text-gray-400 focus:border-blue-300 focus:outline-none focus:ring-2 focus:ring-blue-500/10 dark:border-gray-700 dark:bg-gray-900 dark:text-white/90 dark:placeholder:text-white/30 dark:focus:border-blue-800"
                            placeholder="e.g., Technology, Finance, Operations">
                    </div>
                    
                    <div>
                        <label class="mb-2 block text-sm font-medium text-gray-700 dark:text-gray-300">
                            Description
                        </label>
                        <textarea x-model="formData.sector.description" rows="3"
                            class="w-full rounded-lg border border-gray-300 bg-transparent px-4 py-2.5 text-sm text-gray-800 shadow-theme-xs placeholder:text-gray-400 focus:border-blue-300 focus:outline-none focus:ring-2 focus:ring-blue-500/10 dark:border-gray-700 dark:bg-gray-900 dark:text-white/90 dark:placeholder:text-white/30 dark:focus:border-blue-800"
                            placeholder="Describe the sector's purpose and functions..."></textarea>
                    </div>
                    
                    <div class="flex justify-end gap-3 pt-4">
                        <button type="button" @click="activeTab = 'viewOrg'"
                            class="rounded-lg border border-gray-300 bg-white px-6 py-3 text-sm font-semibold text-gray-700 shadow-theme-xs hover:bg-gray-50 dark:border-gray-700 dark:bg-gray-800 dark:text-gray-400 dark:hover:bg-white/[0.03]">
                            Cancel
                        </button>
                        <button type="button" @click="createSector()"
                            class="rounded-lg bg-blue-500 px-6 py-3 text-sm font-semibold text-white shadow-theme-xs hover:bg-blue-600">
                            Create Sector
                        </button>
                    </div>
                </div>
            </div>

            <!-- Create Division Tab -->
            <div x-show="activeTab === 'createDivision'" 
                 class="rounded-2xl border border-gray-200 bg-white p-6 dark:border-gray-800 dark:bg-white/[0.03]">
                <h3 class="mb-6 text-lg font-semibold text-gray-800 dark:text-white/90">
                    <i class="bi bi-layers me-2"></i>Create New Division
                </h3>
                
                <div class="space-y-5">
                    <div>
                        <label class="mb-2 block text-sm font-medium text-gray-700 dark:text-gray-300">
                            Sector
                        </label>
                        <div class="rounded-lg border border-gray-300 bg-gray-50 p-3 dark:border-gray-700 dark:bg-gray-800">
                            <template x-if="selectedSector">
                                <div>
                                    <div class="font-medium text-gray-800 dark:text-white/90" 
                                         x-text="selectedSector.name"></div>
                                    <div class="text-sm text-gray-500 dark:text-gray-400" 
                                         x-text="selectedSector.description"></div>
                                </div>
                            </template>
                            <template x-if="!selectedSector">
                                <div class="text-gray-500 dark:text-gray-400">No sector selected</div>
                            </template>
                        </div>
                    </div>
                    
                    <div>
                        <label class="mb-2 block text-sm font-medium text-gray-700 dark:text-gray-300">
                            Division Name <span class="text-red-500">*</span>
                        </label>
                        <input type="text" x-model="formData.division.name" required
                            class="h-11 w-full rounded-lg border border-gray-300 bg-transparent px-4 py-2.5 text-sm text-gray-800 shadow-theme-xs placeholder:text-gray-400 focus:border-blue-300 focus:outline-none focus:ring-2 focus:ring-blue-500/10 dark:border-gray-700 dark:bg-gray-900 dark:text-white/90 dark:placeholder:text-white/30 dark:focus:border-blue-800"
                            placeholder="e.g., Software Development, Sales Team, Customer Support">
                    </div>
                    
                    <div>
                        <label class="mb-2 block text-sm font-medium text-gray-700 dark:text-gray-300">
                            Description
                        </label>
                        <textarea x-model="formData.division.description" rows="3"
                            class="w-full rounded-lg border border-gray-300 bg-transparent px-4 py-2.5 text-sm text-gray-800 shadow-theme-xs placeholder:text-gray-400 focus:border-blue-300 focus:outline-none focus:ring-2 focus:ring-blue-500/10 dark:border-gray-700 dark:bg-gray-900 dark:text-white/90 dark:placeholder:text-white/30 dark:focus:border-blue-800"
                            placeholder="Describe the division's responsibilities..."></textarea>
                    </div>
                    
                    <div class="flex justify-end gap-3 pt-4">
                        <button type="button" @click="activeTab = 'viewOrg'"
                            class="rounded-lg border border-gray-300 bg-white px-6 py-3 text-sm font-semibold text-gray-700 shadow-theme-xs hover:bg-gray-50 dark:border-gray-700 dark:bg-gray-800 dark:text-gray-400 dark:hover:bg-white/[0.03]">
                            Cancel
                        </button>
                        <button type="button" @click="createDivision()"
                            class="rounded-lg bg-blue-500 px-6 py-3 text-sm font-semibold text-white shadow-theme-xs hover:bg-blue-600">
                            Create Division
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Demo Notice -->
<div class="mt-6 rounded-lg border border-blue-200 bg-blue-50 p-4 dark:border-blue-800 dark:bg-blue-900/20">
    <div class="flex items-start">
        <i class="bi bi-info-circle-fill me-2 mt-0.5 text-blue-500"></i>
        <div>
            <h4 class="text-sm font-medium text-blue-800 dark:text-blue-200">Organization Management Demo</h4>
            <p class="mt-1 text-sm text-blue-700 dark:text-blue-300">
                This is a demonstration view. All data is hardcoded and operates in-memory. 
                Organizations → Sectors → Divisions hierarchy is fully functional. 
                Create, edit, and delete operations work locally without database interaction.
            </p>
        </div>
    </div>
</div>
@endsection