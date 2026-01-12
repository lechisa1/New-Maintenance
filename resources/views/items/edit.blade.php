@extends('layouts.app')

@section('content')
    <x-common.page-breadcrumb pageTitle="Edit Equipment" />

    <div class="grid grid-cols-1 gap-6 lg:grid-cols-3">
        <!-- Left Column - Edit Form -->
        <div class="lg:col-span-2">
            <div class="rounded-2xl border border-gray-200 bg-white p-6 dark:border-gray-800 dark:bg-white/[0.03]">
                <div class="mb-6 flex items-center justify-between">
                    <div>
                        <h3 class="text-lg font-semibold text-gray-800 dark:text-white/90">
                            <i class="bi bi-pencil-square me-2"></i>Edit Equipment
                        </h3>
                        <p class="mt-1 text-sm text-gray-500 dark:text-gray-400">
                            Equipment ID: #{{ $item->id }}
                        </p>
                    </div>
                    <span class="rounded-full px-3 py-1 text-xs font-medium {{ $item->getStatusBadgeClass() }}">
                        {{ $item->getStatusText() }}
                    </span>
                </div>
                <hr class="mb-6 border-gray-200 dark:border-gray-700">

                <form method="POST" action="{{ route('items.update', $item) }}">
                    @csrf
                    @method('PUT')
                    
                    <div class="space-y-6">
                        <!-- Equipment Name -->
                        <div>
                            <label class="mb-2 block text-sm font-medium text-gray-700 dark:text-gray-300">
                                Equipment Name <span class="text-red-500">*</span>
                            </label>
                            <input type="text" name="name" required value="{{ old('name', $item->name) }}"
                                class="h-11 w-full rounded-lg border border-gray-300 bg-transparent px-4 py-2.5 text-sm text-gray-800 shadow-theme-xs focus:border-blue-300 focus:outline-none focus:ring-2 focus:ring-blue-500/10 dark:border-gray-700 dark:bg-gray-900 dark:text-white/90 dark:focus:border-blue-800 @error('name') border-red-500 dark:border-red-500 @enderror"
                                placeholder="E.g., Office Computer, Laser Printer, Meeting Table">
                            @error('name')
                                <div class="mt-2 text-sm text-red-600 dark:text-red-400">{{ $message }}</div>
                            @enderror
                        </div>

                        <!-- Equipment Type -->
                        <div>
                            <label class="mb-2 block text-sm font-medium text-gray-700 dark:text-gray-300">
                                Equipment Type <span class="text-red-500">*</span>
                            </label>
                            <select name="type" required
                                class="h-11 w-full rounded-lg border border-gray-300 bg-transparent px-4 py-2.5 text-sm text-gray-800 shadow-theme-xs focus:border-blue-300 focus:outline-none focus:ring-2 focus:ring-blue-500/10 dark:border-gray-700 dark:bg-gray-900 dark:text-white/90 dark:focus:border-blue-800 @error('type') border-red-500 dark:border-red-500 @enderror">
                                <option value="" disabled>Select equipment type</option>
                                @foreach(App\Models\Item::getTypeOptions() as $key => $value)
                                    <option value="{{ $key }}" {{ old('type', $item->type) == $key ? 'selected' : '' }}>
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
                                <option value="" disabled>Select unit</option>
                                @foreach(App\Models\Item::getUnitOptions() as $key => $value)
                                    <option value="{{ $key }}" {{ old('unit', $item->unit) == $key ? 'selected' : '' }}>
                                        {{ $value }}
                                    </option>
                                @endforeach
                            </select>
                            @error('unit')
                                <div class="mt-2 text-sm text-red-600 dark:text-red-400">{{ $message }}</div>
                            @enderror
                        </div>

                        <!-- Status -->
                        <div>
                            <label class="mb-2 block text-sm font-medium text-gray-700 dark:text-gray-300">
                                Status <span class="text-red-500">*</span>
                            </label>
                            <div class="grid grid-cols-3 gap-3">
                                @foreach(App\Models\Item::getStatusOptions() as $key => $value)
                                    <label class="cursor-pointer">
                                        <input type="radio" name="status" value="{{ $key }}" 
                                            {{ old('status', $item->status) == $key ? 'checked' : '' }} required
                                            class="peer sr-only">
                                        @php
                                            $colors = [
                                                'active' => ['border' => 'border-green-500', 'bg' => 'bg-green-50', 'dot' => 'bg-green-500', 'dark_bg' => 'dark:bg-green-900/20', 'dark_border' => 'dark:border-green-500'],
                                                'inactive' => ['border' => 'border-gray-500', 'bg' => 'bg-gray-50', 'dot' => 'bg-gray-500', 'dark_bg' => 'dark:bg-gray-800', 'dark_border' => 'dark:border-gray-500'],
                                                'maintenance' => ['border' => 'border-yellow-500', 'bg' => 'bg-yellow-50', 'dot' => 'bg-yellow-500', 'dark_bg' => 'dark:bg-yellow-900/20', 'dark_border' => 'dark:border-yellow-500'],
                                            ];
                                            $color = $colors[$key] ?? $colors['active'];
                                        @endphp
                                        <div class="rounded-lg border-2 p-4 text-center transition-all peer-checked:{{ $color['border'] }} peer-checked:{{ $color['bg'] }} dark:border-gray-700 peer-checked:{{ $color['dark_border'] }} peer-checked:{{ $color['dark_bg'] }}">
                                            <div class="flex items-center justify-center gap-2">
                                                <div class="h-3 w-3 rounded-full {{ $color['dot'] }}"></div>
                                                <div class="text-sm font-medium text-gray-800 dark:text-white/90">{{ $value }}</div>
                                            </div>
                                            <div class="mt-1 text-xs text-gray-600 dark:text-gray-400">
                                                @if($key === 'active')
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
                            <div class="flex justify-between">
                                <form action="{{ route('items.destroy', $item) }}" method="POST" onsubmit="return confirm('Are you sure you want to delete this equipment?')">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit"
                                        class="rounded-lg border border-red-200 bg-red-50 px-6 py-3 text-sm font-medium text-red-600 shadow-theme-xs hover:bg-red-100 hover:text-red-700 dark:border-red-800 dark:bg-red-900/20 dark:text-red-400 dark:hover:bg-red-900/30">
                                        <i class="bi bi-trash me-2"></i>Delete
                                    </button>
                                </form>
                                
                                <div class="flex gap-3">
                                    <a href="{{ route('items.index') }}" 
                                        class="rounded-lg border border-gray-300 bg-white px-6 py-3 text-sm font-medium text-gray-700 shadow-theme-xs hover:bg-gray-50 dark:border-gray-700 dark:bg-gray-800 dark:text-gray-400 dark:hover:bg-white/[0.03]">
                                        <i class="bi bi-x-lg me-2"></i>Cancel
                                    </a>
                                    <button type="submit"
                                        class="rounded-lg bg-blue-500 px-6 py-3 text-sm font-medium text-white shadow-theme-xs hover:bg-blue-600 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:ring-offset-2 dark:focus:ring-offset-gray-900">
                                        <i class="bi bi-save me-2"></i>Update Equipment
                                    </button>
                                </div>
                            </div>
                        </div>
                    </div>
                </form>
            </div>
        </div>

        <!-- Right Column - Equipment Details -->
        <div class="space-y-6">
            <!-- Equipment Details -->
            <div class="rounded-2xl border border-gray-200 bg-white p-6 dark:border-gray-800 dark:bg-white/[0.03]">
                <h3 class="mb-4 text-sm font-semibold text-gray-800 dark:text-white/90">
                    <i class="bi bi-info-circle me-2"></i>Equipment Details
                </h3>
                
                <div class="space-y-4">
                    <div>
                        <div class="text-xs text-gray-500 dark:text-gray-400">Equipment ID</div>
                        <div class="mt-1 font-medium text-gray-800 dark:text-white/90">#{{ $item->id }}</div>
                    </div>
                    
                    <div>
                        <div class="text-xs text-gray-500 dark:text-gray-400">Created On</div>
                        <div class="mt-1 font-medium text-gray-800 dark:text-white/90">
                            {{ $item->created_at->format('M d, Y') }}
                        </div>
                        <div class="text-xs text-gray-500 dark:text-gray-400">
                            {{ $item->created_at->diffForHumans() }}
                        </div>
                    </div>
                    
                    <div>
                        <div class="text-xs text-gray-500 dark:text-gray-400">Last Updated</div>
                        <div class="mt-1 font-medium text-gray-800 dark:text-white/90">
                            {{ $item->updated_at->format('M d, Y') }}
                        </div>
                        <div class="text-xs text-gray-500 dark:text-gray-400">
                            {{ $item->updated_at->diffForHumans() }}
                        </div>
                    </div>
                    
                    <div>
                        <div class="text-xs text-gray-500 dark:text-gray-400">Current Type</div>
                        <div class="mt-1">
                            <span class="inline-flex items-center rounded-full bg-blue-100 px-3 py-1 text-xs font-medium text-blue-800 dark:bg-blue-900 dark:text-blue-200">
                                <i class="bi bi-{{ $item->type === 'computer' ? 'pc-display' : ($item->type === 'printer' ? 'printer' : ($item->type === 'aircon' ? 'snow' : 'box')) }} me-1"></i>
                                {{ $item->getTypeText() }}
                            </span>
                        </div>
                    </div>
                    
                    <div>
                        <div class="text-xs text-gray-500 dark:text-gray-400">Unit of Measure</div>
                        <div class="mt-1 font-medium text-gray-800 dark:text-white/90">
                            {{ $item->getUnitText() }}
                        </div>
                    </div>
                </div>
            </div>

            <!-- Quick Actions -->
            <div class="rounded-2xl border border-gray-200 bg-white p-6 dark:border-gray-800 dark:bg-white/[0.03]">
                <h3 class="mb-4 text-sm font-semibold text-gray-800 dark:text-white/90">
                    <i class="bi bi-lightning me-2"></i>Quick Actions
                </h3>
                
                <div class="space-y-3">
                    <a href="{{ route('items.show', $item) }}" 
                        class="flex items-center rounded-lg border border-gray-200 bg-white px-4 py-3 text-sm font-medium text-gray-700 shadow-theme-xs hover:bg-gray-50 dark:border-gray-700 dark:bg-gray-800 dark:text-gray-400 dark:hover:bg-white/[0.03]">
                        <i class="bi bi-eye me-3"></i>
                        View Details
                    </a>
                    
                    <a href="{{ route('items.create') }}" 
                        class="flex items-center rounded-lg border border-gray-200 bg-white px-4 py-3 text-sm font-medium text-gray-700 shadow-theme-xs hover:bg-gray-50 dark:border-gray-700 dark:bg-gray-800 dark:text-gray-400 dark:hover:bg-white/[0.03]">
                        <i class="bi bi-plus-lg me-3"></i>
                        Add Another Equipment
                    </a>
                    
                    <a href="{{ route('items.index') }}" 
                        class="flex items-center rounded-lg border border-gray-200 bg-white px-4 py-3 text-sm font-medium text-gray-700 shadow-theme-xs hover:bg-gray-50 dark:border-gray-700 dark:bg-gray-800 dark:text-gray-400 dark:hover:bg-white/[0.03]">
                        <i class="bi bi-list-ul me-3"></i>
                        View All Equipment
                    </a>
                </div>
            </div>
        </div>
    </div>

    <!-- Success Message -->
    @if(session('success'))
        <div class="fixed bottom-4 right-4 z-50">
            <x-ui.alert variant="success" title="Success" :message="session('success')" />
        </div>
    @endif
@endsection