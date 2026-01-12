@extends('layouts.app')

@section('content')
    <x-common.page-breadcrumb pageTitle="Equipment Management" />

    <div class="space-y-6">
        <!-- Header -->
        <div class="flex flex-col gap-4 sm:flex-row sm:items-center sm:justify-between">
            <div>
                <h2 class="text-xl font-semibold text-gray-800 dark:text-white/90">
                    <i class="bi bi-pc-display me-2"></i>Equipment List
                </h2>
                <p class="mt-1 text-sm text-gray-500 dark:text-gray-400">
                    Manage all registered equipment for maintenance requests
                </p>
            </div>
            
            <div class="flex gap-2">
                <a href="{{ route('items.trashed') }}" 
                    class="rounded-lg border border-gray-300 bg-white px-4 py-2 text-sm font-medium text-gray-700 shadow-theme-xs hover:bg-gray-50 dark:border-gray-700 dark:bg-gray-800 dark:text-gray-400 dark:hover:bg-white/[0.03]">
                    <i class="bi bi-trash me-2"></i>Deleted Equipment
                </a>
                <a href="{{ route('items.export') }}" 
                    class="rounded-lg border border-gray-300 bg-white px-4 py-2 text-sm font-medium text-gray-700 shadow-theme-xs hover:bg-gray-50 dark:border-gray-700 dark:bg-gray-800 dark:text-gray-400 dark:hover:bg-white/[0.03]">
                    <i class="bi bi-download me-2"></i>Export CSV
                </a>
                <a href="{{ route('items.create') }}" 
                    class="rounded-lg bg-blue-500 px-4 py-2 text-sm font-medium text-white shadow-theme-xs hover:bg-blue-600 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:ring-offset-2 dark:focus:ring-offset-gray-900">
                    <i class="bi bi-plus-lg me-2"></i>Add Equipment
                </a>
            </div>
        </div>

        <!-- Statistics Cards -->
        <div class="grid grid-cols-1 gap-4 sm:grid-cols-2 lg:grid-cols-4">
            <x-common.stat-card 
                title="Total Equipment" 
                value="{{ $totalItems ?? 0 }}" 
                icon="bi bi-pc-display" 
                variant="primary" 
            />
            <x-common.stat-card 
                title="Active" 
                value="{{ $activeItems ?? 0 }}" 
                icon="bi bi-check-circle" 
                variant="success" 
            />
            <x-common.stat-card 
                title="Inactive" 
                value="{{ $inactiveItems ?? 0 }}" 
                icon="bi bi-x-circle" 
                variant="warning" 
            />
            <x-common.stat-card 
                title="Under Maintenance" 
                value="{{ $maintenanceItems ?? 0 }}" 
                icon="bi bi-tools" 
                variant="danger" 
            />
        </div>

        <!-- Filters Card -->
        <div class="rounded-2xl border border-gray-200 bg-white p-6 dark:border-gray-800 dark:bg-white/[0.03]">
            <h3 class="mb-4 text-sm font-semibold text-gray-800 dark:text-white/90">
                <i class="bi bi-funnel me-2"></i>Filter Equipment
            </h3>
            
            <form action="{{ route('items.index') }}" method="GET">
                <div class="grid grid-cols-1 gap-5 md:grid-cols-2 lg:grid-cols-4">
                    <!-- Search -->
                    <div>
                        <label class="mb-2 block text-sm font-medium text-gray-700 dark:text-gray-300">
                            Search
                        </label>
                        <div class="relative">
                            <input type="text" name="search" 
                                class="h-11 w-full rounded-lg border border-gray-300 bg-transparent px-4 py-2.5 pl-11 text-sm text-gray-800 shadow-theme-xs placeholder:text-gray-400 focus:border-blue-300 focus:outline-none focus:ring-2 focus:ring-blue-500/10 dark:border-gray-700 dark:bg-gray-900 dark:text-white/90 dark:placeholder:text-white/30 dark:focus:border-blue-800"
                                placeholder="Search equipment..."
                                value="{{ request('search') }}">
                            <i class="bi bi-search absolute left-3 top-1/2 -translate-y-1/2 text-gray-400"></i>
                        </div>
                    </div>

                    <!-- Type Filter -->
                    <div>
                        <label for="type" class="mb-2 block text-sm font-medium text-gray-700 dark:text-gray-300">
                            Type
                        </label>
                        <select name="type" id="type"
                            class="h-11 w-full rounded-lg border border-gray-300 bg-transparent px-4 py-2.5 text-sm text-gray-800 shadow-theme-xs focus:border-blue-300 focus:outline-none focus:ring-2 focus:ring-blue-500/10 dark:border-gray-700 dark:bg-gray-900 dark:text-white/90 dark:focus:border-blue-800">
                            <option value="">All Types</option>
                            @foreach(App\Models\Item::getTypeOptions() as $key => $value)
                                <option value="{{ $key }}" {{ request('type') == $key ? 'selected' : '' }}>
                                    {{ $value }}
                                </option>
                            @endforeach
                        </select>
                    </div>

                    <!-- Status Filter -->
                    <div>
                        <label for="status" class="mb-2 block text-sm font-medium text-gray-700 dark:text-gray-300">
                            Status
                        </label>
                        <select name="status" id="status"
                            class="h-11 w-full rounded-lg border border-gray-300 bg-transparent px-4 py-2.5 text-sm text-gray-800 shadow-theme-xs focus:border-blue-300 focus:outline-none focus:ring-2 focus:ring-blue-500/10 dark:border-gray-700 dark:bg-gray-900 dark:text-white/90 dark:focus:border-blue-800">
                            <option value="">All Status</option>
                            @foreach(App\Models\Item::getStatusOptions() as $key => $value)
                                <option value="{{ $key }}" {{ request('status') == $key ? 'selected' : '' }}>
                                    {{ $value }}
                                </option>
                            @endforeach
                        </select>
                    </div>

                    <!-- Actions -->
                    <div class="flex items-end">
                        <div class="flex w-full gap-2">
                            <button type="submit"
                                class="h-11 flex-1 rounded-lg bg-blue-500 px-4 py-2.5 text-sm font-medium text-white shadow-theme-xs hover:bg-blue-600 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:ring-offset-2 dark:focus:ring-offset-gray-900">
                                Apply Filters
                            </button>
                            <a href="{{ route('items.index') }}" 
                                class="h-11 rounded-lg border border-gray-300 bg-white px-4 py-2.5 text-sm font-medium text-gray-700 shadow-theme-xs hover:bg-gray-50 dark:border-gray-700 dark:bg-gray-800 dark:text-gray-400 dark:hover:bg-white/[0.03]">
                                Clear
                            </a>
                        </div>
                    </div>
                </div>
            </form>
        </div>

        <!-- Equipment Table -->
        <div class="rounded-2xl border border-gray-200 bg-white p-6 dark:border-gray-800 dark:bg-white/[0.03]">
            <h3 class="mb-6 text-lg font-semibold text-gray-800 dark:text-white/90">
                <i class="bi bi-list-ul me-2"></i>Registered Equipment
            </h3>

            @if($items->count() > 0)
                <!-- Equipment Table Component -->
                @php
                    $itemTransactions = $items->map(function($item) {
                        return [
                            'id' => $item->id,
                            'name' => $item->name,
                            'email' => $item->getTypeText(),
                            'image' => 'https://ui-avatars.com/api/?name=' . urlencode($item->name) . '&background=random',
                            'date' => $item->created_at->format('M d, Y'),
                            'price' => $item->getUnitText(),
                            'category' => $item->getTypeText(),
                            'status' => $item->getStatusText(),
                        ];
                    })->toArray();
                @endphp
<div class="overflow-x-auto">
    <table class="min-w-full border border-gray-200 dark:border-gray-800 rounded-lg">
        <thead class="bg-gray-50 dark:bg-gray-800">
            <tr>
                <th class="px-4 py-3 text-left text-sm font-semibold">#</th>

                <th class="px-4 py-3 text-left text-sm font-semibold">Equipment</th>
                <th class="px-4 py-3 text-left text-sm font-semibold">Type</th>
                <th class="px-4 py-3 text-left text-sm font-semibold">Unit</th>
                <th class="px-4 py-3 text-left text-sm font-semibold">Status</th>
                <th class="px-4 py-3 text-left text-sm font-semibold">Registered</th>
                <th class="px-4 py-3 text-center text-sm font-semibold">Actions</th>
            </tr>
        </thead>

        <tbody class="divide-y divide-gray-200 dark:divide-gray-700">
            @forelse($items as $index => $item)
                <tr class="hover:bg-gray-50 dark:hover:bg-gray-800/50">
                    <td class="px-4 py-3 text-sm text-gray-600 dark:text-gray-400">
    {{ $items->firstItem() + $index }}
</td>

                    <!-- Name + Avatar -->
                    <td class="px-4 py-3 flex items-center gap-3">
                        <img
                            src="https://ui-avatars.com/api/?name={{ urlencode($item->name) }}&background=random"
                            class="w-9 h-9 rounded-full"
                            alt="avatar"
                        >
                        <div>
                            <div class="font-medium text-gray-800 dark:text-white">
                                {{ $item->name }}
                            </div>
                            <div class="text-xs text-gray-500">
                             
                            </div>
                        </div>
                    </td>

                    <!-- Type -->
                    <td class="px-4 py-3 text-sm">
                        {{ $item->getTypeText() }}
                    </td>

                    <!-- Unit -->
                    <td class="px-4 py-3 text-sm">
                        {{ $item->getUnitText() }}
                    </td>

                    <!-- Status -->
                    <td class="px-4 py-3">
                        <span class="px-2 py-1 text-xs rounded {{ $item->getStatusBadgeClass() }}">
                            {{ $item->getStatusText() }}
                        </span>
                    </td>

                    <!-- Date -->
                    <td class="px-4 py-3 text-sm text-gray-600 dark:text-gray-400">
                        {{ $item->created_at->format('M d, Y') }}
                    </td>

                    <!-- Actions -->
                    <td class="px-4 py-3 text-center">
                        <div class="flex justify-center gap-3">
                            <a href="{{ route('items.show', $item) }}"
                               class="text-blue-600 hover:underline text-sm">
                                View
                            </a>

                            <a href="{{ route('items.edit', $item) }}"
                               class="text-yellow-600 hover:underline text-sm">
                                Edit
                            </a>

                            <form action="{{ route('items.destroy', $item) }}"
                                  method="POST"
                                  onsubmit="return confirm('Are you sure you want to delete this equipment?')">
                                @csrf
                                @method('DELETE')
                                <button class="text-red-600 hover:underline text-sm">
                                    Delete
                                </button>
                            </form>
                        </div>
                    </td>
                </tr>
            @empty
                <tr>
                    <td colspan="6" class="px-4 py-6 text-center text-gray-500">
                        No equipment found.
                    </td>
                </tr>
            @endforelse
        </tbody>
    </table>
</div>


@if($items->hasPages())
    <div class="mt-6">
        {{ $items->withQueryString()->links('vendor.pagination.dashboard') }}
    </div>
@endif

            @else
                <div class="py-12 text-center">
                    <div class="mx-auto mb-4 flex h-16 w-16 items-center justify-center rounded-full bg-gray-100 dark:bg-gray-800">
                        <i class="bi bi-pc-display text-2xl text-gray-400 dark:text-gray-500"></i>
                    </div>
                    <h3 class="mb-2 text-lg font-semibold text-gray-800 dark:text-white/90">No Equipment Found</h3>
                    <p class="text-gray-500 dark:text-gray-400">
                        @if(request()->hasAny(['search', 'type', 'status']))
                            No equipment matches your filters. Try adjusting your search criteria.
                        @else
                            No equipment has been registered yet. Start by adding your first equipment.
                        @endif
                    </p>
                    <a href="{{ route('items.create') }}" 
                        class="mt-4 inline-flex items-center rounded-lg bg-blue-500 px-4 py-2.5 text-sm font-medium text-white shadow-theme-xs hover:bg-blue-600 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:ring-offset-2 dark:focus:ring-offset-gray-900">
                        <i class="bi bi-plus-lg me-2"></i> Add First Equipment
                    </a>
                </div>
            @endif
        </div>

        <!-- Type Distribution -->
        <div class="rounded-2xl border border-gray-200 bg-white p-6 dark:border-gray-800 dark:bg-white/[0.03]">
            <h3 class="mb-4 text-sm font-semibold text-gray-800 dark:text-white/90">
                <i class="bi bi-pie-chart me-2"></i>Equipment by Type
            </h3>
            
            <div class="space-y-4">
                @foreach($typeCounts as $typeKey => $count)
                    @if($count > 0)
                        <div class="flex items-center justify-between">
                            <div class="flex items-center">
                                <div class="mr-3 flex h-8 w-8 items-center justify-center rounded-full bg-blue-100 dark:bg-blue-900/30">
                                    <i class="bi bi-{{ $typeKey === 'computer' ? 'pc-display' : ($typeKey === 'printer' ? 'printer' : ($typeKey === 'aircon' ? 'snow' : 'box')) }} text-blue-600 dark:text-blue-400"></i>
                                </div>
                                <span class="text-sm text-gray-600 dark:text-gray-400">
                                    {{ App\Models\Item::getTypeOptions()[$typeKey] ?? $typeKey }}
                                </span>
                            </div>
                            <div class="flex items-center gap-2">
                                <span class="text-sm font-medium text-gray-800 dark:text-white/90">{{ $count }}</span>
                                <span class="text-xs text-gray-500 dark:text-gray-400">
                                    ({{ $totalItems > 0 ? round(($count / $totalItems) * 100) : 0 }}%)
                                </span>
                            </div>
                        </div>
                    @endif
                @endforeach
            </div>
        </div>
    </div>

    <!-- Success Message -->
    @if(session('success'))
        <div class="fixed bottom-4 right-4 z-50">
            <x-ui.alert variant="success" title="Success" :message="session('success')" />
        </div>
    @endif

    <!-- Error Message -->
    @if(session('error'))
        <div class="fixed bottom-4 right-4 z-50">
            <x-ui.alert variant="error" title="Error" :message="session('error')" />
        </div>
    @endif
@endsection