@extends('layouts.app')

@section('content')
    <x-common.page-breadcrumb pageTitle="Users Management" />

    <div class="grid grid-cols-1 gap-6">
        <!-- Statistics Cards -->
        <div class="grid grid-cols-1 gap-4 sm:grid-cols-2 lg:grid-cols-4">
            <x-common.stat-card 
                title="Total Users" 
                value="{{ $totalUsers ?? 0 }}" 
                icon="bi bi-people" 
                variant="primary" 
            />
            <x-common.stat-card 
                title="Active Users" 
                value="{{ $activeUsers ?? 0 }}" 
                icon="bi bi-person-check" 
                variant="success" 
            />
            <x-common.stat-card 
                title="Inactive Users" 
                value="{{ $inactiveUsers ?? 0 }}" 
                icon="bi bi-person-x" 
                variant="warning" 
            />
            <x-common.stat-card 
                title="Admin Users" 
                value="{{ $adminUsers ?? 0 }}" 
                icon="bi bi-shield-check" 
                variant="danger" 
            />
        </div>

        <!-- Filters Card -->
        <div class="rounded-2xl border border-gray-200 bg-white p-6 dark:border-gray-800 dark:bg-white/[0.03]">
            <h3 class="mb-4 text-lg font-semibold text-gray-800 dark:text-white/90">
                <i class="bi bi-funnel me-2"></i>Filter Users
            </h3>
            
            <form action="{{ route('users.index') }}" method="GET">
                <div class="grid grid-cols-1 gap-5 md:grid-cols-2 lg:grid-cols-4">
                    <!-- Search -->
                    <div>
                        <label class="mb-2 block text-sm font-medium text-gray-700 dark:text-gray-300">
                            Search
                        </label>
                        <div class="relative">
                            <input type="text" name="search" 
                                class="h-11 w-full rounded-lg border border-gray-300 bg-transparent px-4 py-2.5 pl-11 text-sm text-gray-800 shadow-theme-xs placeholder:text-gray-400 focus:border-blue-300 focus:outline-none focus:ring-2 focus:ring-blue-500/10 dark:border-gray-700 dark:bg-gray-900 dark:text-white/90 dark:placeholder:text-white/30 dark:focus:border-blue-800"
                                placeholder="Search by name or email..."
                                value="{{ request('search') }}">
                            <i class="bi bi-search absolute left-3 top-1/2 -translate-y-1/2 text-gray-400"></i>
                        </div>
                    </div>

                    <!-- Division Filter -->
                    <div>
                        <label for="division_id" class="mb-2 block text-sm font-medium text-gray-700 dark:text-gray-300">
                            Division
                        </label>
                        <select name="division_id" id="division_id"
                            class="h-11 w-full rounded-lg border border-gray-300 bg-transparent px-4 py-2.5 text-sm text-gray-800 shadow-theme-xs focus:border-blue-300 focus:outline-none focus:ring-2 focus:ring-blue-500/10 dark:border-gray-700 dark:bg-gray-900 dark:text-white/90 dark:focus:border-blue-800">
                            <option value="">All Divisions</option>
                            @foreach($divisions as $division)
                                <option value="{{ $division->id }}" 
                                    {{ request('division_id') == $division->id ? 'selected' : '' }}>
                                    {{ $division->name }}
                                </option>
                            @endforeach
                        </select>
                    </div>

                    <!-- Cluster Filter -->
                    <div>
                        <label for="cluster_id" class="mb-2 block text-sm font-medium text-gray-700 dark:text-gray-300">
                            Cluster
                        </label>
                        <select name="cluster_id" id="cluster_id"
                            class="h-11 w-full rounded-lg border border-gray-300 bg-transparent px-4 py-2.5 text-sm text-gray-800 shadow-theme-xs focus:border-blue-300 focus:outline-none focus:ring-2 focus:ring-blue-500/10 dark:border-gray-700 dark:bg-gray-900 dark:text-white/90 dark:focus:border-blue-800">
                            <option value="">All Clusters</option>
                            @foreach($clusters as $cluster)
                                <option value="{{ $cluster->id }}"
                                    {{ request('cluster_id') == $cluster->id ? 'selected' : '' }}>
                                    {{ $cluster->name }}
                                </option>
                            @endforeach
                        </select>
                    </div>

                    <!-- Role Filter -->
                    <div>
                        <label for="role" class="mb-2 block text-sm font-medium text-gray-700 dark:text-gray-300">
                            Role
                        </label>
                        <select name="role" id="role"
                            class="h-11 w-full rounded-lg border border-gray-300 bg-transparent px-4 py-2.5 text-sm text-gray-800 shadow-theme-xs focus:border-blue-300 focus:outline-none focus:ring-2 focus:ring-blue-500/10 dark:border-gray-700 dark:bg-gray-900 dark:text-white/90 dark:focus:border-blue-800">
                            <option value="">All Roles</option>
                            @foreach($roles as $role)
                                <option value="{{ $role->name }}"
                                    {{ request('role') == $role->name ? 'selected' : '' }}>
                                    {{ $role->name }}
                                </option>
                            @endforeach
                        </select>
                    </div>
                </div>

                <div class="mt-6 flex justify-end space-x-3">
                    <a href="{{ route('users.index') }}" 
                        class="rounded-lg border border-gray-300 bg-white px-5 py-2.5 text-sm font-medium text-gray-700 shadow-theme-xs hover:bg-gray-50 hover:text-gray-800 dark:border-gray-700 dark:bg-gray-800 dark:text-gray-400 dark:hover:bg-white/[0.03] dark:hover:text-gray-200">
                        Clear Filters
                    </a>
                    <button type="submit"
                        class="rounded-lg bg-blue-500 px-5 py-2.5 text-sm font-medium text-white shadow-theme-xs hover:bg-blue-600 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:ring-offset-2 dark:focus:ring-offset-gray-900">
                        Apply Filters
                    </button>
                </div>
            </form>
        </div>

        <!-- Users Table -->
        <div class="rounded-2xl border border-gray-200 bg-white p-6 dark:border-gray-800 dark:bg-white/[0.03]">
            <div class="mb-6 flex items-center justify-between">
                <h3 class="text-lg font-semibold text-gray-800 dark:text-white/90">
                    <i class="bi bi-people me-2"></i>Users List
                </h3>
                <div class="flex space-x-3">
                   
                        <a href="{{ route('users.export') }}" 
                            class="inline-flex items-center rounded-lg border border-gray-300 bg-white px-4 py-2.5 text-sm font-medium text-gray-700 shadow-theme-xs hover:bg-gray-50 hover:text-gray-800 dark:border-gray-700 dark:bg-gray-800 dark:text-gray-400 dark:hover:bg-white/[0.03] dark:hover:text-gray-200">
                            <i class="bi bi-download me-2"></i> Export
                        </a>
                 
                    
                   
                        <a href="{{ route('users.create') }}" 
                            class="inline-flex items-center rounded-lg bg-blue-500 px-4 py-2.5 text-sm font-medium text-white shadow-theme-xs hover:bg-blue-600 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:ring-offset-2 dark:focus:ring-offset-gray-900">
                            <i class="bi bi-plus-lg me-2"></i> Add User
                        </a>
                 
                </div>
            </div>

            <!-- Users Table Component -->
            @php
                $userTransactions = $users->map(function($user) {
                    return [
                        'id' => $user->id,
                        'name' => $user->full_name,
                        'email' => $user->email,
                        'image' => 'https://ui-avatars.com/api/?name=' . urlencode($user->full_name) . '&background=random',
                        'date' => $user->created_at->format('M d, Y h:i A'),
                        'price' => '-', // Not applicable
                        'category' => $user->division?->name ?? 'N/A',
                        'status' => $user->email_verified_at ? 'Active' : 'Inactive',
                    ];
                })->toArray();
            @endphp

            <x-tables.users-table :users="$users" />


            <!-- Pagination Links -->
            @if($users->hasPages())
                <div class="mt-6">
                    {{ $users->links('vendor.pagination.dashboard') }}

                </div>
            @endif
        </div>
    </div>
@endsection