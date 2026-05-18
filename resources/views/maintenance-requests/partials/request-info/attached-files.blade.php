@if ($maintenanceRequest->files->count() > 0)
    <div class="mt-6">
        <h4 class="mb-4 text-sm font-semibold text-gray-800 dark:text-white/90">
            <i class="bi bi-paperclip me-2"></i>Attached Files
        </h4>

        <div class="grid grid-cols-1 gap-3 sm:grid-cols-2">
            @foreach ($maintenanceRequest->files as $file)
                @php
                    $extension = strtolower(pathinfo($file->original_name, PATHINFO_EXTENSION));
                    $isImage = in_array($extension, ['jpg', 'jpeg', 'png', 'gif', 'webp', 'svg', 'bmp']);
                    $isPdf = $extension === 'pdf';
                    $isVideo = in_array($extension, ['mp4', 'webm', 'ogg', 'mov']);
                    $isAudio = in_array($extension, ['mp3', 'wav', 'ogg', 'm4a']);
                    $isDocument = in_array($extension, ['doc', 'docx', 'xls', 'xlsx', 'ppt', 'pptx', 'txt']);
                    $canPreview = $isImage || $isPdf;
                @endphp

                <div
                    class="group relative flex items-center justify-between rounded-lg border border-gray-200 p-3 hover:bg-gray-50 dark:border-gray-700 dark:hover:bg-gray-800/50 transition-all duration-200">
                    <div class="flex items-center overflow-hidden">
                        {{-- File Icon/Thumbnail --}}
                        @if ($isImage)
                            <div class="relative h-10 w-10 flex-shrink-0 overflow-hidden rounded-md">
                                <img src="{{ Storage::url($file->path) }}" alt="{{ $file->original_name }}"
                                    class="h-full w-full object-cover" loading="lazy">
                            </div>
                        @elseif ($isPdf)
                            <div
                                class="flex h-10 w-10 flex-shrink-0 items-center justify-center rounded-md bg-red-100 dark:bg-red-900/30">
                                <i class="bi bi-file-earmark-pdf-fill text-xl text-red-600 dark:text-red-400"></i>
                            </div>
                        @elseif ($isVideo)
                            <div
                                class="flex h-10 w-10 flex-shrink-0 items-center justify-center rounded-md bg-purple-100 dark:bg-purple-900/30">
                                <i
                                    class="bi bi-file-earmark-play-fill text-xl text-purple-600 dark:text-purple-400"></i>
                            </div>
                        @elseif ($isAudio)
                            <div
                                class="flex h-10 w-10 flex-shrink-0 items-center justify-center rounded-md bg-green-100 dark:bg-green-900/30">
                                <i class="bi bi-file-earmark-music-fill text-xl text-green-600 dark:text-green-400"></i>
                            </div>
                        @elseif ($isDocument)
                            <div
                                class="flex h-10 w-10 flex-shrink-0 items-center justify-center rounded-md bg-blue-100 dark:bg-blue-900/30">
                                <i class="bi bi-file-earmark-text-fill text-xl text-blue-600 dark:text-blue-400"></i>
                            </div>
                        @else
                            <div
                                class="flex h-10 w-10 flex-shrink-0 items-center justify-center rounded-md bg-gray-100 dark:bg-gray-800">
                                <i class="bi bi-file-earmark-fill text-xl text-gray-500 dark:text-gray-400"></i>
                            </div>
                        @endif

                        <div class="ml-3 min-w-0 flex-1">
                            <div class="truncate text-sm font-medium text-gray-800 dark:text-white/90 max-w-[200px]"
                                title="{{ $file->original_name }}">
                                {{ Str::limit($file->original_name, 30) }}
                            </div>
                            <div class="text-xs text-gray-500 dark:text-gray-400">
                                {{ $file->getFileSize() }}
                                <span class="ml-1 text-gray-400">({{ strtoupper($extension) }})</span>
                                @if ($canPreview)
                                    <span
                                        class="ml-2 inline-flex items-center rounded-full bg-blue-100 px-2 py-0.5 text-xs font-medium text-blue-800 dark:bg-blue-900/30 dark:text-blue-300">
                                        <i class="bi bi-eye me-1"></i>Preview
                                    </span>
                                @endif
                            </div>
                        </div>
                    </div>

                    <div class="flex gap-2">
                        @if ($canPreview)
                            <button type="button"
                                onclick="previewFile('{{ route('maintenance-requests.download-file', [$maintenanceRequest, $file]) }}', '{{ addslashes($file->original_name) }}', '{{ $isImage ? 'image' : 'pdf' }}')"
                                class="rounded-lg border border-blue-200 bg-blue-50 px-2 py-1 text-xs font-medium text-blue-600 shadow-theme-xs hover:bg-blue-100 hover:text-blue-700 transition-all duration-200 dark:border-blue-800 dark:bg-blue-900/20 dark:text-blue-400 dark:hover:bg-blue-900/30"
                                title="Preview">
                                <i class="bi bi-eye"></i>
                            </button>
                        @endif

                        <a href="{{ route('maintenance-requests.download-file', [$maintenanceRequest, $file]) }}"
                            class="rounded-lg border border-gray-300 bg-white px-2 py-1 text-xs font-medium text-gray-700 shadow-theme-xs hover:bg-gray-50 transition-all duration-200 dark:border-gray-700 dark:bg-gray-800 dark:text-gray-400 dark:hover:bg-white/[0.03]"
                            title="Download">
                            <i class="bi bi-download"></i>
                        </a>

                        @if (
                            $maintenanceRequest->user_id === auth()->id() &&
                                $maintenanceRequest->status === App\Models\MaintenanceRequest::STATUS_PENDING)
                            <form
                                action="{{ route('maintenance-requests.delete-file', [$maintenanceRequest, $file]) }}"
                                method="POST" onsubmit="return confirmDeleteFile()" class="inline">
                                @csrf
                                @method('DELETE')
                                <button type="submit"
                                    class="rounded-lg border border-red-200 bg-red-50 px-2 py-1 text-xs font-medium text-red-600 shadow-theme-xs hover:bg-red-100 hover:text-red-700 transition-all duration-200 dark:border-red-800 dark:bg-red-900/20 dark:text-red-400 dark:hover:bg-red-900/30"
                                    title="Delete">
                                    <i class="bi bi-trash"></i>
                                </button>
                            </form>
                        @endif
                    </div>
                </div>
            @endforeach
        </div>
    </div>

    <!-- Preview Modal -->
    <div id="previewModal"
        class="fixed inset-0 z-50 hidden items-center justify-center bg-black/80 backdrop-blur-sm transition-all duration-300">
        <div class="relative bg-white dark:bg-gray-800 rounded-xl w-full max-w-5xl mx-4 shadow-2xl">
            <!-- Modal Header -->
            <div class="flex items-center justify-between p-4 border-b border-gray-200 dark:border-gray-700">
                <h3 id="previewTitle" class="text-lg font-semibold text-gray-800 dark:text-white/90 truncate pr-8">
                    Loading...
                </h3>
                <button onclick="closePreview()"
                    class="text-gray-500 hover:text-gray-700 dark:text-gray-400 dark:hover:text-gray-200 transition-colors">
                    <i class="bi bi-x-lg text-xl"></i>
                </button>
            </div>

            <!-- Modal Body -->
            <div class="p-4">
                <div id="previewContent"
                    class="w-full min-h-[500px] max-h-[70vh] flex items-center justify-center overflow-auto">
                    <div class="text-center text-gray-500">
                        <i class="bi bi-hourglass-split text-4xl animate-spin"></i>
                        <p class="mt-2">Loading preview...</p>
                    </div>
                </div>
            </div>

            <!-- Modal Footer -->
            <div class="flex justify-end gap-3 p-4 border-t border-gray-200 dark:border-gray-700">
                <button onclick="closePreview()"
                    class="px-4 py-2 text-sm font-medium text-gray-700 bg-gray-100 rounded-lg hover:bg-gray-200 dark:bg-gray-700 dark:text-gray-300 dark:hover:bg-gray-600 transition-colors">
                    Close
                </button>
                <a id="downloadLink" href="#" target="_blank"
                    class="px-4 py-2 text-sm font-medium text-white bg-blue-600 rounded-lg hover:bg-blue-700 transition-colors">
                    <i class="bi bi-download me-2"></i>Download
                </a>
            </div>
        </div>
    </div>
@endif

<style>
    /* Preview Modal Animations */
    #previewModal {
        transition: opacity 0.3s ease, visibility 0.3s ease;
    }

    #previewModal.flex {
        opacity: 1;
        visibility: visible;
    }

    #previewModal.hidden {
        opacity: 0;
        visibility: hidden;
    }

    /* Loading spinner animation */
    @keyframes spin {
        from {
            transform: rotate(0deg);
        }

        to {
            transform: rotate(360deg);
        }
    }

    .animate-spin {
        animation: spin 1s linear infinite;
    }
</style>

<script>
    let currentPreviewUrl = '';

    function previewFile(fileUrl, fileName, fileType) {
        const modal = document.getElementById('previewModal');
        const previewTitle = document.getElementById('previewTitle');
        const previewContent = document.getElementById('previewContent');
        const downloadLink = document.getElementById('downloadLink');

        // Set title and download link
        previewTitle.textContent = fileName;
        downloadLink.href = fileUrl;
        currentPreviewUrl = fileUrl;

        // Show loading state
        previewContent.innerHTML = `
            <div class="text-center text-gray-500">
                <i class="bi bi-hourglass-split text-4xl animate-spin"></i>
                <p class="mt-2">Loading preview...</p>
            </div>
        `;

        // Clear and prepare content based on file type
        if (fileType === 'image') {
            const img = new Image();
            img.onload = function() {
                previewContent.innerHTML = '';
                img.className = 'max-w-full max-h-full object-contain rounded-lg';
                previewContent.appendChild(img);
            };
            img.onerror = function() {
                previewContent.innerHTML = `
                    <div class="text-center text-red-500">
                        <i class="bi bi-image text-6xl"></i>
                        <p class="mt-2">Failed to load image preview</p>
                        <p class="text-sm mt-1">You can still download the file using the button below.</p>
                    </div>
                `;
            };
            img.src = fileUrl;
            img.alt = fileName;
        } else if (fileType === 'pdf') {
            // Use iframe for PDF preview
            const iframe = document.createElement('iframe');
            iframe.src = fileUrl;
            iframe.className = 'w-full h-full min-h-[500px] rounded-lg';
            iframe.style.border = 'none';
            iframe.onload = function() {
                // Remove loading indicator when iframe loads
                const loadingDiv = previewContent.querySelector('.text-center');
                if (loadingDiv && loadingDiv !== iframe) {
                    loadingDiv.remove();
                }
            };
            iframe.onerror = function() {
                previewContent.innerHTML = `
                    <div class="text-center text-red-500">
                        <i class="bi bi-file-earmark-pdf text-6xl"></i>
                        <p class="mt-2">Failed to load PDF preview</p>
                        <p class="text-sm mt-1">You can still download the file using the button below.</p>
                    </div>
                `;
            };
            previewContent.innerHTML = '';
            previewContent.appendChild(iframe);
        } else {
            // For non-previewable files
            previewContent.innerHTML = `
                <div class="text-center text-gray-500">
                    <i class="bi bi-file-earmark text-6xl"></i>
                    <p class="mt-2">Preview not available for this file type</p>
                    <p class="text-sm mt-1">Please download the file to view its contents.</p>
                </div>
            `;
        }

        // Show modal with animation
        modal.classList.remove('hidden');
        modal.classList.add('flex');

        // Prevent body scroll
        document.body.style.overflow = 'hidden';

        // Add ESC key listener
        document.addEventListener('keydown', handlePreviewEscape);
    }

    function closePreview() {
        const modal = document.getElementById('previewModal');
        modal.classList.add('hidden');
        modal.classList.remove('flex');

        // Clear content to free memory
        const previewContent = document.getElementById('previewContent');
        if (previewContent) {
            previewContent.innerHTML = '';
        }

        // Restore body scroll
        document.body.style.overflow = '';

        // Remove ESC key listener
        document.removeEventListener('keydown', handlePreviewEscape);
    }

    function handlePreviewEscape(e) {
        if (e.key === 'Escape') {
            closePreview();
        }
    }

    function confirmDeleteFile() {
        return confirm('Are you sure you want to delete this file? This action cannot be undone.');
    }

    // Close modal when clicking outside
    document.getElementById('previewModal')?.addEventListener('click', function(e) {
        if (e.target === this) {
            closePreview();
        }
    });
</script>
