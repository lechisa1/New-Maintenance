@if ($maintenanceRequest->files->count() > 0)
    <div class="mt-6">
        <h4 class="mb-4 text-sm font-semibold text-gray-800 dark:text-white/90">
            <i class="bi bi-paperclip me-2"></i>Attached Files
        </h4>

        <div class="grid grid-cols-1 gap-3 sm:grid-cols-2">
            @foreach ($maintenanceRequest->files as $file)
                @php
                    $isImage = in_array(strtolower(pathinfo($file->original_name, PATHINFO_EXTENSION)), [
                        'jpg',
                        'jpeg',
                        'png',
                        'gif',
                        'webp',
                        'svg',
                    ]);
                    $isPdf = strtolower(pathinfo($file->original_name, PATHINFO_EXTENSION)) === 'pdf';
                    $canPreview = $isImage || $isPdf;
                @endphp

                <div
                    class="group relative flex items-center justify-between rounded-lg border border-gray-200 p-3 hover:bg-gray-50 dark:border-gray-700 dark:hover:bg-gray-800/50">
                    <div class="flex items-center overflow-hidden">
                        @if ($isImage)
                            <div class="relative h-10 w-10 flex-shrink-0 overflow-hidden rounded-md">
                                <img src="{{ Storage::url($file->path) }}" alt="{{ $file->original_name }}"
                                    class="h-full w-full object-cover" loading="lazy">
                            </div>
                        @elseif ($isPdf)
                            <div
                                class="flex h-10 w-10 flex-shrink-0 items-center justify-center rounded-md bg-red-100 dark:bg-red-900/30">
                                <i class="bi bi-file-earmark-pdf-fill text-lg text-red-600 dark:text-red-400"></i>
                            </div>
                        @else
                            <i class="bi {{ $file->getFileIcon() }} text-lg text-gray-500"></i>
                        @endif

                        <div class="ml-3 min-w-0 flex-1">
                            <div class="truncate text-sm font-medium text-gray-800 dark:text-white/90"
                                title="{{ $file->original_name }}">
                                {{ $file->original_name }}
                            </div>
                            <div class="text-xs text-gray-500 dark:text-gray-400">
                                {{ $file->getFileSize() }}
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
                                onclick="openPreview('{{ route('maintenance-requests.download-file', [$maintenanceRequest, $file]) }}', '{{ addslashes($file->original_name) }}', '{{ $isImage ? 'image' : 'pdf' }}')"
                                class="rounded-lg border border-blue-200 bg-blue-50 px-2 py-1 text-xs font-medium text-blue-600 shadow-theme-xs hover:bg-blue-100 hover:text-blue-700 dark:border-blue-800 dark:bg-blue-900/20 dark:text-blue-400 dark:hover:bg-blue-900/30"
                                title="Preview">
                                <i class="bi bi-eye"></i>
                            </button>
                        @endif

                        <a href="{{ route('maintenance-requests.download-file', [$maintenanceRequest, $file]) }}"
                            class="rounded-lg border border-gray-300 bg-white px-2 py-1 text-xs font-medium text-gray-700 shadow-theme-xs hover:bg-gray-50 dark:border-gray-700 dark:bg-gray-800 dark:text-gray-400 dark:hover:bg-white/[0.03]"
                            title="Download">
                            <i class="bi bi-download"></i>
                        </a>

                        @if (
                            $maintenanceRequest->user_id === auth()->id() &&
                                $maintenanceRequest->status === App\Models\MaintenanceRequest::STATUS_PENDING)
                            <form
                                action="{{ route('maintenance-requests.delete-file', [$maintenanceRequest, $file]) }}"
                                method="POST" onsubmit="return confirm('Are you sure you want to delete this file?')"
                                class="inline">
                                @csrf
                                @method('DELETE')
                                <button type="submit"
                                    class="rounded-lg border border-red-200 bg-red-50 px-2 py-1 text-xs font-medium text-red-600 shadow-theme-xs hover:bg-red-100 hover:text-red-700 dark:border-red-800 dark:bg-red-900/20 dark:text-red-400 dark:hover:bg-red-900/30"
                                    title="Delete">
                                    <i class="bi bi-trash"></i>
                                </button>
                            </form>
                        @endif
                    </div>
                </div>
            @endforeach
        </div>
        <div id="previewModal"
            class="hidden fixed inset-0 z-50 flex items-center justify-center bg-black bg-opacity-50">
            <div class="bg-white dark:bg-gray-800 rounded-lg w-full max-w-3xl p-4 relative">
                <button onclick="closePreview()" class="absolute top-2 right-2 text-gray-600 dark:text-gray-300">
                    <i class="bi bi-x-lg text-2xl"></i>
                </button>

                <h3 id="previewTitle" class="text-lg font-semibold mb-2 text-gray-800 dark:text-white/90"></h3>

                <div id="previewContent" class="w-full h-[70vh] flex items-center justify-center overflow-auto">
                    <!-- Image or PDF will be injected here -->
                </div>
            </div>
        </div>

    </div>
@endif
