@extends('layouts.app')

@section('content')
    <x-common.page-breadcrumb pageTitle="Deleted Equipment" />

    <div class="space-y-6">
        <!-- Header -->
        <div class="flex flex-col gap-4 sm:flex-row sm:items-center sm:justify-between">
            <div>
                <h2 class="text-xl font-semibold text-gray-800 dark:text-white/90">
                    <i class="bi bi-trash me-2"></i>Deleted Equipment
                </h2>
                <p class="mt-1 text-sm text-gray-500 dark:text-gray-400">
                    View and restore deleted equipment. Items will be permanently deleted after 30 days.
                </p>
            </div>
            
            <div class="flex gap-2">
                <a href="{{ route('items.index') }}" 
                    class="rounded-lg border border-gray-300 bg-white px-4 py-2 text-sm font-medium text-gray-700 shadow-theme-xs hover:bg-gray-50 dark:border-gray-700 dark:bg-gray-800 dark:text-gray-400 dark:hover:bg-white/[0.03]">
                    <i class="bi bi-arrow-left me-2"></i>Back to Equipment
                </a>
            </div>
        </div>

        <!-- Deleted Equipment Table -->
        <div class="rounded-2xl border border-gray-200 bg-white p-6 dark:border-gray-800 dark:bg-white/[0.03]">
            <h3 class="mb-6 text-lg font-semibold text-gray-800 dark:text-white/90">
                <i class="bi bi-trash3 me-2"></i>Deleted Equipment List
            </h3>

            @if($items->count() > 0)
                <!-- Deleted Equipment Table Component -->
                @php
                    $trashedTransactions = $items->map(function($item) {
                        return [
                            'id' => $item->id,
                            'name' => $item->name,
                            'email' => $item->getTypeText(),
                            'image' => 'https://ui-avatars.com/api/?name=' . urlencode($item->name) . '&background=random',
                            'date' => $item->deleted_at->format('M d, Y'),
                            'price' => $item->getUnitText(),
                            'category' => $item->getTypeText(),
                            'status' => 'Deleted',
                        ];
                    })->toArray();
                @endphp

                <x-tables.basic-tables.basic-tables-three :transactions="$trashedTransactions" />

                <!-- Bulk Actions -->
                <div class="mt-6 border-t border-gray-200 pt-6 dark:border-gray-700">
                    <div class="text-sm text-gray-500 dark:text-gray-400">
                        <p class="mb-3"><i class="bi bi-info-circle me-2"></i>Deleted equipment can be restored within 30 days.</p>
                        
                        <div class="flex gap-3">
                            <form action="{{ route('items.restore.all') }}" method="POST" onsubmit="return confirm('Are you sure you want to restore all deleted equipment?')">
                                @csrf
                                <button type="submit"
                                    class="inline-flex items-center rounded-lg border border-green-200 bg-green-50 px-4 py-2 text-sm font-medium text-green-600 shadow-theme-xs hover:bg-green-100 hover:text-green-700 dark:border-green-800 dark:bg-green-900/20 dark:text-green-400 dark:hover:bg-green-900/30">
                                    <i class="bi bi-arrow-clockwise me-2"></i> Restore All
                                </button>
                            </form>
                            
                            <form action="{{ route('items.forceDelete.all') }}" method="POST" onsubmit="return confirm('Are you sure you want to permanently delete ALL equipment? This action cannot be undone!')">
                                @csrf
                                <button type="submit"
                                    class="inline-flex items-center rounded-lg border border-red-200 bg-red-50 px-4 py-2 text-sm font-medium text-red-600 shadow-theme-xs hover:bg-red-100 hover:text-red-700 dark:border-red-800 dark:bg-red-900/20 dark:text-red-400 dark:hover:bg-red-900/30">
                                    <i class="bi bi-trash3 me-2"></i> Delete All Permanently
                                </button>
                            </form>
                        </div>
                    </div>
                </div>

                <!-- Pagination Links -->
                @if($items->hasPages())
                    <div class="mt-6">
                        {{ $items->links() }}
                    </div>
                @endif
            @else
                <div class="py-12 text-center">
                    <div class="mx-auto mb-4 flex h-16 w-16 items-center justify-center rounded-full bg-gray-100 dark:bg-gray-800">
                        <i class="bi bi-check2-circle text-2xl text-gray-400 dark:text-gray-500"></i>
                    </div>
                    <h3 class="mb-2 text-lg font-semibold text-gray-800 dark:text-white/90">No Deleted Equipment</h3>
                    <p class="text-gray-500 dark:text-gray-400">There are no deleted equipment to display.</p>
                    <a href="{{ route('items.index') }}" 
                        class="mt-4 inline-flex items-center rounded-lg bg-blue-500 px-4 py-2.5 text-sm font-medium text-white shadow-theme-xs hover:bg-blue-600 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:ring-offset-2 dark:focus:ring-offset-gray-900">
                        <i class="bi bi-arrow-left me-2"></i> Go Back to Equipment
                    </a>
                </div>
            @endif
        </div>

        <!-- Important Notice -->
        <div class="rounded-lg border border-yellow-200 bg-yellow-50 p-4 dark:border-yellow-800 dark:bg-yellow-900/20">
            <div class="flex items-start">
                <i class="bi bi-exclamation-triangle me-2 mt-0.5 text-yellow-500"></i>
                <div>
                    <h4 class="text-sm font-medium text-yellow-800 dark:text-yellow-200">Important Notice</h4>
                    <ul class="mt-2 list-inside list-disc text-sm text-yellow-700 dark:text-yellow-300">
                        <li>Deleted equipment will be permanently removed after 30 days</li>
                        <li>Restoring equipment will bring it back with its original data</li>
                        <li>Permanent deletion cannot be undone</li>
                    </ul>
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