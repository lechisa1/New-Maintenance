@extends('layouts.app')
@php
    $breadcrumbs = [
        ['label' => 'Home', 'url' => url('/')],
        ['label' => 'Equipment Management', 'url' => route('items.index')],
        ['label' => $item->name], // Active page: equipment name
    ];
@endphp

@section('content')
    <x-common.page-breadcrumb :breadcrumbs="$breadcrumbs" />


    <div class="grid grid-cols-1 gap-6 lg:grid-cols-3">
        <!-- Left Column - Equipment Details -->
        <div class="lg:col-span-2">
            <div class="rounded-2xl border border-gray-200 bg-white p-6 dark:border-gray-800 dark:bg-white/[0.03]">
                <div class="flex flex-col items-start justify-between gap-4 sm:flex-row sm:items-center">
                    <div>
                        <h3 class="text-xl font-semibold text-gray-800 dark:text-white/90">{{ $item->name }}</h3>
                        <div class="mt-2 flex flex-wrap items-center gap-2">
                            <span class="rounded-full px-3 py-1 text-xs font-medium {{ $item->getStatusBadgeClass() }}">
                                <i class="bi bi-circle-fill me-1" style="font-size: 6px;"></i>
                                {{ $item->getStatusText() }}
                            </span>
                            <span
                                class="rounded-full bg-blue-100 px-3 py-1 text-xs font-medium text-blue-800 dark:bg-blue-900 dark:text-blue-200">
                                <i
                                    class="bi bi-{{ $item->type === 'computer' ? 'pc-display' : ($item->type === 'printer' ? 'printer' : ($item->type === 'aircon' ? 'snow' : 'box')) }} me-1"></i>
                                {{ $item->getTypeText() }}
                            </span>
                            <span
                                class="rounded-full bg-gray-100 px-3 py-1 text-xs font-medium text-gray-800 dark:bg-gray-900 dark:text-gray-200">
                                <i class="bi bi-rulers me-1"></i>
                                {{ $item->getUnitText() }}
                            </span>
                        </div>
                    </div>

                    <div class="flex gap-2">
                        <a href="{{ route('items.edit', $item) }}"
                            class="rounded-lg border border-gray-300 bg-white px-4 py-2 text-sm font-medium text-gray-700 shadow-theme-xs hover:bg-gray-50 dark:border-gray-700 dark:bg-gray-800 dark:text-gray-400 dark:hover:bg-white/[0.03]">
                            <i class="bi bi-pencil me-2"></i> Edit
                        </a>
                        <form action="{{ route('items.destroy', $item) }}" method="POST"
                            onsubmit="return confirm('Are you sure you want to delete this equipment?')">
                            @csrf
                            @method('DELETE')
                            <button type="submit"
                                class="rounded-lg border border-red-200 bg-red-50 px-4 py-2 text-sm font-medium text-red-600 shadow-theme-xs hover:bg-red-100 hover:text-red-700 dark:border-red-800 dark:bg-red-900/20 dark:text-red-400 dark:hover:bg-red-900/30">
                                <i class="bi bi-trash me-2"></i> Delete
                            </button>
                        </form>
                    </div>
                </div>

                <hr class="my-6 border-gray-200 dark:border-gray-700">

                <!-- Equipment Information Grid -->
                <div class="grid grid-cols-1 gap-6 md:grid-cols-2">
                    <!-- Basic Information -->
                    <div>
                        <h4 class="mb-4 text-sm font-semibold text-gray-800 dark:text-white/90">
                            <i class="bi bi-card-checklist me-2"></i>Basic Information
                        </h4>

                        <div class="space-y-4">
                            <div>
                                <div class="text-xs text-gray-500 dark:text-gray-400">Equipment Name</div>
                                <div class="mt-1 font-medium text-gray-800 dark:text-white/90">{{ $item->name }}</div>
                            </div>

                            <div>
                                <div class="text-xs text-gray-500 dark:text-gray-400">Equipment Type</div>
                                <div class="mt-1">
                                    <span
                                        class="inline-flex items-center rounded-lg bg-blue-50 px-3 py-1.5 text-sm font-medium text-blue-700 dark:bg-blue-900/30 dark:text-blue-300">
                                        <i
                                            class="bi bi-{{ $item->type === 'computer' ? 'pc-display' : ($item->type === 'printer' ? 'printer' : ($item->type === 'aircon' ? 'snow' : 'box')) }} me-2"></i>
                                        {{ $item->getTypeText() }}
                                    </span>
                                </div>
                            </div>

                            <div>
                                <div class="text-xs text-gray-500 dark:text-gray-400">Unit of Measure</div>
                                <div class="mt-1 font-medium text-gray-800 dark:text-white/90">{{ $item->getUnitText() }}
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Status & Timeline -->
                    <div>
                        <h4 class="mb-4 text-sm font-semibold text-gray-800 dark:text-white/90">
                            <i class="bi bi-clock-history me-2"></i>Timeline
                        </h4>

                        <div class="space-y-4">
                            <div>
                                <div class="text-xs text-gray-500 dark:text-gray-400">Current Status</div>
                                <div class="mt-1">
                                    <span
                                        class="inline-flex items-center rounded-full px-3 py-1 text-sm font-medium {{ $item->getStatusBadgeClass() }}">
                                        <i class="bi bi-circle-fill me-1" style="font-size: 6px;"></i>
                                        {{ $item->getStatusText() }}
                                    </span>
                                </div>
                                <p class="mt-2 text-xs text-gray-500 dark:text-gray-400">
                                    @if ($item->status === 'active')
                                        This equipment is available for maintenance requests
                                    @elseif($item->status === 'inactive')
                                        This equipment is not currently in use
                                    @else
                                        This equipment is currently under maintenance
                                    @endif
                                </p>
                            </div>

                            <div class="space-y-3">
                                <div>
                                    <div class="text-xs text-gray-500 dark:text-gray-400">Registered On</div>
                                    <div class="mt-1 font-medium text-gray-800 dark:text-white/90">
                                        {{ $item->created_at->format('F d, Y') }}
                                    </div>
                                    <div class="text-xs text-gray-500 dark:text-gray-400">
                                        {{ $item->created_at->diffForHumans() }}
                                    </div>
                                </div>

                                <div>
                                    <div class="text-xs text-gray-500 dark:text-gray-400">Last Updated</div>
                                    <div class="mt-1 font-medium text-gray-800 dark:text-white/90">
                                        {{ $item->updated_at->format('F d, Y') }}
                                    </div>
                                    <div class="text-xs text-gray-500 dark:text-gray-400">
                                        {{ $item->updated_at->diffForHumans() }}
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>


            </div>
        </div>

        <!-- Right Column - Actions & Statistics -->
        <div class="space-y-6">
            <!-- Quick Actions -->
            <div class="rounded-2xl border border-gray-200 bg-white p-6 dark:border-gray-800 dark:bg-white/[0.03]">
                <h3 class="mb-4 text-sm font-semibold text-gray-800 dark:text-white/90">
                    <i class="bi bi-lightning me-2"></i>Quick Actions
                </h3>

                <div class="space-y-3">
                    <a href="{{ route('items.edit', $item) }}"
                        class="flex items-center rounded-lg border border-gray-200 bg-white px-4 py-3 text-sm font-medium text-gray-700 shadow-theme-xs hover:bg-gray-50 dark:border-gray-700 dark:bg-gray-800 dark:text-gray-400 dark:hover:bg-white/[0.03]">
                        <i class="bi bi-pencil-square me-3"></i>
                        Edit Equipment
                    </a>
                    {{-- 
                    @if ($item->status === 'active')
                        <a href="#"
                            class="flex items-center rounded-lg border border-gray-200 bg-white px-4 py-3 text-sm font-medium text-gray-700 shadow-theme-xs hover:bg-gray-50 dark:border-gray-700 dark:bg-gray-800 dark:text-gray-400 dark:hover:bg-white/[0.03]">
                            <i class="bi bi-wrench me-3"></i>
                            Request Maintenance
                        </a>
                    @endif --}}

                    <a href="{{ route('items.create') }}"
                        class="flex items-center rounded-lg border border-gray-200 bg-white px-4 py-3 text-sm font-medium text-gray-700 shadow-theme-xs hover:bg-gray-50 dark:border-gray-700 dark:bg-gray-800 dark:text-gray-400 dark:hover:bg-white/[0.03]">
                        <i class="bi bi-plus-lg me-3"></i>
                        Add Similar Equipment
                    </a>

                    <a href="{{ route('items.index') }}"
                        class="flex items-center rounded-lg border border-gray-200 bg-white px-4 py-3 text-sm font-medium text-gray-700 shadow-theme-xs hover:bg-gray-50 dark:border-gray-700 dark:bg-gray-800 dark:text-gray-400 dark:hover:bg-white/[0.03]">
                        <i class="bi bi-arrow-left me-3"></i>
                        Back to Equipment List
                    </a>
                </div>
            </div>

            <!-- Similar Equipment -->
            <div class="rounded-2xl border border-gray-200 bg-white p-6 dark:border-gray-800 dark:bg-white/[0.03]">
                <h3 class="mb-4 text-sm font-semibold text-gray-800 dark:text-white/90">
                    <i class="bi bi-grid me-2"></i>Similar Equipment
                </h3>

                <div class="space-y-3">
                    @php
                        $similarItems = App\Models\Item::where('type', $item->type)
                            ->where('id', '!=', $item->id)
                            ->latest()
                            ->take(3)
                            ->get();
                    @endphp

                    @forelse($similarItems as $similarItem)
                        <a href="{{ route('items.show', $similarItem) }}"
                            class="block rounded-lg border border-gray-200 p-3 hover:border-blue-300 hover:bg-blue-50 dark:border-gray-700 dark:hover:border-blue-800 dark:hover:bg-blue-900/20">
                            <div class="flex items-center justify-between">
                                <div>
                                    <div class="font-medium text-gray-800 dark:text-white/90">{{ $similarItem->name }}
                                    </div>
                                    <div class="text-sm text-gray-600 dark:text-gray-400">{{ $similarItem->getUnitText() }}
                                    </div>
                                </div>
                                <span
                                    class="rounded-full px-2 py-0.5 text-xs font-medium {{ $similarItem->getStatusBadgeClass() }}">
                                    {{ $similarItem->getStatusText() }}
                                </span>
                            </div>
                        </a>
                    @empty
                        <div class="rounded-lg border border-dashed border-gray-300 p-4 text-center dark:border-gray-700">
                            <i class="bi bi-pc-display text-2xl text-gray-400"></i>
                            <p class="mt-2 text-sm text-gray-500 dark:text-gray-400">No similar equipment found</p>
                        </div>
                    @endforelse
                </div>
            </div>

            <!-- Export Options -->
            <div class="rounded-2xl border border-gray-200 bg-white p-6 dark:border-gray-800 dark:bg-white/[0.03]">
                <h3 class="mb-4 text-sm font-semibold text-gray-800 dark:text-white/90">
                    <i class="bi bi-download me-2"></i>Export Options
                </h3>

                <div class="space-y-3">
                    <button onclick="printEquipmentDetails()"
                        class="flex w-full items-center rounded-lg border border-gray-200 bg-white px-4 py-3 text-sm font-medium text-gray-700 shadow-theme-xs hover:bg-gray-50 dark:border-gray-700 dark:bg-gray-800 dark:text-gray-400 dark:hover:bg-white/[0.03]">
                        <i class="bi bi-printer me-3"></i>
                        Print Details
                    </button>

                    <button onclick="downloadEquipmentPDF()"
                        class="flex w-full items-center rounded-lg border border-gray-200 bg-white px-4 py-3 text-sm font-medium text-gray-700 shadow-theme-xs hover:bg-gray-50 dark:border-gray-700 dark:bg-gray-800 dark:text-gray-400 dark:hover:bg-white/[0.03]">
                        <i class="bi bi-file-pdf me-3"></i>
                        Download as PDF
                    </button>
                </div>
            </div>
        </div>
    </div>

    <!-- Equipment Purpose Notice -->
    <div class="mt-6 rounded-lg border border-green-200 bg-green-50 p-4 dark:border-green-800 dark:bg-green-900/20">
        <div class="flex items-start">
            <i class="bi bi-shield-check me-2 mt-0.5 text-green-500"></i>
            <div>
                <h4 class="text-sm font-medium text-green-800 dark:text-green-200">Equipment Registration Purpose</h4>
                <p class="mt-1 text-sm text-green-700 dark:text-green-300">
                    This equipment is registered for maintenance requests.
                    Employees can request maintenance only for registered equipment.
                    Keep equipment information updated for accurate maintenance tracking.
                </p>
            </div>
        </div>
    </div>

    <script>
        function printEquipmentDetails() {
            const printContent = `
                <html>
                    <head>
                        <title>Equipment Details - {{ $item->name }}</title>
                        <style>
                            body { font-family: Arial, sans-serif; padding: 20px; }
                            h1 { color: #333; }
                            .info { margin: 10px 0; }
                            .label { font-weight: bold; color: #666; }
                            .value { margin-left: 10px; }
                        </style>
                    </head>
                    <body>
                        <h1>Equipment Details</h1>
                        <div class="info">
                            <span class="label">Name:</span>
                            <span class="value">{{ $item->name }}</span>
                        </div>
                        <div class="info">
                            <span class="label">Type:</span>
                            <span class="value">{{ $item->getTypeText() }}</span>
                        </div>
                        <div class="info">
                            <span class="label">Unit:</span>
                            <span class="value">{{ $item->getUnitText() }}</span>
                        </div>
                        <div class="info">
                            <span class="label">Status:</span>
                            <span class="value">{{ $item->getStatusText() }}</span>
                        </div>
                        <div class="info">
                            <span class="label">Equipment ID:</span>
                            <span class="value">#{{ str_pad($item->id, 6, '0', STR_PAD_LEFT) }}</span>
                        </div>
                        <div class="info">
                            <span class="label">Registered On:</span>
                            <span class="value">{{ $item->created_at->format('F d, Y') }}</span>
                        </div>
                        <hr>
                        <p>Printed on: ${new Date().toLocaleDateString()}</p>
                    </body>
                </html>
            `;

            const printWindow = window.open('', '_blank');
            printWindow.document.write(printContent);
            printWindow.document.close();
            printWindow.print();
        }

        function downloadEquipmentPDF() {
            alert('PDF download functionality would be implemented here. This is a placeholder.');
            // In a real application, you would generate and download a PDF
        }
    </script>
@endsection
