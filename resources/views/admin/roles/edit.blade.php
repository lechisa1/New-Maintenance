@extends('layouts.app')
@php
    $breadcrumbs = [
        ['label' => 'Home', 'url' => url('/')],
        ['label' => 'Role Management', 'url' => route('roles.index')], // active page

        ['label' => $role->display_name ?? ucwords(str_replace('-', ' ', $role->name))], // active page
    ];
@endphp

@section('content')
    <x-common.page-breadcrumb :breadcrumbs="$breadcrumbs" />

    <form @submit.prevent="submitForm" x-data="roleEditForm()">
        {{-- Alert Component: Controlled by Alpine.js --}}
        <template x-if="alert.show">
            <div class="mb-6" x-transition:enter="transition ease-out duration-300"
                x-transition:enter-start="opacity-0 transform -translate-y-2">
                <x-common.alert ::variant="alert.variant">
                    <x-slot name="title">
                        <span x-text="alert.title"></span>
                    </x-slot>
                    <span x-text="alert.message"></span>
                </x-common.alert>
            </div>
        </template>

        <div class="grid grid-cols-1 gap-6 lg:grid-cols-3">

            <div class="lg:col-span-2 space-y-6">
                <div
                    class="rounded-2xl border border-gray-200 bg-white shadow-sm dark:border-gray-800 dark:bg-white/[0.03]">

                    <div class="border-b border-gray-200 px-6 pt-6 dark:border-gray-700">
                        <div class="mb-4 flex items-center justify-between">
                            <div>
                                <h3 class="text-lg font-bold text-gray-800 dark:text-white/90">
                                    {{ $role->display_name ?? ucwords(str_replace('-', ' ', $role->name)) }}
                                </h3>
                                <p class="text-sm text-gray-500">Modify role attributes and access levels</p>
                            </div>
                        </div>

                        <div class="flex gap-6">
                            <button type="button" @click="tab = 'general'"
                                :class="tab === 'general' ? 'border-blue-600 text-blue-600' :
                                    'border-transparent text-gray-500 hover:text-gray-700'"
                                class="pb-4 border-b-2 text-sm font-medium transition-colors">
                                General Information
                            </button>
                            <button type="button" @click="tab = 'permissions'"
                                :class="tab === 'permissions' ? 'border-blue-600 text-blue-600' :
                                    'border-transparent text-gray-500 hover:text-gray-700'"
                                class="pb-4 border-b-2 text-sm font-medium transition-colors">
                                Permissions Setup
                            </button>
                        </div>
                    </div>

                    <div class="p-6">
                        <div x-show="tab === 'general'" x-transition>
                            <div class="grid grid-cols-1 gap-6 md:grid-cols-2">
                                <div class="space-y-2">
                                    <label class="text-sm font-semibold text-gray-700 dark:text-gray-300">Role Identity
                                        <span class="text-red-500">*</span></label>
                                    <input type="text" x-model="formData.name"
                                        class="w-full rounded-lg border-gray-200 bg-gray-50 px-4 py-2.5 text-sm focus:bg-white focus:ring-2 focus:ring-blue-500/20 dark:bg-gray-900 dark:border-gray-700 dark:text-white">
                                    <p x-show="errors.name" x-text="errors.name" class="text-xs text-red-500 mt-1"></p>
                                </div>

                                <div class="space-y-2">
                                    <label class="text-sm font-semibold text-gray-700 dark:text-gray-300">Auth Guard</label>
                                    <select x-model="formData.guard_name"
                                        class="w-full h-[44px] rounded-lg border-gray-200 bg-gray-50 px-4 py-2 text-sm dark:bg-gray-900 dark:border-gray-700 dark:text-white">
                                        @foreach ($guards as $guard)
                                            <option value="{{ $guard }}">{{ ucfirst($guard) }}</option>
                                        @endforeach
                                    </select>
                                </div>

                                <div class="md:col-span-2 space-y-2">
                                    <label class="text-sm font-semibold text-gray-700 dark:text-gray-300">Redirect Route
                                        (Optional)</label>
                                    <input type="text" x-model="formData.dashboard_route"
                                        placeholder="e.g. admin.dashboard"
                                        class="w-full rounded-lg border-gray-200 bg-gray-50 px-4 py-2.5 text-sm dark:bg-gray-900 dark:border-gray-700 dark:text-white">
                                </div>

                                <div class="md:col-span-2 space-y-2">
                                    <label class="text-sm font-semibold text-gray-700 dark:text-gray-300">Purpose
                                        Description</label>
                                    <textarea x-model="formData.description" rows="4"
                                        class="w-full rounded-lg border-gray-200 bg-gray-50 px-4 py-2.5 text-sm dark:bg-gray-900 dark:border-gray-700 dark:text-white"></textarea>
                                </div>
                            </div>
                        </div>

                        <div x-show="tab === 'permissions'" x-transition>
                            <div class="mb-6 flex flex-wrap items-center justify-between gap-4">
                                <div class="relative w-full max-w-xs">
                                    <span class="absolute inset-y-0 left-0 flex items-center pl-3">
                                        <i class="bi bi-search text-gray-400"></i>
                                    </span>
                                    <input type="text" x-model="searchQuery" placeholder="Filter permissions..."
                                        class="w-full rounded-full border-gray-200 py-2.5 pl-10 pr-4 text-sm focus:ring-blue-500/20 dark:bg-gray-900 dark:border-gray-700 dark:text-white">
                                </div>
                                <div class="flex gap-2">
                                    <button type="button" @click="selectAllPermissions"
                                        class="text-xs font-bold text-blue-600 hover:text-blue-700">Select All</button>
                                    <span class="text-gray-300">|</span>
                                    <button type="button" @click="clearAllPermissions"
                                        class="text-xs font-bold text-gray-500 hover:text-gray-700">Clear All</button>
                                </div>
                            </div>

                            <div class="space-y-4 max-h-[600px] overflow-y-auto pr-2 custom-scrollbar">
                                @foreach ($permissions as $group => $groupPermissions)
                                    @php
                                        $group_ids = $groupPermissions
                                            ->pluck('id')
                                            ->map(fn($id) => (string) $id)
                                            ->toArray();
                                    @endphp

                                    <div x-show="shouldShowGroup('{{ $group }}')"
                                        class="rounded-xl border border-gray-100 dark:border-gray-800">
                                        <div
                                            class="flex items-center justify-between bg-gray-50/50 p-4 dark:bg-gray-800/50">
                                            <div class="flex items-center gap-4">
                                                <h4 class="text-sm font-bold text-gray-700 dark:text-gray-200 capitalize">
                                                    <i class="bi bi-folder2-open mr-2 text-blue-500"></i>{{ $group }}
                                                </h4>
                                                <div
                                                    class="flex items-center gap-2 border-l border-gray-300 pl-4 dark:border-gray-600">
                                                    <button type="button"
                                                        @click="toggleGroup(@json($group_ids), true)"
                                                        class="text-[10px] uppercase tracking-wider font-bold text-blue-600 hover:underline">Select
                                                        all</button>
                                                    <button type="button"
                                                        @click="toggleGroup(@json($group_ids), false)"
                                                        class="text-[10px] uppercase tracking-wider font-bold text-gray-400 hover:text-red-500">Clear</button>
                                                </div>
                                            </div>

                                            <span
                                                class="text-xs font-medium text-gray-500 bg-white px-2 py-0.5 rounded-full shadow-sm dark:bg-gray-900">
                                                <span x-text="getGroupCount(@json($group_ids))"></span> /
                                                {{ count($groupPermissions) }}
                                            </span>
                                        </div>

                                        <div class="p-4 grid grid-cols-1 md:grid-cols-2 gap-3">
                                            @foreach ($groupPermissions as $permission)
                                                <label
                                                    class="relative flex cursor-pointer items-start rounded-lg border border-transparent p-2 transition-all hover:bg-blue-50 dark:hover:bg-blue-900/10"
                                                    :class="selectedPermissions.includes('{{ $permission->id }}') ?
                                                        'bg-blue-50/50 border-blue-100 dark:bg-blue-900/5' : ''">
                                                    <div class="flex h-5 items-center">
                                                        <input type="checkbox" value="{{ $permission->id }}"
                                                            x-model="selectedPermissions"
                                                            class="h-4 w-4 rounded border-gray-300 text-blue-600 focus:ring-blue-500">
                                                    </div>
                                                    <div class="ml-3">
                                                        <span
                                                            class="block text-sm font-medium text-gray-700 dark:text-gray-300">
                                                            {{ $permission->display_name ?? $permission->name }}
                                                        </span>
                                                    </div>
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

            <div class="lg:col-span-1">
                <div class="sticky top-6 space-y-6">
                    <div
                        class="rounded-2xl border border-gray-200 bg-white p-6 shadow-sm dark:border-gray-800 dark:bg-white/[0.03]">
                        <h4 class="mb-4 text-sm font-bold text-gray-800 dark:text-white">Publishing</h4>
                        <div class="space-y-4">
                            <div class="flex justify-between text-sm">
                                <span class="text-gray-500">Selected Permissions:</span>
                                <span class="font-bold text-blue-600" x-text="selectedPermissions.length"></span>
                            </div>
                            <div class="flex justify-between text-sm">
                                <span class="text-gray-500">Auth Guard:</span>
                                <span class="font-bold text-gray-700 dark:text-gray-300"
                                    x-text="formData.guard_name"></span>
                            </div>
                        </div>

                        <hr class="my-6 border-gray-100 dark:border-gray-800">

                        <div class="flex flex-col gap-3">
                            <button type="submit" :disabled="loading"
                                class="w-full rounded-xl bg-blue-600 py-3 text-sm font-bold text-white shadow-lg shadow-blue-500/20 transition-all hover:bg-blue-700 active:scale-95 disabled:opacity-70">
                                <span x-show="!loading"><i class="bi bi-check2-circle mr-2"></i>Save Changes</span>
                                <span x-show="loading">Processing...</span>
                            </button>
                            <a href="{{ route('roles.index') }}"
                                class="w-full rounded-xl border border-gray-200 bg-white py-3 text-center text-sm font-bold text-gray-600 transition-all hover:bg-gray-50 dark:border-gray-700 dark:bg-transparent dark:text-gray-400">
                                Cancel
                            </a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </form>
@endsection

@push('scripts')
    <script>
        function roleEditForm() {
            return {
                tab: 'general',
                loading: false,
                searchQuery: '',
                alert: {
                    show: false,
                    variant: 'info',
                    title: '',
                    message: ''
                },
                formData: {
                    name: @json($role->name),
                    guard_name: @json($role->guard_name),
                    dashboard_route: @json($role->dashboard_route ?? ''),
                    description: @json($role->description ?? '')
                },
                // Ensure initial IDs are strings for Alpine's x-model
                selectedPermissions: @json($role->permissions->pluck('id')->map(fn($id) => (string) $id)->toArray()),
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

                shouldShowGroup(groupName) {
                    if (!this.searchQuery) return true;
                    return groupName.toLowerCase().includes(this.searchQuery.toLowerCase());
                },

                getGroupCount(groupIds) {
                    return groupIds.filter(id => this.selectedPermissions.includes(id.toString())).length;
                },

                toggleGroup(ids, selectAll) {
                    const stringIds = ids.map(String);
                    if (selectAll) {
                        // Add group IDs if they aren't already selected
                        stringIds.forEach(id => {
                            if (!this.selectedPermissions.includes(id)) {
                                this.selectedPermissions.push(id);
                            }
                        });
                    } else {
                        // Remove group IDs from the selection
                        this.selectedPermissions = this.selectedPermissions.filter(id => !stringIds.includes(id));
                    }
                },

                selectAllPermissions() {
                    // This flattens your grouped collection and grabs every ID
                    this.selectedPermissions = @json($permissions->flatten()->pluck('id')->map(fn($id) => (string) $id)->toArray());
                },
                clearAllPermissions() {
                    this.selectedPermissions = [];
                },

                async submitForm() {
                    this.loading = true;
                    this.errors = {};
                    this.alert.show = false;

                    try {
                        const response = await fetch("{{ route('roles.update', $role->id) }}", {
                            method: 'PUT',
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

                        const result = await response.json();
                        if (response.ok) {
                            this.showAlert('success', 'Success', 'Role updated successfully');
                            setTimeout(() => window.location.href = "{{ route('roles.index') }}", 1000);
                        } else {
                            this.errors = result.errors || {};
                            this.showAlert('error', 'Update Failed', 'Please check the form for errors.');
                        }
                    } catch (error) {
                        this.showAlert('error', 'System Error', 'An unexpected error occurred.');
                    } finally {
                        this.loading = false;
                    }
                }
            };
        }
    </script>
@endpush
