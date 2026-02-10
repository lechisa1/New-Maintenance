@extends('layouts.app')
@php
    $breadcrumbs = [
        ['label' => 'Home', 'url' => url('/')],
        ['label' => 'Users', 'url' => route('users.index')],
        ['label' => $user->full_name . ' Details'], // current page
    ];
@endphp



@section('content')
    <x-common.page-breadcrumb :breadcrumbs="$breadcrumbs" />
    @include('maintenance-requests.partials.alerts')
    <div class="grid grid-cols-1 gap-6 lg:grid-cols-3">
        <!-- Left Column: User Profile -->
        <div class="lg:col-span-2 space-y-6">
            <!-- Profile Card -->
            <div class="rounded-2xl border border-gray-200 bg-white p-6 dark:border-gray-800 dark:bg-white/[0.03]">
                <div class="flex flex-col items-center sm:flex-row sm:items-start">
                    <!-- Avatar -->
                    <div class="mb-4 sm:mb-0 sm:mr-6">
                        <div
                            class="h-20 w-20 rounded-full bg-gradient-to-r from-blue-500 to-purple-600 flex items-center justify-center text-2xl font-bold text-white">
                            {{ strtoupper(substr($user->full_name, 0, 2)) }}
                        </div>
                    </div>

                    <!-- User Info -->
                    <div class="flex-1">
                        <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between">
                            <div>
                                <h2 class="text-2xl font-bold text-gray-800 dark:text-white/90">{{ $user->full_name }}</h2>
                                <p class="mt-1 text-gray-500 dark:text-gray-400">{{ $user->email }}</p>
                            </div>
                            <div class="mt-3 flex items-center space-x-2 sm:mt-0">
                                <span
                                    class="inline-flex items-center rounded-full px-2.5 py-0.5 text-xs font-medium {{ $user->is_active ? 'bg-green-100 text-green-800 dark:bg-green-900 dark:text-green-200' : 'bg-gray-100 text-gray-800 dark:bg-gray-900 dark:text-gray-200' }}">
                                    {{ $user->is_active ? 'Active' : 'Inactive' }}
                                </span>
                                <span
                                    class="inline-flex items-center rounded-full px-2.5 py-0.5 text-xs font-medium bg-blue-100 text-blue-800 dark:bg-blue-900 dark:text-blue-200">
                                    {{ $user->roles->pluck('name')->join(', ') }}
                                </span>
                            </div>
                        </div>

                        <div class="mt-6 grid grid-cols-1 gap-4 sm:grid-cols-2">
                            <div>
                                <p class="text-sm font-medium text-gray-500 dark:text-gray-400">Phone</p>
                                <p class="mt-1 text-gray-800 dark:text-white/90">{{ $user->phone ?? 'Not provided' }}</p>
                            </div>
                            <div>
                                <p class="text-sm font-medium text-gray-500 dark:text-gray-400">Division</p>
                                <p class="mt-1 text-gray-800 dark:text-white/90">
                                    {{ $user->division->name ?? 'Not assigned' }}</p>
                            </div>
                            <div>
                                <p class="text-sm font-medium text-gray-500 dark:text-gray-400">Cluster</p>
                                <p class="mt-1 text-gray-800 dark:text-white/90">
                                    {{ $user->cluster->name ?? 'Not assigned' }}</p>
                            </div>
                            <div>
                                <p class="text-sm font-medium text-gray-500 dark:text-gray-400">Organization</p>
                                <p class="mt-1 text-gray-800 dark:text-white/90">
                                    {{ $user->organization->name ?? 'Not assigned' }}</p>
                            </div>
                            <div>
                                <p class="text-sm font-medium text-gray-500 dark:text-gray-400">Member Since</p>
                                <p class="mt-1 text-gray-800 dark:text-white/90">{{ $user->created_at->format('M d, Y') }}
                                </p>
                            </div>
                            <div>
                                <p class="text-sm font-medium text-gray-500 dark:text-gray-400">Last Updated</p>
                                <p class="mt-1 text-gray-800 dark:text-white/90">{{ $user->updated_at->format('M d, Y') }}
                                </p>
                            </div>
                        </div>
                        @if (auth()->user()->can('users.update'))
                            <div class="mt-6 flex space-x-3">

                                <a href="{{ route('users.edit', $user) }}"
                                    class="inline-flex items-center rounded-lg border border-gray-300 bg-white px-4 py-2 text-sm font-medium text-gray-700 shadow-theme-xs hover:bg-gray-50 hover:text-gray-800 dark:border-gray-700 dark:bg-gray-800 dark:text-gray-400 dark:hover:bg-white/[0.03] dark:hover:text-gray-200">
                                    <i class="bi bi-pencil me-2"></i> Edit
                                </a>




                                <button type="button" onclick="confirmDelete()"
                                    class="inline-flex items-center rounded-lg border border-red-200 bg-red-50 px-4 py-2 text-sm font-medium text-red-600 shadow-theme-xs hover:bg-red-100 hover:text-red-700 dark:border-red-800 dark:bg-red-900/20 dark:text-red-400 dark:hover:bg-red-900/30">
                                    <i class="bi bi-trash me-2"></i> Delete
                                </button>

                            </div>
                        @endif
                        @if (!$user->is_active && auth()->user()->can('users.update'))
                            <form action="{{ route('users.restore', $user) }}" method="POST">
                                @csrf
                                <button type="submit"
                                    class="inline-flex items-center rounded-lg border border-green-200 bg-green-50 px-4 py-2 text-sm font-medium text-green-600 shadow-theme-xs hover:bg-green-100 hover:text-green-700 dark:border-green-800 dark:bg-green-900/20 dark:text-green-400 dark:hover:bg-green-900/30">
                                    <i class="bi bi-check-circle me-2"></i> Activate
                                </button>
                            </form>
                        @endif

                    </div>
                </div>
            </div>

            <!-- Recent Activity -->
            {{-- <div class="rounded-2xl border border-gray-200 bg-white p-6 dark:border-gray-800 dark:bg-white/[0.03]">
                <h3 class="mb-4 text-lg font-semibold text-gray-800 dark:text-white/90">
                    <i class="bi bi-clock-history me-2"></i>Recent Activity
                </h3>


            </div> --}}
        </div>

        <!-- Right Column: User Roles & Actions -->
        <div class="space-y-6">
            <!-- User Roles -->
            <div class="rounded-2xl border border-gray-200 bg-white p-6 dark:border-gray-800 dark:bg-white/[0.03]">
                <h3 class="mb-4 text-lg font-semibold text-gray-800 dark:text-white/90">
                    <i class="bi bi-shield-check me-2"></i>User Roles
                </h3>

                <div class="space-y-3">
                    @foreach ($user->roles as $role)
                        <div class="flex items-center justify-between rounded-lg bg-gray-50 px-4 py-3 dark:bg-gray-900">
                            <div class="flex items-center">
                                <div
                                    class="mr-3 flex h-8 w-8 items-center justify-center rounded-full bg-blue-100 dark:bg-blue-900/30">
                                    <i class="bi bi-shield text-blue-600 dark:text-blue-400"></i>
                                </div>
                                <div>
                                    <p class="font-medium text-gray-800 dark:text-white/90">{{ $role->name }}</p>
                                    <p class="text-xs text-gray-500 dark:text-gray-400">
                                        {{ $role->description ?? 'No description' }}</p>
                                </div>
                            </div>
                        </div>
                    @endforeach

                    @if ($user->roles->count() === 0)
                        <p class="text-gray-500 dark:text-gray-400">No roles assigned.</p>
                    @endif
                </div>
            </div>


        </div>
    </div>

    <!-- Delete Confirmation Modal -->
    <x-ui.modal x-data="{ open: false }" x-cloak>
        <x-slot name="button">
            <div x-show="false"></div>
        </x-slot>

        <x-slot name="content">
            <div class="p-6">
                <div class="mx-auto flex h-12 w-12 items-center justify-center rounded-full bg-red-100 dark:bg-red-900/30">
                    <i class="bi bi-exclamation-triangle text-red-600 dark:text-red-400 text-xl"></i>
                </div>
                <div class="mt-4 text-center">
                    <h3 class="text-lg font-semibold text-gray-800 dark:text-white/90">
                        Delete User
                    </h3>
                    <p class="mt-2 text-sm text-gray-500 dark:text-gray-400">
                        Are you sure you want to delete <span class="font-medium">{{ $user->full_name }}</span>?
                        This action cannot be undone.
                    </p>
                </div>
                <div class="mt-6 flex justify-end space-x-3">
                    <button @click="open = false" type="button"
                        class="rounded-lg border border-gray-300 bg-white px-4 py-2.5 text-sm font-medium text-gray-700 shadow-theme-xs hover:bg-gray-50 hover:text-gray-800 dark:border-gray-700 dark:bg-gray-800 dark:text-gray-400 dark:hover:bg-white/[0.03] dark:hover:text-gray-200">
                        Cancel
                    </button>
                    <form action="{{ route('users.destroy', $user) }}" method="POST">
                        @csrf
                        @method('DELETE')
                        <button type="submit"
                            class="rounded-lg bg-red-500 px-4 py-2.5 text-sm font-medium text-white shadow-theme-xs hover:bg-red-600 focus:outline-none focus:ring-2 focus:ring-red-500 focus:ring-offset-2 dark:focus:ring-offset-gray-900">
                            Delete User
                        </button>
                    </form>
                </div>
            </div>
        </x-slot>
    </x-ui.modal>

    <script>
        function confirmDelete() {
            // You can use Alpine.js or plain JavaScript to show the modal
            const modal = document.querySelector('[x-data]');
            if (modal) {
                modal.__x.$data.open = true;
            }
        }
    </script>
@endsection
