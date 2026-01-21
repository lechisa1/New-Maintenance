@extends('layouts.app')

@section('content')
    <x-common.page-breadcrumb pageTitle="Base Data Management" />

    <div class="grid grid-cols-1 gap-6">
        <!-- Header -->
        <div class="rounded-2xl border border-gray-200 bg-white p-6 dark:border-gray-800 dark:bg-white/[0.03]">
            <div class="mb-4">
                <h2 class="text-2xl font-bold text-gray-800 dark:text-white">
                    <i class="bi bi-database me-2"></i>Base Data Management
                </h2>
                <p class="mt-1 text-sm text-gray-500">
                    Manage all foundational data for the maintenance system
                </p>
            </div>
            
            <!-- Statistics -->
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-4">
                <div class="rounded-xl border border-gray-200 bg-gray-50 p-4 dark:border-gray-700 dark:bg-gray-900">
                    <div class="flex items-center">
                        <div class="mr-4 flex h-12 w-12 items-center justify-center rounded-lg bg-blue-100 dark:bg-blue-900">
                            <i class="bi bi-database text-blue-600 dark:text-blue-400"></i>
                        </div>
                        <div>
                            <p class="text-sm text-gray-500 dark:text-gray-400">Total Modules</p>
                            <p class="text-2xl font-bold text-gray-800 dark:text-white">{{ count($modules) }}</p>
                        </div>
                    </div>
                </div>
                
                <div class="rounded-xl border border-gray-200 bg-gray-50 p-4 dark:border-gray-700 dark:bg-gray-900">
                    <div class="flex items-center">
                        <div class="mr-4 flex h-12 w-12 items-center justify-center rounded-lg bg-green-100 dark:bg-green-900">
                            <i class="bi bi-box text-green-600 dark:text-green-400"></i>
                        </div>
                        <div>
                            <p class="text-sm text-gray-500 dark:text-gray-400">Total Items</p>
                            <p class="text-2xl font-bold text-gray-800 dark:text-white">{{ $modules[0]['count'] ?? 0 }}</p>
                        </div>
                    </div>
                </div>
                
                <div class="rounded-xl border border-gray-200 bg-gray-50 p-4 dark:border-gray-700 dark:bg-gray-900">
                    <div class="flex items-center">
                        <div class="mr-4 flex h-12 w-12 items-center justify-center rounded-lg bg-purple-100 dark:bg-purple-900">
                            <i class="bi bi-tag text-purple-600 dark:text-purple-400"></i>
                        </div>
                        <div>
                            <p class="text-sm text-gray-500 dark:text-gray-400">Total Issue Types</p>
                            <p class="text-2xl font-bold text-gray-800 dark:text-white">{{ $modules[1]['count'] ?? 0 }}</p>
                        </div>
                    </div>
                </div>
                
                <div class="rounded-xl border border-gray-200 bg-gray-50 p-4 dark:border-gray-700 dark:bg-gray-900">
                    <div class="flex items-center">
                        <div class="mr-4 flex h-12 w-12 items-center justify-center rounded-lg bg-yellow-100 dark:bg-yellow-900">
                            <i class="bi bi-check-circle text-yellow-600 dark:text-yellow-400"></i>
                        </div>
                        <div>
                            <p class="text-sm text-gray-500 dark:text-gray-400">Active Records</p>
                            <p class="text-2xl font-bold text-gray-800 dark:text-white">
                                {{ ($modules[0]['count'] ?? 0) + ($modules[1]['count'] ?? 0) }}
                            </p>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Base Data Modules Grid -->
        <div class="rounded-2xl border border-gray-200 bg-white p-6 dark:border-gray-800 dark:bg-white/[0.03]">
            <div class="mb-6">
                <h3 class="text-lg font-semibold text-gray-800 dark:text-white/90">
                    <i class="bi bi-grid me-2"></i>Base Data Modules
                </h3>
                <p class="mt-1 text-sm text-gray-500">
                    Click on any module to manage its data
                </p>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
                @foreach($modules as $module)
                    <a href="{{ route($module['route']) }}" 
                       class="group block rounded-xl border p-6 transition-all duration-200 hover:shadow-lg {{ $module['border_color'] }} {{ $module['bg_color'] }}">
                        <div class="flex items-start">
                            <div class="mr-4 flex h-14 w-14 items-center justify-center rounded-xl {{ $module['bg_color'] }} group-hover:scale-110 transition-transform duration-200">
                                <i class="{{ $module['icon'] }} text-2xl {{ $module['text_color'] }}"></i>
                            </div>
                            <div class="flex-1">
                                <div class="flex items-center justify-between">
                                    <h4 class="text-lg font-semibold text-gray-800 dark:text-white">
                                        {{ $module['title'] }}
                                    </h4>
                                    <span class="rounded-full {{ $module['bg_color'] }} px-3 py-1 text-sm font-medium {{ $module['text_color'] }}">
                                        {{ $module['count'] }} records
                                    </span>
                                </div>
                                <p class="mt-2 text-sm text-gray-600 dark:text-gray-400">
                                    {{ $module['description'] }}
                                </p>
                                <div class="mt-4 flex items-center text-sm font-medium {{ $module['text_color'] }}">
                                    <span>Manage Module</span>
                                    <i class="bi bi-arrow-right ms-2 transition-transform group-hover:translate-x-1"></i>
                                </div>
                            </div>
                        </div>
                    </a>
                @endforeach
                
                <!-- Add New Module Card (Optional) -->
                <div class="rounded-xl border border-dashed border-gray-300 p-6 dark:border-gray-700">
                    <div class="flex h-full flex-col items-center justify-center text-center">
                        <div class="mb-4 flex h-14 w-14 items-center justify-center rounded-xl bg-gray-100 dark:bg-gray-800">
                            <i class="bi bi-plus-lg text-2xl text-gray-400"></i>
                        </div>
                        <h4 class="mb-2 text-lg font-semibold text-gray-700 dark:text-gray-300">
                            Add New Module
                        </h4>
                        <p class="mb-4 text-sm text-gray-500">
                            Coming soon - Add more base data modules
                        </p>
                        <button disabled
                            class="cursor-not-allowed rounded-lg border border-gray-300 px-4 py-2 text-sm font-medium text-gray-400 dark:border-gray-700">
                            Coming Soon
                        </button>
                    </div>
                </div>
            </div>
        </div>

        <!-- Quick Actions -->
        <div class="rounded-2xl border border-gray-200 bg-white p-6 dark:border-gray-800 dark:bg-white/[0.03]">
            <div class="mb-6">
                <h3 class="text-lg font-semibold text-gray-800 dark:text-white/90">
                    <i class="bi bi-lightning me-2"></i>Quick Actions
                </h3>
                <p class="mt-1 text-sm text-gray-500">
                    Perform common base data tasks quickly
                </p>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-4">
                <a href="{{ route('items.create') }}"
                    class="rounded-xl border border-blue-200 bg-blue-50 p-4 text-center transition-colors hover:bg-blue-100 dark:border-blue-800 dark:bg-blue-900/20 dark:hover:bg-blue-900/30">
                    <i class="bi bi-plus-circle text-2xl text-blue-600 dark:text-blue-400"></i>
                    <p class="mt-2 font-medium text-blue-700 dark:text-blue-300">Add New Item</p>
                </a>
                
                <a href="{{ route('issue-types.create') }}"
                    class="rounded-xl border border-green-200 bg-green-50 p-4 text-center transition-colors hover:bg-green-100 dark:border-green-800 dark:bg-green-900/20 dark:hover:bg-green-900/30">
                    <i class="bi bi-plus-circle text-2xl text-green-600 dark:text-green-400"></i>
                    <p class="mt-2 font-medium text-green-700 dark:text-green-300">Add Issue Type</p>
                </a>
                
                <a href="{{ route('items.index') }}?export=excel"
                    class="rounded-xl border border-yellow-200 bg-yellow-50 p-4 text-center transition-colors hover:bg-yellow-100 dark:border-yellow-800 dark:bg-yellow-900/20 dark:hover:bg-yellow-900/30">
                    <i class="bi bi-download text-2xl text-yellow-600 dark:text-yellow-400"></i>
                    <p class="mt-2 font-medium text-yellow-700 dark:text-yellow-300">Export Data</p>
                </a>
                
                <a href="{{ route('base-data.index') }}"
                    class="rounded-xl border border-gray-200 bg-gray-50 p-4 text-center transition-colors hover:bg-gray-100 dark:border-gray-700 dark:bg-gray-900 dark:hover:bg-gray-800">
                    <i class="bi bi-arrow-clockwise text-2xl text-gray-600 dark:text-gray-400"></i>
                    <p class="mt-2 font-medium text-gray-700 dark:text-gray-300">Refresh</p>
                </a>
            </div>
        </div>
    </div>
@endsection