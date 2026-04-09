<div>
    <h4 class="mb-4 text-sm font-semibold text-gray-800 dark:text-white/90">
        <i class="bi bi-pc-display me-2"></i>Equipment Information
    </h4>

    <div class="space-y-6">

        @forelse ($maintenanceRequest->items as $requestItem)
            @php $item = $requestItem->item; @endphp

            <div class="border rounded-lg p-4 dark:border-gray-700">

                {{-- Equipment Name --}}
                <div class="mb-3">
                    <div class="text-xs text-gray-500 dark:text-gray-400">Equipment</div>
                    <div class="mt-1 font-medium text-gray-800 dark:text-white/90">
                        {{ $item?->name ?? 'Not specified' }}
                    </div>

                </div>

                {{-- Equipment Type --}}
                <div class="mb-3">
                    <div class="text-xs text-gray-500 dark:text-gray-400">Equipment Type</div>
                    <div class="mt-1">
                        <span
                            class="inline-flex items-center rounded-lg bg-blue-50 px-3 py-1.5 text-sm font-medium text-blue-700 dark:bg-blue-900/30 dark:text-blue-300">
                            <i class="bi bi-box me-2"></i>
                            {{ $item?->type ?? 'N/A' }}
                        </span>
                    </div>
                </div>

                {{-- Equipment Status --}}
                <div>
                    <div class="text-xs text-gray-500 dark:text-gray-400">Equipment Status</div>
                    <div class="mt-1">
                        <span
                            class="rounded-full px-2 py-1 text-xs font-medium 
                            {{ $item?->status === 'active' ? 'bg-green-100 text-green-800' : 'bg-gray-100 text-gray-800' }}">
                            {{ ucfirst($item?->status ?? 'unknown') }}
                        </span>
                    </div>
                </div>

            </div>

        @empty
            <div class="text-sm text-gray-500">
                No equipment linked to this request.
            </div>
        @endforelse

    </div>
</div>
