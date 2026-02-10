@extends('layouts.app')
@php
    $breadcrumbs = [
        ['label' => 'Home', 'url' => url('/')],
        ['label' => 'Base Data Management'], // active page
    ];
@endphp

@section('content')
    <x-common.page-breadcrumb :breadcrumbs="$breadcrumbs" />
    @include('maintenance-requests.partials.alerts')

    <div class="space-y-6">
        <div class="rounded-2xl border border-gray-200 bg-white p-6 dark:border-gray-800 dark:bg-white/[0.03]">
            <div class="flex flex-col lg:flex-row lg:items-center justify-between gap-6">
                <div>
                    <h2 class="text-xl font-bold text-gray-900 dark:text-white flex items-center gap-2">
                        <span class="flex h-8 w-8 items-center justify-center rounded-lg bg-blue-600 text-white">
                            <i class="bi bi-database-fill text-sm"></i>
                        </span>
                        System Base Data
                    </h2>
                    <p class="mt-1 text-sm text-gray-500 dark:text-gray-400">
                        Configure foundational parameters and master records for the maintenance workflow.
                    </p>
                </div>

                <div class="flex flex-wrap gap-3">
                    <div
                        class="flex items-center gap-3 rounded-xl border border-gray-100 bg-gray-50/50 px-4 py-2 dark:border-gray-700 dark:bg-gray-800/50">
                        <div class="text-blue-600 dark:text-blue-400"><i class="bi bi-layers"></i></div>
                        <div>
                            <p class="text-[10px] font-bold uppercase tracking-wider text-gray-400">Modules</p>
                            <p class="text-sm font-bold text-gray-800 dark:text-white">{{ count($modules) }}</p>
                        </div>
                    </div>
                    <div
                        class="flex items-center gap-3 rounded-xl border border-gray-100 bg-gray-50/50 px-4 py-2 dark:border-gray-700 dark:bg-gray-800/50">
                        <div class="text-green-600 dark:text-green-400"><i class="bi bi-check-all"></i></div>
                        <div>
                            <p class="text-[10px] font-bold uppercase tracking-wider text-gray-400">Total Records</p>
                            <p class="text-sm font-bold text-gray-800 dark:text-white">
                                {{ array_sum(array_column($modules, 'count')) }}
                            </p>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
            @foreach ($modules as $module)
                <a href="{{ route($module['route']) }}"
                    class="group relative overflow-hidden rounded-2xl border border-gray-200 bg-white p-6 transition-all duration-300 hover:-translate-y-1 hover:border-blue-500 hover:shadow-xl hover:shadow-blue-500/10 dark:border-gray-800 dark:bg-white/[0.03] dark:hover:border-blue-500">

                    <div class="relative z-10">
                        <div class="flex items-center justify-between mb-4">
                            <div
                                class="flex h-12 w-12 items-center justify-center rounded-xl {{ $module['bg_color'] }} bg-opacity-10 transition-colors group-hover:bg-opacity-100">
                                <i
                                    class="{{ $module['icon'] }} text-xl {{ $module['text_color'] }} group-hover:text-white"></i>
                            </div>
                            <span
                                class="text-xs font-bold px-2.5 py-1 rounded-full bg-gray-100 text-gray-600 dark:bg-gray-800 dark:text-gray-400">
                                {{ $module['count'] }} items
                            </span>
                        </div>

                        <h4 class="text-lg font-bold text-gray-900 dark:text-white">
                            {{ $module['title'] }}
                        </h4>
                        <p class="mt-2 text-sm leading-relaxed text-gray-500 dark:text-gray-400">
                            {{ $module['description'] }}
                        </p>

                        <div class="mt-6 flex items-center gap-2 text-sm font-bold {{ $module['text_color'] }}">
                            Manage Master Data
                            <i class="bi bi-arrow-right transition-transform group-hover:translate-x-1"></i>
                        </div>
                    </div>

                    <div
                        class="absolute -right-4 -bottom-4 h-24 w-24 opacity-[0.03] transition-opacity group-hover:opacity-[0.08]">
                        <i class="{{ $module['icon'] }} text-8xl"></i>
                    </div>
                </a>
            @endforeach

            <div
                class="flex flex-col items-center justify-center rounded-2xl border-2 border-dashed border-gray-200 p-6 text-center dark:border-gray-800">
                <div class="flex h-12 w-12 items-center justify-center rounded-full bg-gray-50 dark:bg-gray-800">
                    <i class="bi bi-plus-circle text-gray-400"></i>
                </div>
                <h4 class="mt-4 text-sm font-semibold text-gray-500">More Modules</h4>
                <p class="mt-1 text-xs text-gray-400">Expandable base data system</p>
            </div>
        </div>

        <div class="rounded-2xl border border-gray-200 bg-white p-1 dark:border-gray-800 dark:bg-white/[0.03]">
            <div class="grid grid-cols-2 lg:grid-cols-4 gap-1">
                <a href="{{ route('items.create') }}"
                    class="flex items-center justify-center gap-2 rounded-xl px-4 py-3 text-sm font-semibold text-gray-600 transition hover:bg-gray-50 dark:text-gray-400 dark:hover:bg-gray-800">
                    <i class="bi bi-plus-lg text-blue-500"></i> New Item
                </a>
                <a href="{{ route('issue-types.create') }}"
                    class="flex items-center justify-center gap-2 rounded-xl px-4 py-3 text-sm font-semibold text-gray-600 transition hover:bg-gray-50 dark:text-gray-400 dark:hover:bg-gray-800">
                    <i class="bi bi-plus-lg text-green-500"></i> New Issue Type
                </a>
                <a href="{{ route('items.index', ['export' => 'excel']) }}"
                    class="flex items-center justify-center gap-2 rounded-xl px-4 py-3 text-sm font-semibold text-gray-600 transition hover:bg-gray-50 dark:text-gray-400 dark:hover:bg-gray-800">
                    <i class="bi bi-file-earmark-excel text-amber-500"></i> Export Excel
                </a>
                <button onclick="window.location.reload()"
                    class="flex items-center justify-center gap-2 rounded-xl px-4 py-3 text-sm font-semibold text-gray-600 transition hover:bg-gray-50 dark:text-gray-400 dark:hover:bg-gray-800">
                    <i class="bi bi-arrow-clockwise text-purple-500"></i> Refresh
                </button>
            </div>
        </div>
    </div>
@endsection
