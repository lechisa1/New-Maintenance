<div x-data="{ showUpdateStatusModal: false }">
    <!-- The modal will be opened from quick actions -->

    <div x-show="showUpdateStatusModal" x-cloak style="display: none;"
        class="fixed inset-0 z-50 flex items-center justify-center bg-black bg-opacity-50">
        <div class="bg-white dark:bg-gray-800 rounded-lg p-6 w-full max-w-md">
            <div class="flex justify-between items-center mb-4">
                <h3 class="text-lg font-semibold text-gray-800 dark:text-white/90">
                    Update Request Status
                </h3>
                <button @click="showUpdateStatusModal = false"
                    class="text-gray-500 hover:text-gray-700 dark:text-gray-400 dark:hover:text-gray-200">
                    <i class="bi bi-x-lg"></i>
                </button>
            </div>

            <form action="{{ route('maintenance-requests.update-status', $maintenanceRequest) }}" method="POST"
                class="mt-4">
                @csrf
                <div class="space-y-4">
                    <div>
                        <label class="mb-2 block text-sm font-medium text-gray-700 dark:text-gray-300">
                            New Status
                        </label>
                        <select name="status" required
                            class="h-11 w-full rounded-lg border border-gray-300 bg-transparent px-4 py-2.5 text-sm text-gray-800 shadow-theme-xs focus:border-blue-300 focus:outline-none focus:ring-2 focus:ring-blue-500/10 dark:border-gray-700 dark:bg-gray-900 dark:text-white/90 dark:focus:border-blue-800">
                            <option value="" disabled selected>Select status</option>
                            @if ($maintenanceRequest->status === 'assigned')
                                <option value="in_progress">Start Work (In Progress)</option>
                            @endif
                            <option value="completed">Mark as Completed</option>
                            <option value="not_fixed">Could Not Fix</option>
                        </select>
                    </div>
                    <div>
                        <label class="mb-2 block text-sm font-medium text-gray-700 dark:text-gray-300">
                            Resolution Notes (Required for completion)
                        </label>
                        <textarea name="resolution_notes" rows="3"
                            class="w-full rounded-lg border border-gray-300 bg-transparent px-4 py-2.5 text-sm text-gray-800 shadow-theme-xs placeholder:text-gray-400 focus:border-blue-300 focus:outline-none focus:ring-2 focus:ring-blue-500/10 dark:border-gray-700 dark:bg-gray-900 dark:text-white/90 dark:placeholder:text-white/30 dark:focus:border-blue-800"
                            placeholder="Describe what was done or why it couldn't be fixed..."></textarea>
                    </div>
                    <div class="flex justify-end gap-3">
                        <button @click="showUpdateStatusModal = false" type="button"
                            class="rounded-lg border border-gray-300 bg-white px-4 py-2.5 text-sm font-medium text-gray-700 shadow-theme-xs hover:bg-gray-50 dark:border-gray-700 dark:bg-gray-800 dark:text-gray-400 dark:hover:bg-white/[0.03]">
                            Cancel
                        </button>
                        <button type="submit"
                            class="rounded-lg bg-blue-500 px-4 py-2.5 text-sm font-medium text-white shadow-theme-xs hover:bg-blue-600 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:ring-offset-2 dark:focus:ring-offset-gray-900">
                            Update Status
                        </button>
                    </div>
                </div>
            </form>
        </div>
    </div>
</div>
