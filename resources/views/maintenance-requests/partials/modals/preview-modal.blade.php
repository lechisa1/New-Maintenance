<div id="previewModal" class="fixed inset-0 z-[9999] hidden overflow-y-auto bg-black/75 p-4">
    <div class="relative mx-auto flex min-h-screen max-w-6xl items-center justify-center">
        <div class="relative w-full rounded-xl bg-white shadow-2xl dark:bg-gray-900">
            <!-- Modal Header -->
            <div class="flex items-center justify-between border-b border-gray-200 px-6 py-4 dark:border-gray-700">
                <div class="flex items-center gap-3">
                    <div id="fileTypeIcon" class="text-xl">
                        <!-- Icon will be set dynamically -->
                    </div>
                    <div>
                        <h3 id="previewTitle" class="text-lg font-semibold text-gray-800 dark:text-white/90"></h3>
                        <div id="fileInfo" class="text-sm text-gray-500 dark:text-gray-400"></div>
                    </div>
                </div>
                <div class="flex items-center gap-2">
                    <a id="downloadLink" href="#"
                        class="rounded-lg border border-gray-300 bg-white px-4 py-2 text-sm font-medium text-gray-700 shadow-theme-xs hover:bg-gray-50 dark:border-gray-700 dark:bg-gray-800 dark:text-gray-400 dark:hover:bg-white/[0.03]">
                        <i class="bi bi-download me-2"></i>Download
                    </a>
                    <button onclick="closePreview()"
                        class="rounded-lg p-2 text-gray-500 hover:bg-gray-100 hover:text-gray-700 dark:hover:bg-gray-800">
                        <i class="bi bi-x-lg text-2xl"></i>
                    </button>
                </div>
            </div>

            <!-- Modal Content -->
            <div class="max-h-[80vh] overflow-auto p-6">
                <div id="previewContent" class="flex flex-col items-center justify-center">
                    <!-- Loading spinner -->
                    <div id="previewLoading" class="hidden">
                        <div class="flex flex-col items-center justify-center p-8">
                            <div class="h-12 w-12 animate-spin rounded-full border-4 border-blue-200 border-t-blue-600">
                            </div>
                            <p class="mt-4 text-gray-600 dark:text-gray-400">Loading preview...</p>
                        </div>
                    </div>
                    <!-- Content will be loaded here -->
                </div>
            </div>
        </div>
    </div>
</div>
