<div x-data="{ showApproveModal: false }">
    <!-- Modal triggered from quick actions -->
    
    <div x-show="showApproveModal" x-cloak style="display: none;"
        class="fixed inset-0 z-50 flex items-center justify-center bg-black bg-opacity-50">
        <div class="bg-white dark:bg-gray-800 rounded-lg p-6 w-full max-w-md">
            <div class="flex justify-between items-center mb-4">
                <h3 class="text-lg font-semibold text-gray-800 dark:text-white/90">
                    Approve Maintenance Request
                </h3>
                <button @click="showApproveModal = false"
                    class="text-gray-500 hover:text-gray-700 dark:text-gray-400 dark:hover:text-gray-200">
                    <i class="bi bi-x-lg"></i>
                </button>
            </div>

            <form action="{{ route('approvals.approve', $maintenanceRequest) }}"
                method="POST" enctype="multipart/form-data" id="approve-request-form">
                @csrf
                <div class="space-y-4">
                    <!-- Approval Notes -->
                    <div>
                        <label class="mb-2 block text-sm font-medium text-gray-700 dark:text-gray-300">
                            Approval Notes (Optional)
                        </label>
                        <textarea name="approval_notes" rows="3"
                            class="w-full rounded-lg border border-gray-300 bg-transparent px-4 py-2.5 text-sm text-gray-800 shadow-theme-xs placeholder:text-gray-400 focus:border-blue-300 focus:outline-none focus:ring-2 focus:ring-blue-500/10 dark:border-gray-700 dark:bg-gray-900 dark:text-white/90 dark:placeholder:text-white/30 dark:focus:border-blue-800"
                            placeholder="Add any notes or comments about this approval..."></textarea>
                    </div>

                    <!-- File Attachments -->
                    <div>
                        <label class="mb-2 block text-sm font-medium text-gray-700 dark:text-gray-300">
                            Attach Files (Optional)
                        </label>
                        <div class="mt-1">
                            <input type="file" name="attachments[]" multiple
                                accept=".pdf,.doc,.docx,.xls,.xlsx,.jpg,.jpeg,.png,.gif"
                                class="block w-full text-sm text-gray-500 file:mr-4 file:py-2 file:px-4 file:rounded-lg file:border-0 file:text-sm file:font-medium file:bg-blue-50 file:text-blue-700 hover:file:bg-blue-100 dark:file:bg-blue-900/30 dark:file:text-blue-300">
                            <p class="mt-1 text-xs text-gray-500 dark:text-gray-400">
                                You can upload multiple files (PDF, Word, Excel, Images). Max 5MB each.
                            </p>
                        </div>

                        <!-- Preview container -->
                        <div id="file-preview" class="mt-2 space-y-2 hidden">
                            <div class="text-xs font-medium text-gray-700 dark:text-gray-300">
                                Selected files:</div>
                            <div id="file-list" class="space-y-1"></div>
                        </div>
                    </div>

                    <div class="flex justify-end gap-3">
                        <button type="button" @click="showApproveModal = false"
                            class="rounded-lg border border-gray-300 bg-white px-4 py-2.5 text-sm font-medium text-gray-700 shadow-theme-xs hover:bg-gray-50 dark:border-gray-700 dark:bg-gray-800 dark:text-gray-400 dark:hover:bg-white/[0.03]">
                            Cancel
                        </button>
                        <button type="submit"
                            class="rounded-lg bg-green-500 px-4 py-2.5 text-sm font-medium text-white shadow-theme-xs hover:bg-green-600 focus:outline-none focus:ring-2 focus:ring-green-500 focus:ring-offset-2 dark:focus:ring-offset-gray-900">
                            Approve Request
                        </button>
                    </div>
                </div>
            </form>
        </div>
    </div>
</div>