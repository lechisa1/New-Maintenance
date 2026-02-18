@extends('layouts.app')
@php
    $breadcrumbs = [
        ['label' => 'Home', 'url' => url('/')],
        ['label' => 'Base Data', 'url' => route('base-data.index')],
        ['label' => 'Equipment Management'], // Active page
    ];
@endphp

@section('content')
    <x-common.page-breadcrumb :breadcrumbs="$breadcrumbs" />

    @include('maintenance-requests.partials.alerts')
    <div class="grid grid-cols-1 gap-6 lg:grid-cols-3">
        <!-- Left Column - Registration Form -->
        <div class="lg:col-span-2">
            <div class="rounded-2xl border border-gray-200 bg-white p-6 dark:border-gray-800 dark:bg-white/[0.03]">
                <h3 class="mb-6 text-lg font-semibold text-gray-800 dark:text-white/90">
                    <i class="bi bi-pc-display me-2"></i>Register New Equipment
                </h3>
                <hr class="mb-6 border-gray-200 dark:border-gray-700">

                <form method="POST" action="{{ route('items.store') }}">
                    @csrf

                    <div class="space-y-6">
                        <!-- Equipment Name -->
                        <div>
                            <label class="mb-2 block text-sm font-medium text-gray-700 dark:text-gray-300">
                                Equipment Name <span class="text-red-500">*</span>
                            </label>
                            <input type="text" name="name" required value="{{ old('name') }}"
                                class="h-11 w-full rounded-lg border border-gray-300 bg-transparent px-4 py-2.5 text-sm text-gray-800 shadow-theme-xs focus:border-blue-300 focus:outline-none focus:ring-2 focus:ring-blue-500/10 dark:border-gray-700 dark:bg-gray-900 dark:text-white/90 dark:focus:border-blue-800 @error('name') border-red-500 dark:border-red-500 @enderror"
                                placeholder="E.g., Office Computer, Laser Printer, Meeting Table">
                            @error('name')
                                <div class="mt-2 text-sm text-red-600 dark:text-red-400">{{ $message }}</div>
                            @enderror
                            <div class="mt-2 text-xs text-gray-500 dark:text-gray-400">
                                Enter a clear and descriptive name for the equipment
                            </div>
                        </div>

                        <!-- Equipment Type -->
                        <div>
                            <label class="mb-2 block text-sm font-medium text-gray-700 dark:text-gray-300">
                                Equipment Type <span class="text-red-500">*</span>
                            </label>
                            <select name="type" required
                                class="h-11 w-full rounded-lg border border-gray-300 bg-transparent px-4 py-2.5 text-sm text-gray-800 shadow-theme-xs focus:border-blue-300 focus:outline-none focus:ring-2 focus:ring-blue-500/10 dark:border-gray-700 dark:bg-gray-900 dark:text-white/90 dark:focus:border-blue-800 @error('type') border-red-500 dark:border-red-500 @enderror">
                                <option value="" disabled selected>Select equipment type</option>
                                @foreach (App\Models\Item::getTypeOptions() as $key => $value)
                                    <option value="{{ $key }}" {{ old('type') == $key ? 'selected' : '' }}>
                                        {{ $value }}
                                    </option>
                                @endforeach
                            </select>
                            @error('type')
                                <div class="mt-2 text-sm text-red-600 dark:text-red-400">{{ $message }}</div>
                            @enderror
                        </div>

                        <!-- Unit of Measure -->
                        <div>
                            <label class="mb-2 block text-sm font-medium text-gray-700 dark:text-gray-300">
                                Unit of Measure <span class="text-red-500">*</span>
                            </label>
                            <select name="unit" required
                                class="h-11 w-full rounded-lg border border-gray-300 bg-transparent px-4 py-2.5 text-sm text-gray-800 shadow-theme-xs focus:border-blue-300 focus:outline-none focus:ring-2 focus:ring-blue-500/10 dark:border-gray-700 dark:bg-gray-900 dark:text-white/90 dark:focus:border-blue-800 @error('unit') border-red-500 dark:border-red-500 @enderror">
                                <option value="" disabled selected>Select unit</option>
                                @foreach (App\Models\Item::getUnitOptions() as $key => $value)
                                    <option value="{{ $key }}" {{ old('unit') == $key ? 'selected' : '' }}>
                                        {{ $value }}
                                    </option>
                                @endforeach
                            </select>
                            @error('unit')
                                <div class="mt-2 text-sm text-red-600 dark:text-red-400">{{ $message }}</div>
                            @enderror
                            <div class="mt-2 text-xs text-gray-500 dark:text-gray-400">
                                How this equipment is measured or counted
                            </div>
                        </div>

                        <!-- Status -->
                        <div>
                            <label class="mb-2 block text-sm font-medium text-gray-700 dark:text-gray-300">
                                Status <span class="text-red-500">*</span>
                            </label>
                            <div class="grid grid-cols-3 gap-3">
                                @foreach (App\Models\Item::getStatusOptions() as $key => $value)
                                    <label class="cursor-pointer">
                                        <input type="radio" name="status" value="{{ $key }}"
                                            {{ old('status', 'active') == $key ? 'checked' : '' }} required
                                            class="peer sr-only">
                                        @php
                                            $colors = [
                                                'active' => [
                                                    'border' => 'border-green-500',
                                                    'bg' => 'bg-green-50',
                                                    'dot' => 'bg-green-500',
                                                    'dark_bg' => 'dark:bg-green-900/20',
                                                    'dark_border' => 'dark:border-green-500',
                                                ],
                                                'inactive' => [
                                                    'border' => 'border-gray-500',
                                                    'bg' => 'bg-gray-50',
                                                    'dot' => 'bg-gray-500',
                                                    'dark_bg' => 'dark:bg-gray-800',
                                                    'dark_border' => 'dark:border-gray-500',
                                                ],
                                                'maintenance' => [
                                                    'border' => 'border-yellow-500',
                                                    'bg' => 'bg-yellow-50',
                                                    'dot' => 'bg-yellow-500',
                                                    'dark_bg' => 'dark:bg-yellow-900/20',
                                                    'dark_border' => 'dark:border-yellow-500',
                                                ],
                                            ];
                                            $color = $colors[$key] ?? $colors['active'];
                                        @endphp
                                        <div
                                            class="rounded-lg border-2 p-4 text-center transition-all peer-checked:{{ $color['border'] }} peer-checked:{{ $color['bg'] }} dark:border-gray-700 peer-checked:{{ $color['dark_border'] }} peer-checked:{{ $color['dark_bg'] }}">
                                            <div class="flex items-center justify-center gap-2">
                                                <div class="h-3 w-3 rounded-full {{ $color['dot'] }}"></div>
                                                <div class="text-sm font-medium text-gray-800 dark:text-white/90">
                                                    {{ $value }}</div>
                                            </div>
                                            <div class="mt-1 text-xs text-gray-600 dark:text-gray-400">
                                                @if ($key === 'active')
                                                    Available for maintenance
                                                @elseif($key === 'inactive')
                                                    Not in use
                                                @else
                                                    Currently being repaired
                                                @endif
                                            </div>
                                        </div>
                                    </label>
                                @endforeach
                            </div>
                            @error('status')
                                <div class="mt-2 text-sm text-red-600 dark:text-red-400">{{ $message }}</div>
                            @enderror
                        </div>

                        <!-- Submit Buttons -->
                        <div class="border-t border-gray-200 pt-6 dark:border-gray-700">
                            <div class="flex justify-end gap-3">
                                <a href="{{ route('items.index') }}"
                                    class="rounded-lg border border-gray-300 bg-white px-6 py-3 text-sm font-medium text-gray-700 shadow-theme-xs hover:bg-gray-50 dark:border-gray-700 dark:bg-gray-800 dark:text-gray-400 dark:hover:bg-white/[0.03]">
                                    <i class="bi bi-x-lg me-2"></i>Cancel
                                </a>
                                <button type="submit"
                                    class="rounded-lg bg-blue-500 px-6 py-3 text-sm font-medium text-white shadow-theme-xs hover:bg-blue-600 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:ring-offset-2 dark:focus:ring-offset-gray-900">
                                    <i class="bi bi-save me-2"></i>Register Equipment
                                </button>
                            </div>
                        </div>
                    </div>
                </form>
            </div>
        </div>

        <!-- Right Column - Stats & Info -->
        <div class="space-y-6">
            <!-- Recently Added Equipment -->
            <div class="rounded-2xl border border-gray-200 bg-white p-6 dark:border-gray-800 dark:bg-white/[0.03]">
                <h3 class="mb-4 text-sm font-semibold text-gray-800 dark:text-white/90">
                    <i class="bi bi-clock-history me-2"></i>Recently Added
                </h3>

                <div class="space-y-3">
                    @php
                        $recentItems = App\Models\Item::latest()->take(4)->get();
                    @endphp

                    @forelse($recentItems as $recentItem)
                        <div class="rounded-lg border border-gray-200 p-3 dark:border-gray-700">
                            <div class="flex items-center justify-between">
                                <div>
                                    <div class="font-medium text-gray-800 dark:text-white/90">{{ $recentItem->name }}</div>
                                    <div class="text-sm text-gray-600 dark:text-gray-400">
                                        {{ $recentItem->getTypeText() }} • {{ $recentItem->getUnitText() }}
                                    </div>
                                </div>
                                <span
                                    class="rounded-full px-2 py-0.5 text-xs font-medium {{ $recentItem->getStatusBadgeClass() }}">
                                    {{ $recentItem->getStatusText() }}
                                </span>
                            </div>
                        </div>
                    @empty
                        <div class="rounded-lg border border-dashed border-gray-300 p-4 text-center dark:border-gray-700">
                            <i class="bi bi-pc-display text-2xl text-gray-400"></i>
                            <p class="mt-2 text-sm text-gray-500 dark:text-gray-400">No equipment registered yet</p>
                        </div>
                    @endforelse
                </div>
            </div>

            <!-- Equipment Statistics -->
            <div class="rounded-2xl border border-gray-200 bg-white p-6 dark:border-gray-800 dark:bg-white/[0.03]">
                <h3 class="mb-4 text-sm font-semibold text-gray-800 dark:text-white/90">
                    <i class="bi bi-bar-chart me-2"></i>Equipment Summary
                </h3>

                <div class="space-y-4">
                    <div class="grid grid-cols-2 gap-3">
                        <div class="rounded-lg bg-gray-50 p-3 dark:bg-gray-800">
                            <div class="text-xs text-gray-500 dark:text-gray-400">Total Equipment</div>
                            <div class="mt-1 text-lg font-semibold text-gray-800 dark:text-white/90">
                                {{ App\Models\Item::count() }}
                            </div>
                        </div>

                        <div class="rounded-lg bg-gray-50 p-3 dark:bg-gray-800">
                            <div class="text-xs text-gray-500 dark:text-gray-400">Active</div>
                            <div class="mt-1 text-lg font-semibold text-green-600 dark:text-green-400">
                                {{ App\Models\Item::active()->count() }}
                            </div>
                        </div>
                    </div>

                    <div>
                        <div class="mb-2 text-xs text-gray-500 dark:text-gray-400">Status Distribution</div>
                        <div class="space-y-2">
                            @foreach (App\Models\Item::getStatusOptions() as $key => $value)
                                @php
                                    $count = App\Models\Item::where('status', $key)->count();
                                    $total = App\Models\Item::count();
                                    $percentage = $total > 0 ? ($count / $total) * 100 : 0;
                                @endphp
                                <div class="flex items-center justify-between">
                                    <span class="text-sm text-gray-600 dark:text-gray-400">{{ $value }}</span>
                                    <span class="text-sm font-medium text-gray-800 dark:text-white/90">
                                        {{ $count }}
                                        <span class="text-xs text-gray-500">({{ round($percentage) }}%)</span>
                                    </span>
                                </div>
                            @endforeach
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Success Message -->
    @if (session('success'))
        <div class="fixed bottom-4 right-4 z-50">
            <x-ui.alert variant="success" title="Success" :message="session('success')" />
        </div>
    @endif
@endsection
