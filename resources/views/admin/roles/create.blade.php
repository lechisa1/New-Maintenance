@extends('layouts.app')
@php
    $breadcrumbs = [
        ['label' => 'Home', 'url' => url('/')],
        ['label' => 'Role Management', 'url' => route('roles.index')], // active page
        ['label' => 'Create Role', 'url' => route('roles.create')],
    ];
@endphp

@section('content')
    <x-common.page-breadcrumb :breadcrumbs="$breadcrumbs" />

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
                        class="rounded-2xl border border-gray-200 bg-white shadow-sm dark:border-gray-800 dark:bg-gray-900 flex flex-col h-full">

                        {{-- Header with Quick Stats --}}
                        <div
                            class="border-b border-gray-200 p-5 dark:border-gray-800 bg-gray-50/50 dark:bg-white/[0.02] rounded-t-2xl flex items-center justify-between">
                            <div>
                                <h3 class="text-base font-bold text-gray-800 dark:text-white/90">Permissions</h3>
                                <p class="text-xs text-gray-500 mt-1">
                                    Selected: <span class="font-bold text-blue-600"
                                        x-text="selectedPermissions.length"></span> permissions
                                </p>
                            </div>
                            <div class="flex items-center gap-3">
                                <div class="relative">
                                    <i
                                        class="bi bi-search absolute left-3 top-1/2 -translate-y-1/2 text-gray-400 text-xs"></i>
                                    <input type="text" x-model="searchQuery" placeholder="Filter permissions..."
                                        class="h-9 w-40 md:w-56 rounded-full border-gray-200 bg-white pl-9 text-xs focus:ring-2 focus:ring-blue-500/20 dark:border-gray-700 dark:bg-gray-800">
                                </div>
                                <button type="button" @click="selectAllPermissions"
                                    class="text-xs font-semibold px-3 py-1.5 rounded-md bg-blue-50 text-blue-600 hover:bg-blue-100 dark:bg-blue-900/20 transition">All</button>
                                <button type="button" @click="clearAllPermissions"
                                    class="text-xs font-semibold px-3 py-1.5 rounded-md bg-red-50 text-red-600 hover:bg-red-100 dark:bg-red-900/20 transition">None</button>
                            </div>
                        </div>

                        {{-- Optimized Matrix Body --}}
                        <div class="p-0 overflow-y-auto custom-scrollbar" style="max-height: 600px;">
                            <table class="w-full text-left border-separate border-spacing-0">
                                <thead class="sticky top-0 bg-white dark:bg-gray-900 z-10 shadow-sm">
                                    <tr>
                                        <th
                                            class="px-6 py-3 text-[10px] font-bold uppercase tracking-widest text-gray-400 border-b dark:border-gray-800">
                                            Module / Resource</th>
                                        <th
                                            class="px-6 py-3 text-[10px] font-bold uppercase tracking-widest text-gray-400 border-b dark:border-gray-800">
                                            Actions & Privileges</th>
                                    </tr>
                                </thead>
                                <tbody class="divide-y divide-gray-100 dark:divide-gray-800">
                                    @foreach ($permissions as $group => $groupPermissions)
                                        @php
                                            $groupIds = $groupPermissions
                                                ->pluck('id')
                                                ->map(fn($id) => (string) $id)
                                                ->toArray();
                                        @endphp
                                        <tr x-show="shouldShowGroup('{{ $group }}', @json($groupPermissions->pluck('name')))"
                                            class="hover:bg-gray-50/50 dark:hover:bg-white/[0.01] transition">
                                            {{-- Resource Name --}}
                                            <td class="px-6 py-4 align-top w-1/3">
                                                <div class="flex items-center gap-3">
                                                    <input type="checkbox"
                                                        @change="toggleGroup(@json($groupIds))"
                                                        :checked="isGroupFullySelected(@json($groupIds))"
                                                        :indeterminate="isGroupPartiallySelected(@json($groupIds))"
                                                        class="h-4 w-4 rounded border-gray-300 text-blue-600 focus:ring-blue-500">
                                                    <span
                                                        class="text-sm font-bold text-gray-700 dark:text-gray-300">{{ ucwords(str_replace('_', ' ', $group)) }}</span>
                                                </div>
                                            </td>

                                            {{-- Permissions Grid --}}
                                            <td class="px-6 py-4">
                                                <div class="flex flex-wrap gap-2">
                                                    @foreach ($groupPermissions as $permission)
                                                        @php
                                                            $action = str_contains($permission->name, '.')
                                                                ? explode('.', $permission->name)[1]
                                                                : $permission->name;
                                                            $colorClass = match ($action) {
                                                                'view'
                                                                    => 'bg-emerald-50 text-emerald-700 border-emerald-100 dark:bg-emerald-500/10 dark:text-emerald-400 dark:border-emerald-500/20',
                                                                'create'
                                                                    => 'bg-blue-50 text-blue-700 border-blue-100 dark:bg-blue-500/10 dark:text-blue-400 dark:border-blue-500/20',
                                                                'update',
                                                                'edit'
                                                                    => 'bg-amber-50 text-amber-700 border-amber-100 dark:bg-amber-500/10 dark:text-amber-400 dark:border-amber-500/20',
                                                                'delete'
                                                                    => 'bg-red-50 text-red-700 border-red-100 dark:bg-red-500/10 dark:text-red-400 dark:border-red-500/20',
                                                                default
                                                                    => 'bg-gray-50 text-gray-700 border-gray-100 dark:bg-white/5 dark:text-gray-400 dark:border-white/10',
                                                            };
                                                        @endphp
                                                        <label class="cursor-pointer">
                                                            <input type="checkbox" value="{{ (string) $permission->id }}"
                                                                x-model="selectedPermissions" class="hidden peer">
                                                            <div
                                                                class="px-3 py-1.5 rounded-lg border text-[11px] font-bold transition-all peer-checked:ring-2 peer-checked:ring-offset-1 peer-checked:ring-blue-500 {{ $colorClass }}">
                                                                <i
                                                                    class="bi bi-{{ $action == 'view' ? 'eye' : ($action == 'create' ? 'plus-lg' : ($action == 'delete' ? 'trash' : 'pencil')) }} mr-1"></i>
                                                                {{ strtoupper($action) }}
                                                            </div>
                                                        </label>
                                                    @endforeach
                                                </div>
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
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
