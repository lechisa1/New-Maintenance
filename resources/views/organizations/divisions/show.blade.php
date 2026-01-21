@extends('layouts.app')

@section('content')
    <x-common.page-breadcrumb pageTitle="Division Details" :links="[
        ['label' => 'Organizations', 'url' => route('organizations.index')],
        ['label' => $division->cluster->organization->name],
        ['label' => $division->cluster->name],
        ['label' => $division->name],
    ]" />

    <div class="space-y-6">
        <div class="rounded-2xl border border-gray-200 bg-white p-6 shadow-sm dark:border-gray-800 dark:bg-white/[0.03]">
            <div class="flex flex-col gap-6 lg:flex-row lg:items-center lg:justify-between">
                <div>
                    <span
                        class="inline-flex items-center rounded-full bg-purple-100 px-2.5 py-0.5 text-xs font-medium text-purple-800 dark:bg-purple-900/30 dark:text-purple-400">
                        Division Level
                    </span>
                    <h2 class="mt-2 text-3xl font-bold text-gray-800 dark:text-white">{{ $division->name }}</h2>
                    <p class="text-sm text-gray-500">
                        Part of <span
                            class="font-semibold text-gray-700 dark:text-gray-300">{{ $division->cluster->name }}</span>
                        within <span
                            class="font-semibold text-gray-700 dark:text-gray-300">{{ $division->cluster->organization->name }}</span>
                    </p>
                </div>

                <div class="flex items-center gap-3">
                    <a href="{{ url()->previous() }}"
                        class="rounded-xl border border-gray-200 px-4 py-2 text-sm font-medium text-gray-600 hover:bg-gray-50 dark:border-gray-700 dark:text-gray-400">
                        <i class="bi bi-arrow-left me-1"></i> Back
                    </a>
                    <button
                        class="rounded-xl bg-blue-600 px-5 py-2 text-sm font-medium text-white hover:bg-blue-700 shadow-lg shadow-blue-200 dark:shadow-none">
                        Edit Details
                    </button>
                </div>
            </div>
        </div>

        <div class="grid grid-cols-1 gap-6 md:grid-cols-2 lg:grid-cols-3">

            <div class="rounded-2xl border border-gray-200 bg-white p-5 dark:border-gray-800 dark:bg-white/[0.03]">
                <div class="flex items-center gap-4">
                    <div
                        class="flex h-12 w-12 items-center justify-center rounded-xl bg-blue-400 text-blue-600 dark:bg-blue-900/20">
                        <i class="bi bi-building text-xl"></i>
                    </div>
                    <div>
                        <p class="text-xs font-medium uppercase tracking-wider text-gray-500">Organization</p>
                        <p class="font-bold text-gray-800 dark:text-white">{{ $division->cluster->organization->name }}</p>
                    </div>
                </div>
            </div>

            <div class="rounded-2xl border border-gray-200 bg-white p-5 dark:border-gray-800 dark:bg-white/[0.03]">
                <div class="flex items-center justify-between">
                    <div class="flex items-center gap-4">
                        <div
                            class="flex h-12 w-12 items-center justify-center rounded-xl bg-green-50 text-green-600 dark:bg-green-900/20">
                            <i class="bi bi-diagram-3 text-xl"></i>
                        </div>
                        <div>
                            <p class="text-xs font-medium uppercase tracking-wider text-gray-500">Parent Cluster</p>
                            <p class="font-bold text-gray-800 dark:text-white">{{ $division->cluster->name }}</p>
                        </div>
                    </div>
                </div>
                <div class="mt-4 border-t border-gray-100 pt-3 dark:border-gray-800">
                    <p class="text-xs text-gray-500">Cluster Chairman:</p>
                    <p class="text-sm font-semibold text-gray-700 dark:text-gray-300">
                        <i class="bi bi-person me-1"></i>
                        {{ $division->cluster->chairman->full_name ?? 'No Chairman Assigned' }}
                    </p>
                </div>
            </div>

            <div class="rounded-2xl border-2 border-blue-500 bg-blue-50/30 p-5 dark:bg-blue-900/10">
                <div class="flex items-center gap-4">
                    <div class="flex h-12 w-12 items-center justify-center rounded-xl bg-blue-600 text-white">
                        <i class="bi bi-person-badge text-xl"></i>
                    </div>
                    <div>
                        <p class="text-xs font-bold uppercase tracking-wider text-blue-600 dark:text-blue-400">Division
                            Chairman</p>
                        <p class="text-lg font-black text-gray-900 dark:text-white">
                            {{ $division->chairman->full_name ?? 'Vacant Position' }}
                        </p>
                    </div>
                </div>
                <div class="mt-4 flex items-center justify-between text-xs text-blue-700 dark:text-blue-300">
                    <span>Direct Supervisor of {{ $division->users_count }} members</span>
                    <i class="bi bi-shield-check"></i>
                </div>
            </div>

        </div>

        <div class="rounded-2xl border border-gray-200 bg-white dark:border-gray-800 dark:bg-white/[0.03]">
            <div class="flex items-center justify-between border-b border-gray-100 p-5 dark:border-gray-800">
                <h3 class="text-lg font-bold text-gray-800 dark:text-white">Personnel Directory</h3>
                <span
                    class="rounded-lg bg-gray-100 px-3 py-1 text-xs font-bold dark:bg-gray-800">{{ $division->users_count }}
                    Total</span>
            </div>
            <div class="p-12 text-center">
                <div
                    class="mx-auto flex h-16 w-16 items-center justify-center rounded-full bg-gray-50 text-gray-300 dark:bg-gray-800/50">
                    <i class="bi bi-people text-3xl"></i>
                </div>
                <h4 class="mt-4 font-semibold text-gray-800 dark:text-white">No Team Members Listed Yet</h4>
                <p class="text-sm text-gray-500">Personnel data for this division is coming soon.</p>
            </div>
        </div>
    </div>
@endsection
