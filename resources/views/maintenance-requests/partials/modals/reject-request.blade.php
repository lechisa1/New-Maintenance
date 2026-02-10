<div x-data="{ showRejectModal: false }">
    <!-- Modal triggered from quick actions -->

    <div x-show="showRejectModal" x-cloak style="display: none;"
        class="fixed inset-0 z-50 flex items-center justify-center bg-black bg-opacity-50">
        <div class="bg-white dark:bg-gray-800 rounded-lg p-6 w-full max-w-md">
            <div class="flex justify-between items-center mb-4">
                <h3 class="text-lg font-semibold text-gray-800 dark:text-white/90">
                    Reject Maintenance Request
                </h3>
                <button @click="showRejectModal = false"
                    class="text-gray-500 hover:text-gray-700 dark:text-gray-400 dark:hover:text-gray-200">
                    <i class="bi bi-x-lg"></i>
                </button>
            </div>

            <form action="{{ route('approvals.reject', $maintenanceRequest) }}" method="POST" id="reject-request-form">
                @csrf
                <div class="space-y-4">
                    <div>
                        <label class="mb-2 block text-sm font-medium text-gray-700 dark:text-gray-300">
                            Reason for Rejection <span class="text-red-500">*</span>
                        </label>
                        <textarea name="rejection_reason" rows="4" required
                            class="w-full rounded-lg border border-gray-300 bg-transparent px-4 py-2.5 text-sm text-gray-800 shadow-theme-xs placeholder:text-gray-400 focus:border-blue-300 focus:outline-none focus:ring-2 focus:ring-blue-500/10 dark:border-gray-700 dark:bg-gray-900 dark:text-white/90 dark:placeholder:text-white/30 dark:focus:border-blue-800"
                            placeholder="Please provide a reason for rejecting this request..."></textarea>
                    </div>

                    <div class="flex justify-end gap-3">
                        <button type="button" @click="showRejectModal = false"
                            class="rounded-lg border border-gray-300 bg-white px-4 py-2.5 text-sm font-medium text-gray-700 shadow-theme-xs hover:bg-gray-50 dark:border-gray-700 dark:bg-gray-800 dark:text-gray-400 dark:hover:bg-white/[0.03]">
                            Cancel
                        </button>
                        <button type="submit"
                            class="rounded-lg bg-red-500 px-4 py-2.5 text-sm font-medium text-white shadow-theme-xs hover:bg-red-600 focus:outline-none focus:ring-2 focus:ring-red-500 focus:ring-offset-2 dark:focus:ring-offset-gray-900">
                            Submit Rejection
                        </button>
                    </div>
                </div>
            </form>
        </div>
    </div>
</div>
