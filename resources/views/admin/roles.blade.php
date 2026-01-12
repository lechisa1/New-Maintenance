@extends('layouts.app')

@section('content')
    <x-common.page-breadcrumb pageTitle="Role Management" />
    
    <div class="grid grid-cols-1 gap-6 lg:grid-cols-2">
        <!-- Create/Edit Role Form -->
        <div class="rounded-2xl border border-gray-200 bg-white p-6 dark:border-gray-800 dark:bg-white/[0.03]">
            <h3 class="mb-6 text-lg font-semibold text-gray-800 dark:text-white/90">
                <i class="bi bi-shield-lock me-2"></i>
                <span x-text="editingRole ? 'Edit Role' : 'Create New Role'"></span>
            </h3>
            <hr class="mb-6 border-gray-200 dark:border-gray-700">

            <form x-data="{
                editingRole: false,
                roleName: '',
                roleDescription: '',
                selectedPermissions: [],
                allPermissions: [
                    {id: 1, name: 'users.view', description: 'View users'},
                    {id: 2, name: 'users.create', description: 'Create users'},
                    {id: 3, name: 'users.edit', description: 'Edit users'},
                    {id: 4, name: 'users.delete', description: 'Delete users'},
                    {id: 5, name: 'roles.manage', description: 'Manage roles and permissions'},
                    {id: 6, name: 'settings.view', description: 'View system settings'},
                    {id: 7, name: 'settings.edit', description: 'Edit system settings'},
                    {id: 8, name: 'reports.view', description: 'View reports'},
                    {id: 9, name: 'reports.generate', description: 'Generate reports'},
                    {id: 10, name: 'content.create', description: 'Create content'},
                    {id: 11, name: 'content.edit', description: 'Edit content'},
                    {id: 12, name: 'content.delete', description: 'Delete content'},
                ],
                
                togglePermission(permissionId) {
                    const index = this.selectedPermissions.indexOf(permissionId);
                    if (index === -1) {
                        this.selectedPermissions.push(permissionId);
                    } else {
                        this.selectedPermissions.splice(index, 1);
                    }
                },
                
                selectAllPermissions() {
                    this.selectedPermissions = this.allPermissions.map(p => p.id);
                },
                
                clearAllPermissions() {
                    this.selectedPermissions = [];
                },
                
                submitForm() {
                    // Show success message (in real app, this would submit to server)
                    alert('Role saved successfully! (This is a demo)');
                    console.log('Role Name:', this.roleName);
                    console.log('Role Description:', this.roleDescription);
                    console.log('Selected Permissions:', this.selectedPermissions);
                    
                    // Reset form
                    if (!this.editingRole) {
                        this.roleName = '';
                        this.roleDescription = '';
                        this.selectedPermissions = [];
                    }
                }
            }" @submit.prevent="submitForm">
                @csrf

                <div class="space-y-6">
                    <!-- Role Information Section -->
                    <div>
                        <h4 class="mb-4 text-sm font-semibold text-gray-800 dark:text-white/90">Role Information</h4>
                        <div class="grid grid-cols-1 gap-5">
                            <!-- Role Name -->
                            <div>
                                <label class="mb-2 block text-sm font-medium text-gray-700 dark:text-gray-300">
                                    Role Name <span class="text-red-500">*</span>
                                </label>
                                <input type="text" x-model="roleName" required
                                    class="h-11 w-full rounded-lg border border-gray-300 bg-transparent px-4 py-2.5 text-sm text-gray-800 shadow-theme-xs placeholder:text-gray-400 focus:border-blue-300 focus:outline-none focus:ring-2 focus:ring-blue-500/10 dark:border-gray-700 dark:bg-gray-900 dark:text-white/90 dark:placeholder:text-white/30 dark:focus:border-blue-800"
                                    placeholder="e.g., Administrator, Editor, Viewer">
                            </div>

                            <!-- Role Description -->
                            <div>
                                <label class="mb-2 block text-sm font-medium text-gray-700 dark:text-gray-300">
                                    Description
                                </label>
                                <textarea x-model="roleDescription" rows="3"
                                    class="w-full rounded-lg border border-gray-300 bg-transparent px-4 py-2.5 text-sm text-gray-800 shadow-theme-xs placeholder:text-gray-400 focus:border-blue-300 focus:outline-none focus:ring-2 focus:ring-blue-500/10 dark:border-gray-700 dark:bg-gray-900 dark:text-white/90 dark:placeholder:text-white/30 dark:focus:border-blue-800"
                                    placeholder="Describe the role's purpose and responsibilities"></textarea>
                            </div>
                        </div>
                    </div>

                    <!-- Permissions Section -->
                    <div>
                        <div class="mb-4 flex items-center justify-between">
                            <h4 class="text-sm font-semibold text-gray-800 dark:text-white/90">Permissions</h4>
                            <div class="flex gap-2">
                                <button type="button" @click="selectAllPermissions"
                                    class="rounded-lg border border-gray-300 bg-white px-3 py-1.5 text-xs font-medium text-gray-700 shadow-theme-xs hover:bg-gray-50 hover:text-gray-800 dark:border-gray-700 dark:bg-gray-800 dark:text-gray-400 dark:hover:bg-white/[0.03] dark:hover:text-gray-200">
                                    Select All
                                </button>
                                <button type="button" @click="clearAllPermissions"
                                    class="rounded-lg border border-gray-300 bg-white px-3 py-1.5 text-xs font-medium text-gray-700 shadow-theme-xs hover:bg-gray-50 hover:text-gray-800 dark:border-gray-700 dark:bg-gray-800 dark:text-gray-400 dark:hover:bg-white/[0.03] dark:hover:text-gray-200">
                                    Clear All
                                </button>
                            </div>
                        </div>

                        <div class="grid grid-cols-1 gap-4 md:grid-cols-2 lg:grid-cols-3">
                            <template x-for="permission in allPermissions" :key="permission.id">
                                <div class="flex items-start">
                                    <div class="flex h-5 items-center">
                                        <input type="checkbox" :id="'permission_' + permission.id" 
                                            :value="permission.id"
                                            :checked="selectedPermissions.includes(permission.id)"
                                            @change="togglePermission(permission.id)"
                                            class="h-4 w-4 rounded border-gray-300 text-blue-600 focus:ring-blue-500 dark:border-gray-600 dark:bg-gray-800 dark:focus:ring-blue-600">
                                    </div>
                                    <div class="ml-3 text-sm">
                                        <label :for="'permission_' + permission.id" 
                                            class="font-medium text-gray-700 dark:text-gray-300" 
                                            x-text="permission.name.replace('.', ' ')"></label>
                                        <p class="text-gray-500 dark:text-gray-400" 
                                            x-text="permission.description"></p>
                                    </div>
                                </div>
                            </template>
                        </div>
                        
                        <div class="mt-4 text-sm text-gray-500 dark:text-gray-400">
                            <span x-text="selectedPermissions.length"></span> of 
                            <span x-text="allPermissions.length"></span> permissions selected
                        </div>
                    </div>

                    <!-- Submit Button -->
                    <div class="border-t border-gray-200 pt-6 dark:border-gray-700">
                        <div class="flex justify-end gap-3">
                            <button type="button" @click="editingRole = !editingRole; roleName = editingRole ? 'Administrator' : ''; roleDescription = editingRole ? 'Full system access with all permissions' : ''; selectedPermissions = editingRole ? [1,2,3,4,5,6,7,8,9,10,11,12] : []"
                                class="inline-flex items-center justify-center rounded-lg border border-gray-300 bg-white px-6 py-3 text-sm font-semibold text-gray-700 shadow-theme-xs hover:bg-gray-50 hover:text-gray-800 dark:border-gray-700 dark:bg-gray-800 dark:text-gray-400 dark:hover:bg-white/[0.03] dark:hover:text-gray-200">
                                <span x-text="editingRole ? 'Switch to Create' : 'Switch to Edit Demo'"></span>
                            </button>
                            <button type="submit"
                                class="inline-flex items-center justify-center rounded-lg bg-blue-500 px-6 py-3 text-sm font-semibold text-white shadow-theme-xs hover:bg-blue-600 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:ring-offset-2 dark:focus:ring-offset-gray-900">
                                <i class="bi bi-save me-2"></i>
                                <span x-text="editingRole ? 'Update Role' : 'Create Role'"></span>
                            </button>
                        </div>
                    </div>
                </div>
            </form>
        </div>

        <!-- Role List -->
        <div class="rounded-2xl border border-gray-200 bg-white p-6 dark:border-gray-800 dark:bg-white/[0.03]">
            <h3 class="mb-6 text-lg font-semibold text-gray-800 dark:text-white/90">
                <i class="bi bi-list-task me-2"></i>Existing Roles
            </h3>
            <hr class="mb-6 border-gray-200 dark:border-gray-700">

            <div x-data="{
                search: '',
                roles: [
                    {
                        id: 1,
                        name: 'admin',
                        description: 'Full system access with all permissions',
                        users_count: 3,
                        permissions_count: 12,
                        created_at: '2024-01-15'
                    },
                    {
                        id: 2,
                        name: 'manager',
                        description: 'Manage users and content, view reports',
                        users_count: 8,
                        permissions_count: 8,
                        created_at: '2024-01-20'
                    },
                    {
                        id: 3,
                        name: 'editor',
                        description: 'Create and edit content',
                        users_count: 15,
                        permissions_count: 5,
                        created_at: '2024-02-01'
                    },
                    {
                        id: 4,
                        name: 'viewer',
                        description: 'View content and reports only',
                        users_count: 25,
                        permissions_count: 3,
                        created_at: '2024-02-10'
                    },
                    {
                        id: 5,
                        name: 'moderator',
                        description: 'Moderate user content and comments',
                        users_count: 5,
                        permissions_count: 6,
                        created_at: '2024-02-15'
                    }
                ],
                
                get filteredRoles() {
                    if (!this.search) return this.roles;
                    return this.roles.filter(role => 
                        role.name.toLowerCase().includes(this.search.toLowerCase()) ||
                        role.description.toLowerCase().includes(this.search.toLowerCase())
                    );
                }
            }">
                <!-- Search -->
                <div class="mb-4">
                    <div class="relative">
                        <input type="text" x-model="search" placeholder="Search roles..."
                            class="h-11 w-full rounded-lg border border-gray-300 bg-transparent px-4 py-2.5 pl-11 text-sm text-gray-800 shadow-theme-xs placeholder:text-gray-400 focus:border-blue-300 focus:outline-none focus:ring-2 focus:ring-blue-500/10 dark:border-gray-700 dark:bg-gray-900 dark:text-white/90 dark:placeholder:text-white/30 dark:focus:border-blue-800">
                        <svg class="absolute left-4 top-1/2 -translate-y-1/2 fill-gray-500 dark:fill-gray-400" width="18" height="18" viewBox="0 0 18 18" fill="none">
                            <path fill-rule="evenodd" clip-rule="evenodd" d="M8.25 3C5.3505 3 3 5.3505 3 8.25C3 11.1495 5.3505 13.5 8.25 13.5C11.1495 13.5 13.5 11.1495 13.5 8.25C13.5 5.3505 11.1495 3 8.25 3ZM1.5 8.25C1.5 4.52208 4.52208 1.5 8.25 1.5C11.9779 1.5 15 4.52208 15 8.25C15 11.9779 11.9779 15 8.25 15C4.52208 15 1.5 11.9779 1.5 8.25Z" fill=""></path>
                            <path fill-rule="evenodd" clip-rule="evenodd" d="M11.9571 11.9571C12.25 11.6642 12.7249 11.6642 13.0178 11.9571L16.2803 15.2197C16.5732 15.5126 16.5732 15.9874 16.2803 16.2803C15.9874 16.5732 15.5126 16.5732 15.2197 16.2803L11.9571 13.0178C11.6642 12.7249 11.6642 12.25 11.9571 11.9571Z" fill=""></path>
                        </svg>
                    </div>
                </div>

                <!-- Role List -->
                <div class="space-y-3">
                    <template x-if="filteredRoles.length === 0">
                        <div class="rounded-lg border border-gray-200 bg-gray-50 p-6 text-center dark:border-gray-700 dark:bg-gray-800">
                            <p class="text-gray-500 dark:text-gray-400">No roles found</p>
                        </div>
                    </template>

                    <template x-for="role in filteredRoles" :key="role.id">
                        <div class="rounded-lg border border-gray-200 p-4 hover:border-blue-300 hover:bg-blue-50/50 dark:border-gray-700 dark:hover:border-blue-800 dark:hover:bg-blue-900/20">
                            <div class="flex items-start justify-between">
                                <div>
                                    <h4 class="font-medium text-gray-800 dark:text-white/90" x-text="role.name"></h4>
                                    <p class="mt-1 text-sm text-gray-500 dark:text-gray-400" x-text="role.description || 'No description'"></p>
                                    <div class="mt-2 flex flex-wrap gap-1">
                                        <span class="rounded-full bg-gray-100 px-2 py-1 text-xs text-gray-600 dark:bg-gray-800 dark:text-gray-400">
                                            <span x-text="role.users_count"></span> users
                                        </span>
                                        <span class="rounded-full bg-gray-100 px-2 py-1 text-xs text-gray-600 dark:bg-gray-800 dark:text-gray-400">
                                            <span x-text="role.permissions_count"></span> permissions
                                        </span>
                                        <span class="rounded-full bg-gray-100 px-2 py-1 text-xs text-gray-600 dark:bg-gray-800 dark:text-gray-400">
                                            <span x-text="new Date(role.created_at).toLocaleDateString('en-US', { month: 'short', day: 'numeric', year: 'numeric' })"></span>
                                        </span>
                                    </div>
                                </div>
                                <div class="flex gap-2">
                                    <button type="button" @click="alert('Edit role: ' + role.name)"
                                        class="rounded-lg border border-gray-300 bg-white p-2 text-gray-500 hover:bg-gray-50 hover:text-blue-600 dark:border-gray-700 dark:bg-gray-800 dark:text-gray-400 dark:hover:bg-white/[0.03] dark:hover:text-blue-500">
                                        <i class="bi bi-pencil"></i>
                                    </button>
                                    <button type="button" @click="if(confirm('Are you sure you want to delete ' + role.name + ' role?')) { roles = roles.filter(r => r.id !== role.id); alert('Role deleted! (This is a demo)'); }"
                                        class="rounded-lg border border-gray-300 bg-white p-2 text-gray-500 hover:bg-gray-50 hover:text-red-600 dark:border-gray-700 dark:bg-gray-800 dark:text-gray-400 dark:hover:bg-white/[0.03] dark:hover:text-red-500">
                                        <i class="bi bi-trash"></i>
                                    </button>
                                </div>
                            </div>
                        </div>
                    </template>
                </div>

                <!-- Stats -->
                <div class="mt-6 grid grid-cols-2 gap-4">
                    <div class="rounded-lg border border-gray-200 bg-gray-50 p-4 dark:border-gray-700 dark:bg-gray-800">
                        <div class="text-sm text-gray-500 dark:text-gray-400">Total Roles</div>
                        <div class="mt-1 text-2xl font-semibold text-gray-800 dark:text-white/90" x-text="roles.length"></div>
                    </div>
                    <div class="rounded-lg border border-gray-200 bg-gray-50 p-4 dark:border-gray-700 dark:bg-gray-800">
                        <div class="text-sm text-gray-500 dark:text-gray-400">Total Users</div>
                        <div class="mt-1 text-2xl font-semibold text-gray-800 dark:text-white/90" 
                             x-text="roles.reduce((total, role) => total + role.users_count, 0)"></div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Demo Instructions -->
    <div class="mt-6 rounded-lg border border-blue-200 bg-blue-50 p-4 dark:border-blue-800 dark:bg-blue-900/20">
        <div class="flex items-start">
            <i class="bi bi-info-circle-fill me-2 mt-0.5 text-blue-500"></i>
            <div>
                <h4 class="text-sm font-medium text-blue-800 dark:text-blue-200">Demo Mode Active</h4>
                <p class="mt-1 text-sm text-blue-700 dark:text-blue-300">
                    This is a demonstration view with hardcoded data. No data is being saved to the database.
                    Click "Switch to Edit Demo" to see edit mode, or interact with any button to see demo actions.
                </p>
            </div>
        </div>
    </div>
@endsection