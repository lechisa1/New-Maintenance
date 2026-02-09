<div x-data="{ showDeleteModal: false }" class="flex flex-col items-start justify-between gap-4 sm:flex-row sm:items-center">

    <!-- LEFT: Ticket Info -->
    <div>
        <h3 class="text-xl font-semibold text-gray-800 dark:text-white/90">
            Ticket #{{ $maintenanceRequest->ticket_number }}
        </h3>

        <div class="mt-2 flex flex-wrap items-center gap-2">
            <span class="rounded-full px-3 py-1 text-xs font-medium {{ $maintenanceRequest->getPriorityBadgeClass() }}">
                {{ strtoupper($maintenanceRequest->priority) }} PRIORITY
            </span>

            <span class="rounded-full px-3 py-1 text-xs font-medium {{ $maintenanceRequest->getStatusBadgeClass() }}">
                {{ $maintenanceRequest->getStatusText() }}
            </span>

            <span
                class="rounded-full bg-gray-100 px-3 py-1 text-xs font-medium text-gray-800 dark:bg-gray-900 dark:text-gray-200">
                <i class="bi bi-calendar me-1"></i>
                {{ $maintenanceRequest->getRequestedDate() }}
            </span>
        </div>
    </div>

    <!-- RIGHT: ACTION BUTTONS -->
    <div class="flex gap-2">
        @if ($maintenanceRequest->user_id === auth()->id())
            @if ($maintenanceRequest->status === App\Models\MaintenanceRequest::STATUS_PENDING)
                <!-- Delete Button -->
                <button @click="showDeleteModal = true"
                    class="rounded-lg border border-red-200 bg-red-50 px-4 py-2 text-sm font-medium text-red-600 shadow-theme-xs hover:bg-red-100 hover:text-red-700 dark:border-red-800 dark:bg-red-900/20 dark:text-red-400 dark:hover:bg-red-900/30">
                    <i class="bi bi-trash me-2"></i> Delete
                </button>

                <!-- Edit Button -->
                <a href="{{ route('maintenance-requests.edit', $maintenanceRequest) }}"
                    class="rounded-lg border border-gray-300 bg-white px-4 py-2 text-sm font-medium text-gray-700 shadow-theme-xs hover:bg-gray-50 dark:border-gray-700 dark:bg-gray-800 dark:text-gray-400 dark:hover:bg-white/[0.03]">
                    <i class="bi bi-pencil me-2"></i> Edit
                </a>
            @endif
        @endif
    </div>

    <!-- DELETE CONFIRMATION MODAL -->
    <div x-show="showDeleteModal" x-transition x-cloak
        class="fixed inset-0 z-50 flex items-center justify-center bg-black/50">

        <div @click.away="showDeleteModal = false"
            class="w-full max-w-md rounded-xl bg-white p-6 shadow-lg dark:bg-gray-800">

            <h2 class="text-lg font-semibold text-gray-800 dark:text-white">
                Confirm Delete
            </h2>

            <p class="mt-2 text-sm text-gray-600 dark:text-gray-300">
                Are you sure you want to delete this maintenance request?
                This action cannot be undone.
            </p>

            <div class="mt-6 flex justify-end gap-3">
                <!-- Cancel -->
                <button @click="showDeleteModal = false"
                    class="rounded-lg border border-gray-300 px-4 py-2 text-sm text-gray-700 hover:bg-gray-100 dark:border-gray-600 dark:text-gray-300 dark:hover:bg-gray-700">
                    Cancel
                </button>

                <!-- Confirm Delete -->
                <form action="{{ route('maintenance-requests.destroy', $maintenanceRequest) }}" method="POST">
                    @csrf
                    @method('DELETE')

                    <button type="submit"
                        class="rounded-lg bg-red-600 px-4 py-2 text-sm font-medium text-white hover:bg-red-700">
                        Yes, Delete
                    </button>
                </form>
            </div>
        </div>
    </div>
</div>
