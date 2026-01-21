@extends('layouts.app')

@section('content')
    <x-common.page-breadcrumb pageTitle="Create New Role" />

    <div class="mx-auto px-4 pb-10" x-data="roleForm()">
        <template x-if="alert.show">
            <div class="mb-6 max-w-7xl mx-auto" x-transition:enter="transition ease-out duration-300">
                <x-common.alert ::variant="alert.variant">
                    <x-slot name="title"><span x-text="alert.title"></span></x-slot>
                    <span x-text="alert.message"></span>
                </x-common.alert>
            </div>
        </template>

        <form @submit.prevent="submitForm">
            @csrf
            <div class="grid grid-cols-1 gap-6 lg:grid-cols-12">

                {{-- LEFT SIDE: Role Details --}}
                <div class="lg:col-span-4">
                    <div
                        class="sticky top-6 rounded-2xl border border-gray-200 bg-white shadow-sm dark:border-gray-800 dark:bg-white/[0.03]">
                        <div class="border-b border-gray-200 p-5 dark:border-gray-800">
                            <h3 class="flex items-center text-base font-bold text-gray-800 dark:text-white/90">
                                <i class="bi bi-shield-plus mr-3 text-blue-600"></i> Role Details
                            </h3>
                        </div>

                        <div class="p-6 space-y-5">
                            <input type="hidden" name="guard_name" x-model="formData.guard_name">
                            <div>
                                <label class="mb-2 block text-sm font-semibold text-gray-700 dark:text-gray-300">Role Name
                                    <span class="text-red-500">*</span></label>
                                <input type="text" x-model="formData.name" name="name" required
                                    :class="`h-11 w-full rounded-lg border ${errors.name ? 'border-red-300' : 'border-gray-300'} bg-gray-50 px-4 py-2.5 text-sm transition focus:border-blue-500 dark:border-gray-700 dark:bg-gray-800`"
                                    placeholder="e.g., Content Manager">
                                <div x-show="errors.name" class="mt-1 text-xs text-red-500" x-text="errors.name"></div>
                            </div>

                            <div>
                                <label class="mb-2 block text-sm font-semibold text-gray-700 dark:text-gray-300">Dashboard
                                    Route</label>
                                <input type="text" x-model="formData.dashboard_route" placeholder="admin.dashboard"
                                    class="h-11 w-full rounded-lg border border-gray-300 bg-gray-50 px-4 py-2.5 text-sm dark:border-gray-700 dark:bg-gray-800">
                            </div>

                            <div>
                                <label
                                    class="mb-2 block text-sm font-semibold text-gray-700 dark:text-gray-300">Description</label>
                                <textarea x-model="formData.description" rows="3" placeholder="Briefly define this role"
                                    class="w-full rounded-lg border border-gray-300 bg-gray-50 px-4 py-2.5 text-sm dark:border-gray-700 dark:bg-gray-800"></textarea>
                            </div>

                            <div class="pt-4 border-t border-gray-100 dark:border-gray-800 flex flex-col gap-3">
                                <button type="submit" :disabled="loading"
                                    :class="`inline-flex items-center justify-center rounded-lg py-3 text-sm font-bold shadow-lg transition ${loading ? 'bg-blue-400 cursor-not-allowed' : 'bg-blue-600 hover:bg-blue-700'} text-white`">
                                    <i x-show="!loading" class="bi bi-check-lg mr-2"></i>
                                    <i x-show="loading" class="bi bi-arrow-repeat animate-spin mr-2"></i>
                                    <span x-text="loading ? 'Saving...' : 'Create Role'"></span>
                                </button>
                                <a href="{{ route('roles.index') }}"
                                    class="text-center text-sm font-bold text-gray-500 hover:text-gray-700">Cancel</a>
                            </div>
                        </div>
                    </div>
                </div>
                {{-- RIGHT SIDE: Permissions --}}
                <div class="lg:col-span-8">
                    <div
                        class="rounded-2xl border border-gray-200 bg-white shadow-sm dark:border-gray-800 dark:bg-white/[0.03] flex flex-col h-full max-h-[600px]">
                        {{-- Sticky Header for Permissions --}}
                        <div
                            class="sticky top-0 z-10 bg-white dark:bg-[#121212] border-b border-gray-200 p-5 dark:border-gray-800 flex flex-wrap items-center justify-between gap-4 rounded-t-2xl">
                            <h3 class="text-base font-bold text-gray-800 dark:text-white/90">
                                <i class="bi bi-key mr-2 text-blue-600"></i> Permissions Assignment
                            </h3>

                            <div class="flex items-center gap-4">
                                <div class="relative">
                                    <span class="absolute inset-y-0 left-0 flex items-center pl-3 text-gray-400">
                                        <i class="bi bi-search text-xs"></i>
                                    </span>
                                    <input type="text" x-model="searchQuery" placeholder="Search..."
                                        class="h-9 w-48 rounded-md border border-gray-200 bg-gray-50 pl-9 pr-3 text-xs focus:ring-1 focus:ring-blue-500 dark:border-gray-700 dark:bg-gray-800">
                                </div>
                                <div class="flex gap-2 border-l pl-4 dark:border-gray-700">
                                    <button type="button" @click="selectAllPermissions"
                                        class="text-xs font-bold text-blue-600 hover:underline">Select All</button>
                                    <button type="button" @click="clearAllPermissions"
                                        class="text-xs font-bold text-red-500 hover:underline">Clear</button>
                                </div>
                            </div>
                        </div>

                        {{-- Scrollable Body --}}
                        <div class="p-6 overflow-y-auto custom-scrollbar" style="max-height: 650px;">
                            <div class="grid grid-cols-1 gap-4 md:grid-cols-2">
                                @foreach ($permissions as $group => $groupPermissions)
                                    @php
                                        $groupIds = $groupPermissions
                                            ->pluck('id')
                                            ->map(fn($id) => (string) $id)
                                            ->toArray();
                                    @endphp

                                    <div class="overflow-hidden rounded-xl border border-gray-100 dark:border-gray-800 bg-white dark:bg-transparent h-fit"
                                        x-show="shouldShowGroup('{{ $group }}', @json($groupPermissions->pluck('name')))">

                                        <div
                                            class="flex items-center justify-between bg-gray-50/50 px-4 py-2.5 dark:bg-gray-800/50">
                                            <label class="flex items-center gap-2 cursor-pointer">
                                                <input type="checkbox" @change="toggleGroup(@json($groupIds))"
                                                    :checked="isGroupFullySelected(@json($groupIds))"
                                                    :indeterminate="isGroupPartiallySelected(@json($groupIds))"
                                                    class="h-4 w-4 rounded border-gray-300 text-blue-600 focus:ring-blue-500">
                                                <span
                                                    class="text-xs font-bold uppercase tracking-wider text-gray-600 dark:text-gray-400">{{ $group }}</span>
                                            </label>
                                            <span
                                                class="text-[10px] font-bold text-blue-600 bg-blue-50 px-2 py-0.5 rounded-full dark:bg-blue-900/30">
                                                <span
                                                    x-text="getSelectedCount(@json($groupIds))"></span>/{{ count($groupPermissions) }}
                                            </span>
                                        </div>

                                        <div class="p-4 grid gap-3">
                                            @foreach ($groupPermissions as $permission)
                                                <label class="flex items-start cursor-pointer group"
                                                    x-show="searchQuery === '' || '{{ strtolower($permission->name) }}'.includes(searchQuery.toLowerCase())">
                                                    <input type="checkbox" value="{{ (string) $permission->id }}"
                                                        x-model="selectedPermissions"
                                                        class="h-4 w-4 rounded border-gray-300 text-blue-600 focus:ring-blue-500 dark:border-gray-700 dark:bg-gray-800">
                                                    <span
                                                        class="ml-3 text-sm text-gray-600 group-hover:text-blue-600 dark:text-gray-400 transition-colors">
                                                        {{ $permission->display_name ?? ucwords(str_replace(['.', '_'], ' ', $permission->name)) }}
                                                    </span>
                                                </label>
                                            @endforeach
                                        </div>
                                    </div>
                                @endforeach
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </form>
    </div>
@endsection

@push('scripts')
    <script>
        function roleForm() {
            return {
                loading: false,
                searchQuery: '',
                alert: {
                    show: false,
                    variant: 'info',
                    title: '',
                    message: ''
                },
                formData: {
                    name: '',
                    guard_name: 'web',
                    dashboard_route: '',
                    description: ''
                },
                selectedPermissions: [], // IDs will be stored as strings here
                errors: {},

                showAlert(variant, title, message) {
                    this.alert = {
                        show: true,
                        variant,
                        title,
                        message
                    };
                    window.scrollTo({
                        top: 0,
                        behavior: 'smooth'
                    });
                },

                isGroupFullySelected(groupIds) {
                    return groupIds.every(id => this.selectedPermissions.includes(id.toString()));
                },

                isGroupPartiallySelected(groupIds) {
                    const count = this.getSelectedCount(groupIds);
                    return count > 0 && count < groupIds.length;
                },

                getSelectedCount(groupIds) {
                    return groupIds.filter(id => this.selectedPermissions.includes(id.toString())).length;
                },

                shouldShowGroup(groupName, perms) {
                    if (this.searchQuery === '') return true;
                    const query = this.searchQuery.toLowerCase();
                    return groupName.toLowerCase().includes(query) || perms.some(p => p.toLowerCase().includes(query));
                },

                toggleGroup(groupIds) {
                    const ids = groupIds.map(id => id.toString());
                    if (this.isGroupFullySelected(ids)) {
                        // Unselect all in group
                        this.selectedPermissions = this.selectedPermissions.filter(id => !ids.includes(id));
                    } else {
                        // Select all in group (avoid duplicates)
                        ids.forEach(id => {
                            if (!this.selectedPermissions.includes(id)) this.selectedPermissions.push(id);
                        });
                    }
                },

                selectAllPermissions() {
                    // Flat array of all IDs as strings
                    const allIds = [
                        @foreach ($permissions->flatten() as $p)
                            "{{ (string) $p->id }}",
                        @endforeach
                    ];
                    this.selectedPermissions = allIds;
                },

                clearAllPermissions() {
                    this.selectedPermissions = [];
                },

                async submitForm() {
                    this.loading = true;
                    this.errors = {};
                    try {
                        const response = await fetch('{{ route('roles.store') }}', {
                            method: 'POST',
                            headers: {
                                'Content-Type': 'application/json',
                                'X-CSRF-TOKEN': '{{ csrf_token() }}',
                                'Accept': 'application/json'
                            },
                            body: JSON.stringify({
                                ...this.formData,
                                permissions: this.selectedPermissions
                            })
                        });
                        const data = await response.json();
                        if (response.ok && data.success) {
                            this.showAlert('success', 'Success!', 'Role created successfully.');
                            setTimeout(() => window.location.href = '{{ route('roles.index') }}', 1000);
                        } else {
                            this.errors = data.errors || {};
                            this.showAlert('error', 'Form Error', data.message || 'Please check fields.');
                        }
                    } catch (error) {
                        this.showAlert('error', 'System Error', 'Communication failure.');
                    } finally {
                        this.loading = false;
                    }
                }
            };
        }
    </script>
@endpush
