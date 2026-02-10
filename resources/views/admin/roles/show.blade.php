@extends('layouts.app')
@php
    $breadcrumbs = [
        ['label' => 'Home', 'url' => url('/')],
        ['label' => 'Role Management', 'url' => route('roles.index')],
        ['label' => $role->display_name ?? $role->name],
    ];

@endphp
@section('content')
    <x-common.page-breadcrumb :breadcrumbs="$breadcrumbs" />


    <div class="grid grid-cols-1 gap-6 lg:grid-cols-3">
        <!-- Role Information -->
        <div class="rounded-2xl border border-gray-200 bg-white p-6 dark:border-gray-800 dark:bg-white/[0.03] lg:col-span-2">
            <div class="mb-6 flex items-start justify-between">
                <div>
                    <div class="flex items-center gap-3">
                        <div
                            class="flex h-12 w-12 items-center justify-center rounded-full bg-green-500 text-yellow-600 dark:bg-blue-900/30 dark:text-blue-400">
                            <i class="bi bi-shield text-xl"></i>
                        </div>
                        <div>
                            <h3 class="text-lg font-semibold text-gray-800 dark:text-white/90">
                                {{ $role->display_name ?? ucwords(str_replace('-', ' ', $role->name)) }}
                            </h3>
                            <div class="mt-1 flex flex-wrap gap-2">
                                <span
                                    class="rounded-full bg-gray-100 px-3 py-1 text-xs font-medium text-gray-700 dark:bg-gray-800 dark:text-gray-300">
                                    {{ $role->guard_name }} Guard
                                </span>
                                @if ($role->is_system_role)
                                    <span
                                        class="rounded-full bg-yellow-100 px-3 py-1 text-xs font-medium text-yellow-800 dark:bg-yellow-900/30 dark:text-yellow-300">
                                        <i class="bi bi-shield-lock me-1"></i>System Role
                                    </span>
                                @endif
                                @if ($role->dashboard_route)
                                    <span
                                        class="rounded-full bg-green-100 px-3 py-1 text-xs font-medium text-green-800 dark:bg-green-900/30 dark:text-green-300">
                                        <i class="bi bi-route me-1"></i>{{ $role->dashboard_route }}
                                    </span>
                                @endif
                            </div>
                        </div>
                    </div>
                </div>
                <div class="flex gap-2">
                    <a href="{{ route('roles.edit', $role) }}"
                        class="inline-flex items-center rounded-lg border border-gray-300 bg-white px-3 py-2 text-gray-500 hover:bg-gray-50 hover:text-blue-600 dark:border-gray-700 dark:bg-gray-800 dark:text-gray-400 dark:hover:bg-white/[0.03] dark:hover:text-blue-500">
                        <i class="bi bi-pencil mr-2"></i>
                        <span>Edit</span>
                    </a>
                    <a href="{{ route('roles.index') }}"
                        class="inline-flex items-center rounded-lg border border-gray-300 bg-white px-3 py-2 text-gray-500 hover:bg-gray-50 hover:text-gray-800 dark:border-gray-700 dark:bg-gray-800 dark:text-gray-400 dark:hover:bg-white/[0.03] dark:hover:text-gray-200">
                        <i class="bi bi-arrow-left mr-2"></i>
                        <span>Back</span>
                    </a>
                </div>

            </div>

            <hr class="mb-6 border-gray-200 dark:border-gray-700">

            <!-- Role Description -->
            <div class="mb-8">
                <h4 class="mb-3 text-sm font-semibold text-gray-800 dark:text-white/90">
                    <i class="bi bi-card-text me-2"></i>Description
                </h4>
                <div class="rounded-lg border border-gray-200 bg-gray-50 p-4 dark:border-gray-700 dark:bg-gray-800">
                    <p class="text-gray-700 dark:text-gray-300">
                        {{ $role->description ?: 'No description provided.' }}
                    </p>
                </div>
            </div>

            <!-- Permissions -->
            <div>
                <div class="mb-4 flex items-center justify-between">
                    <h4 class="text-sm font-semibold text-gray-800 dark:text-white/90">
                        <i class="bi bi-key me-2"></i>Assigned Permissions
                        <span
                            class="ml-2 rounded-full bg-gray-200 px-2 py-0.5 text-xs text-gray-700 dark:bg-gray-700 dark:text-gray-300">
                            {{ $role->permissions->count() }} permissions
                        </span>
                    </h4>
                </div>

                @if ($role->permissions->isEmpty())
                    <div
                        class="rounded-lg border border-gray-200 bg-gray-50 p-8 text-center dark:border-gray-700 dark:bg-gray-800">
                        <i class="bi bi-key text-4xl text-gray-400 dark:text-gray-500"></i>
                        <p class="mt-3 text-gray-500 dark:text-gray-400">No permissions assigned to this role</p>
                        <a href="{{ route('roles.edit', $role) }}"
                            class="mt-4 inline-flex items-center text-sm text-blue-500 hover:text-blue-600 dark:text-blue-400">
                            <i class="bi bi-plus-circle me-1"></i> Add Permissions
                        </a>
                    </div>
                @else
                    <div class="space-y-4">
                        @foreach ($permissionGroups as $group => $permissions)
                            <div class="rounded-lg border border-gray-200 dark:border-gray-700">
                                <div class="rounded-t-lg bg-gray-50 p-4 dark:bg-gray-800">
                                    <div class="flex items-center justify-between">
                                        <div class="flex items-center">
                                            <span
                                                class="font-medium text-gray-800 dark:text-white/90">{{ $group ?: 'General' }}</span>
                                            <span
                                                class="ml-2 rounded-full bg-gray-200 px-2 py-0.5 text-xs text-gray-700 dark:bg-gray-700 dark:text-gray-300">
                                                {{ $permissions->count() }}
                                            </span>
                                        </div>
                                    </div>
                                </div>

                                <div class="p-4">
                                    <div class="grid grid-cols-1 gap-3 md:grid-cols-2">
                                        @foreach ($permissions as $permission)
                                            <div
                                                class="flex items-start rounded-lg border border-gray-200 bg-white p-3 dark:border-gray-700 dark:bg-gray-800">
                                                <div
                                                    class="flex h-5 w-5 items-center justify-center rounded-full bg-green-100 text-green-600 dark:bg-green-900/30 dark:text-green-400">
                                                    <i class="bi bi-check text-xs"></i>
                                                </div>
                                                <div class="ml-3">
                                                    <div class="text-sm font-medium text-gray-800 dark:text-white/90">
                                                        {{ $permission->display_name ?? ucwords(str_replace('.', ' ', $permission->name)) }}
                                                    </div>
                                                    @if ($permission->description)
                                                        <p class="mt-1 text-xs text-gray-500 dark:text-gray-400">
                                                            {{ $permission->description }}
                                                        </p>
                                                    @endif
                                                    <p class="mt-1 text-xs text-gray-400 dark:text-gray-500">
                                                        <code>{{ $permission->name }}</code>
                                                    </p>
                                                </div>
                                            </div>
                                        @endforeach
                                    </div>
                                </div>
                            </div>
                        @endforeach
                    </div>
                @endif
            </div>
        </div>

        <!-- Sidebar -->
        <div class="space-y-6">
            <!-- Statistics -->


            <!-- Activity -->
            <div class="rounded-2xl border border-gray-200 bg-white p-6 dark:border-gray-800 dark:bg-white/[0.03]">
                <h4 class="mb-4 text-sm font-semibold text-gray-800 dark:text-white/90">
                    <i class="bi bi-clock-history me-2"></i>Activity
                </h4>

                <div class="space-y-3">
                    <div class="flex items-start">
                        <div
                            class="flex h-8 w-8 items-center justify-center rounded-full bg-blue-100 text-blue-600 dark:bg-blue-900/30 dark:text-blue-400">
                            <i class="bi bi-calendar-plus"></i>
                        </div>
                        <div class="ml-3">
                            <div class="text-sm font-medium text-gray-800 dark:text-white/90">Created</div>
                            <div class="text-xs text-gray-500 dark:text-gray-400">
                                {{ $role->created_at->format('F d, Y \a\t h:i A') }}
                            </div>
                        </div>
                    </div>

                    <div class="flex items-start">
                        <div
                            class="flex h-8 w-8 items-center justify-center rounded-full bg-green-100 text-green-600 dark:bg-green-900/30 dark:text-green-400">
                            <i class="bi bi-arrow-clockwise"></i>
                        </div>
                        <div class="ml-3">
                            <div class="text-sm font-medium text-gray-800 dark:text-white/90">Last Updated</div>
                            <div class="text-xs text-gray-500 dark:text-gray-400">
                                {{ $role->updated_at->format('F d, Y \a\t h:i A') }}
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Quick Actions -->
            <div class="rounded-2xl border border-gray-200 bg-white p-6 dark:border-gray-800 dark:bg-white/[0.03]">
                <h4 class="mb-4 text-sm font-semibold text-gray-800 dark:text-white/90">
                    <i class="bi bi-lightning me-2"></i>Quick Actions
                </h4>

                <div class="space-y-3">
                    <a href="{{ route('roles.users', $role) }}"
                        class="flex items-center justify-between rounded-lg border border-gray-200 bg-white p-3 hover:border-blue-300 hover:bg-blue-50 dark:border-gray-700 dark:bg-gray-800 dark:hover:border-blue-800 dark:hover:bg-blue-900/20">
                        <div class="flex items-center">
                            <div
                                class="flex h-8 w-8 items-center justify-center rounded-full bg-blue-100 text-blue-600 dark:bg-blue-900/30 dark:text-blue-400">
                                <i class="bi bi-people"></i>
                            </div>
                            <span class="ml-3 text-sm font-medium text-gray-700 dark:text-gray-300">View Users</span>
                        </div>
                        <i class="bi bi-chevron-right text-gray-400"></i>
                    </a>

                    <a href="{{ route('roles.edit', $role) }}"
                        :class="`flex items-center justify-between rounded-lg border p-3 ${$role->is_editable ? 'border-gray-200 bg-white hover:border-yellow-300 hover:bg-yellow-50 dark:border-gray-700 dark:bg-gray-800 dark:hover:border-yellow-800 dark:hover:bg-yellow-900/20' : 'border-gray-200 bg-gray-100 cursor-not-allowed dark:border-gray-700 dark:bg-gray-800'}`">
                        <div class="flex items-center">
                            <div
                                class="flex h-8 w-8 items-center justify-center rounded-full bg-yellow-100 text-yellow-600 dark:bg-yellow-900/30 dark:text-yellow-400">
                                <i class="bi bi-pencil"></i>
                            </div>
                            <span class="ml-3 text-sm font-medium text-gray-700 dark:text-gray-300">Edit Role</span>
                        </div>
                        <i class="bi bi-chevron-right text-gray-400"></i>
                    </a>

                    @if ($role->is_deletable)
                        <form action="{{ route('roles.destroy', $role) }}" method="POST"
                            onsubmit="return confirm('Are you sure you want to delete this role?')">
                            @csrf
                            @method('DELETE')
                            <button type="submit"
                                class="flex w-full items-center justify-between rounded-lg border border-gray-200 bg-white p-3 hover:border-red-300 hover:bg-red-50 dark:border-gray-700 dark:bg-gray-800 dark:hover:border-red-800 dark:hover:bg-red-900/20">
                                <div class="flex items-center">
                                    <div
                                        class="flex h-8 w-8 items-center justify-center rounded-full bg-red-100 text-red-600 dark:bg-red-900/30 dark:text-red-400">
                                        <i class="bi bi-trash"></i>
                                    </div>
                                    <span class="ml-3 text-sm font-medium text-gray-700 dark:text-gray-300">Delete
                                        Role</span>
                                </div>
                                <i class="bi bi-chevron-right text-gray-400"></i>
                            </button>
                        </form>
                    @endif
                </div>
            </div>
        </div>
    </div>
@endsection
