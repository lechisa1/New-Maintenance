@extends('layouts.app')
@php
    $breadcrumbs = [
        ['label' => 'Home', 'url' => url('/')],
        ['label' => 'Equipment Management'], // Active page
    ];
@endphp

@section('content')
    <x-common.page-breadcrumb :breadcrumbs="$breadcrumbs" />

    @include('maintenance-requests.partials.alerts')
    <div class="space-y-6">
        {{-- Statistics Cards --}}


        {{-- Professional Filter Card --}}
        <div class="rounded-2xl border border-gray-200 bg-white p-5 dark:border-gray-800 dark:bg-white/[0.03] mt-2">
            <form action="{{ route('items.index') }}" method="GET" id="filterForm" class="space-y-4">

                {{-- Top Row: Search and Actions --}}
                <div class="flex flex-wrap items-center justify-between gap-4">
                    <div class="relative w-full max-w-md">
                        <input type="text" name="search" id="searchInput"
                            class="h-11 w-full rounded-lg border border-gray-300 bg-transparent px-4 pl-11 text-sm text-gray-800 focus:border-blue-500 focus:ring-2 focus:ring-blue-500/10 dark:border-gray-700 dark:bg-gray-900 dark:text-white"
                            placeholder="Search equipment, serial number..." value="{{ request('search') }}">
                        <i class="bi bi-search absolute left-4 top-1/2 -translate-y-1/2 text-gray-400"></i>
                    </div>

                    <div class="flex items-center gap-3">

                        <a href="{{ route('items.create') }}"
                            class="inline-flex items-center rounded-lg bg-blue-600 px-4 py-2.5 text-sm font-medium text-white hover:bg-blue-700 transition shadow-sm">
                            <i class="bi bi-plus-lg me-2"></i> Add Equipment
                        </a>
                    </div>
                </div>

                {{-- Bottom Row: Inline Filters --}}
                <div class="flex flex-wrap items-center gap-6 border-t border-gray-100 pt-4 dark:border-gray-800">
                    <div class="flex items-center gap-2">
                        <label class="text-xs font-semibold uppercase tracking-wider text-gray-500">Type:</label>
                        <select name="type"
                            class="filter-select h-9 rounded-md border-gray-300 bg-gray-50 py-1 text-xs dark:border-gray-700 dark:bg-gray-900 dark:text-gray-300">
                            <option value="">All Categories</option>
                            @foreach (App\Models\Item::getTypeOptions() as $key => $value)
                                <option value="{{ $key }}" {{ request('type') == $key ? 'selected' : '' }}>
                                    {{ $value }}</option>
                            @endforeach
                        </select>
                    </div>

                    <div class="flex items-center gap-2">
                        <label class="text-xs font-semibold uppercase tracking-wider text-gray-500">Status:</label>
                        <select name="status"
                            class="filter-select h-9 rounded-md border-gray-300 bg-gray-50 py-1 text-xs dark:border-gray-700 dark:bg-gray-900 dark:text-gray-300">
                            <option value="">All Statuses</option>
                            @foreach (App\Models\Item::getStatusOptions() as $key => $value)
                                <option value="{{ $key }}" {{ request('status') == $key ? 'selected' : '' }}>
                                    {{ $value }}</option>
                            @endforeach
                        </select>
                    </div>

                    @if (request()->anyFilled(['search', 'type', 'status']))
                        <a href="{{ route('items.index') }}"
                            class="text-xs font-medium text-red-500 hover:text-red-600 transition-colors">
                            <i class="bi bi-arrow-counterclockwise me-1"></i> Reset Filters
                        </a>
                    @endif
                </div>
            </form>
        </div>

        {{-- Table Container --}}
        <div class="rounded-2xl border border-gray-200 bg-white dark:border-gray-800 dark:bg-white/[0.03]">
            <div class="overflow-x-auto">
                <table class="min-w-full text-left">
                    <thead class="bg-gray-50/50 dark:bg-gray-800/50">
                        <tr class="border-b border-gray-200 dark:border-gray-700">
                            <th class="px-6 py-4 text-xs font-bold uppercase text-gray-500">#</th>
                            <th class="px-6 py-4 text-xs font-bold uppercase text-gray-500">Equipment</th>
                            <th class="px-6 py-4 text-xs font-bold uppercase text-gray-500">Type</th>
                            <th class="px-6 py-4 text-xs font-bold uppercase text-gray-500">Unit</th>
                            <th class="px-6 py-4 text-xs font-bold uppercase text-gray-500">Status</th>
                            <th class="px-6 py-4 text-xs font-bold uppercase text-gray-500 text-right">Actions</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-100 dark:divide-gray-800">
                        @forelse($items as $index => $item)
                            <tr class="hover:bg-gray-50/50 dark:hover:bg-white/[0.02]">
                                <td class="px-6 py-4 text-sm text-gray-500">
                                    {{ $items->firstItem() + $index }}
                                </td>
                                <td class="px-6 py-4">
                                    <div class="flex items-center gap-3">
                                        <img src="https://ui-avatars.com/api/?name={{ urlencode($item->name) }}&background=random"
                                            class="h-9 w-9 rounded-full shadow-sm" alt="avatar">
                                        <span
                                            class="font-medium text-gray-800 dark:text-gray-200">{{ $item->name }}</span>
                                    </div>
                                </td>
                                <td class="px-6 py-4 text-sm text-gray-600 dark:text-gray-400">{{ $item->getTypeText() }}
                                </td>
                                <td class="px-6 py-4 text-sm text-gray-600 dark:text-gray-400">{{ $item->getUnitText() }}
                                </td>
                                <td class="px-6 py-4">
                                    <span
                                        class="inline-flex rounded-full px-2.5 py-0.5 text-xs font-medium {{ $item->getStatusBadgeClass() }}">
                                        {{ $item->getStatusText() }}
                                    </span>
                                </td>
                                <td class="px-6 py-4 text-right">
                                    <div class="flex justify-end gap-2">
                                        <a href="{{ route('items.show', $item) }}"
                                            class="p-2 text-blue-500 hover:bg-blue-50 rounded-lg transition dark:hover:bg-blue-500/10">
                                            <i class="bi bi-eye"></i>
                                        </a>
                                        <a href="{{ route('items.edit', $item) }}"
                                            class="p-2 text-amber-500 hover:bg-amber-50 rounded-lg transition dark:hover:bg-amber-500/10">
                                            <i class="bi bi-pencil"></i>
                                        </a>
                                        <button type="button"
                                            onclick="confirmDelete('{{ route('items.destroy', $item) }}', '{{ $item->name }}')"
                                            class="p-2 text-red-500 hover:bg-red-50 rounded-lg transition dark:hover:bg-red-500/10">
                                            <i class="bi bi-trash"></i>
                                        </button>
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="6" class="px-6 py-12 text-center text-gray-500 dark:text-gray-400">
                                    No equipment matching your criteria was found.
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            @if ($items->hasPages())
                <div class="border-t border-gray-100 p-6 dark:border-gray-800">
                    {{ $items->withQueryString()->links('vendor.pagination.dashboard') }}
                </div>
            @endif
        </div>
    </div>

    {{-- Delete Modal (Standardized) --}}
    <div id="deleteModal" class="fixed inset-0 z-50 hidden items-center justify-center bg-black/50">
        <div class="w-full max-w-md rounded-2xl bg-white p-6 shadow-xl dark:bg-gray-900">
            <div class="flex items-center gap-3 mb-4">
                <div
                    class="flex h-10 w-10 items-center justify-center rounded-full bg-red-100 text-red-600 dark:bg-red-500/10 dark:text-red-400">
                    <i class="bi bi-exclamation-triangle text-lg"></i>
                </div>
                <h3 class="text-lg font-semibold text-gray-800 dark:text-white">Delete Asset</h3>
            </div>
            <p class="text-sm text-gray-600 dark:text-gray-400 mb-6">
                Are you sure you want to delete <span id="deleteUserName"
                    class="font-semibold text-gray-800 dark:text-white"></span>? This action can be reversed via the
                Trashed section.
            </p>
            <div class="flex justify-end gap-3">
                <button type="button" onclick="closeDeleteModal()"
                    class="rounded-lg border border-gray-300 px-4 py-2 text-sm font-medium text-gray-700 hover:bg-gray-50 dark:border-gray-700 dark:text-gray-300 dark:hover:bg-gray-800">Cancel</button>
                <form id="deleteForm" method="POST">
                    @csrf @method('DELETE')
                    <button type="submit"
                        class="rounded-lg bg-red-600 px-4 py-2 text-sm font-medium text-white hover:bg-red-700">Delete</button>
                </form>
            </div>
        </div>
    </div>
@endsection

@push('scripts')
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const filterForm = document.getElementById('filterForm');
            const searchInput = document.getElementById('searchInput');
            const selects = document.querySelectorAll('.filter-select');

            // Dropdowns auto-submit
            selects.forEach(select => {
                select.addEventListener('change', () => filterForm.submit());
            });

            // Search auto-submit (Debounced 600ms)
            let typingTimer;
            searchInput.addEventListener('input', () => {
                clearTimeout(typingTimer);
                typingTimer = setTimeout(() => {
                    filterForm.submit();
                }, 600);
            });
        });

        function confirmDelete(actionUrl, itemName) {
            const modal = document.getElementById('deleteModal');
            const form = document.getElementById('deleteForm');
            const nameSpan = document.getElementById('deleteUserName');
            form.action = actionUrl;
            nameSpan.textContent = itemName;
            modal.classList.remove('hidden');
            modal.classList.add('flex');
        }

        function closeDeleteModal() {
            const modal = document.getElementById('deleteModal');
            modal.classList.add('hidden');
            modal.classList.remove('flex');
        }
        // Auto-hide success alert after 5 seconds
        const successAlert = document.getElementById('alert-success');
        if (successAlert) {
            setTimeout(() => {
                successAlert.style.transition = 'opacity 0.5s ease';
                successAlert.style.opacity = '0';
                setTimeout(() => successAlert.remove(), 500);
            }, 5000);
        }
    </script>
@endpush
