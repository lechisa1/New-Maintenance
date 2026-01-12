@extends('layouts.app')

@section('content')
    <x-common.page-breadcrumb pageTitle="Submit Maintenance Request" />

    <div class="grid grid-cols-1 gap-6 lg:grid-cols-3">
        <!-- Maintenance Request Form -->
        <div class="lg:col-span-2">
            <div class="rounded-2xl border border-gray-200 bg-white p-6 dark:border-gray-800 dark:bg-white/[0.03]">
                <h3 class="mb-6 text-lg font-semibold text-gray-800 dark:text-white/90">
                    <i class="bi bi-tools me-2"></i>Submit Maintenance Request
                </h3>
                <hr class="mb-6 border-gray-200 dark:border-gray-700">

                <form method="POST" action="{{ route('maintenance-requests.store') }}" enctype="multipart/form-data">
                    @csrf

                    <div class="space-y-6">
                        <!-- Equipment Information -->
                        <div>
                            <h4 class="mb-4 text-sm font-semibold text-gray-800 dark:text-white/90">
                                <i class="bi bi-box me-1"></i>Equipment Information
                            </h4>
                            <div class="grid grid-cols-1 gap-5 md:grid-cols-2">
                                <!-- Select Equipment -->
                                <div>
                                    <label class="mb-2 block text-sm font-medium text-gray-700 dark:text-gray-300">
                                        Select Equipment <span class="text-red-500">*</span>
                                    </label>
                                    <select name="item_id" required
                                        class="h-11 w-full rounded-lg border border-gray-300 bg-transparent px-4 py-2.5 text-sm text-gray-800 shadow-theme-xs focus:border-blue-300 focus:outline-none focus:ring-2 focus:ring-blue-500/10 dark:border-gray-700 dark:bg-gray-900 dark:text-white/90 dark:focus:border-blue-800 @error('item_id') border-red-500 dark:border-red-500 @enderror">
                                        <option value="" disabled selected>Select equipment</option>
                                        @foreach($items as $item)
                                            <option value="{{ $item->id }}" {{ old('item_id') == $item->id ? 'selected' : '' }}>
                                                {{ $item->name }} ({{ $item->getTypeText() }})
                                            </option>
                                        @endforeach
                                    </select>
                                    @error('item_id')
                                        <div class="mt-2 text-sm text-red-600 dark:text-red-400">{{ $message }}</div>
                                    @enderror
                                </div>

                                <!-- Select Issue Type -->
                                <div>
                                    <label class="mb-2 block text-sm font-medium text-gray-700 dark:text-gray-300">
                                        Issue Type <span class="text-red-500">*</span>
                                    </label>
                                    <select name="issue_type_id" required
                                        class="h-11 w-full rounded-lg border border-gray-300 bg-transparent px-4 py-2.5 text-sm text-gray-800 shadow-theme-xs focus:border-blue-300 focus:outline-none focus:ring-2 focus:ring-blue-500/10 dark:border-gray-700 dark:bg-gray-900 dark:text-white/90 dark:focus:border-blue-800 @error('issue_type') border-red-500 dark:border-red-500 @enderror">
                                        <option value="" disabled selected>Select issue type</option>
                                        @foreach($issueTypes as $issueType)
                                            <option value="{{ $issueType->id }}" {{ old('issue_type_id') == $issueType->id ? 'selected' : '' }}>
                                                {{ $issueType->name }}
                                            </option>
                                        @endforeach
                                    </select>
                                    @error('issue_type')
                                        <div class="mt-2 text-sm text-red-600 dark:text-red-400">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                        </div>

                        <!-- Priority Selection -->
                        <div>
                            <h4 class="mb-4 text-sm font-semibold text-gray-800 dark:text-white/90">
                                <i class="bi bi-flag me-1"></i>Priority Level
                            </h4>
                            <div class="grid grid-cols-2 gap-3 sm:grid-cols-4">
                                @foreach(App\Models\MaintenanceRequest::getPriorityOptions() as $key => $value)
                                    <label class="cursor-pointer">
                                        <input type="radio" name="priority" value="{{ $key }}" 
                                            {{ old('priority', 'medium') == $key ? 'checked' : '' }} required
                                            class="peer sr-only">
                                        @php
                                            $colors = [
                                                'low' => ['border' => 'border-green-500', 'bg' => 'bg-green-100', 'text' => 'text-green-800', 'dark_bg' => 'dark:bg-green-900', 'dark_text' => 'dark:text-green-200'],
                                                'medium' => ['border' => 'border-yellow-500', 'bg' => 'bg-yellow-100', 'text' => 'text-yellow-800', 'dark_bg' => 'dark:bg-yellow-900', 'dark_text' => 'dark:text-yellow-200'],
                                                'high' => ['border' => 'border-orange-500', 'bg' => 'bg-orange-100', 'text' => 'text-orange-800', 'dark_bg' => 'dark:bg-orange-900', 'dark_text' => 'dark:text-orange-200'],
                                                'emergency' => ['border' => 'border-red-500', 'bg' => 'bg-red-100', 'text' => 'text-red-800', 'dark_bg' => 'dark:bg-red-900', 'dark_text' => 'dark:text-red-200'],
                                            ];
                                            $color = $colors[$key] ?? $colors['medium'];
                                        @endphp
                                        <div class="rounded-lg border-2 p-4 text-center transition-all peer-checked:{{ $color['border'] }} {{ $color['bg'] }} {{ $color['text'] }} dark:border-gray-700 peer-checked:dark:{{ $color['border'] }} {{ $color['dark_bg'] }} {{ $color['dark_text'] }}">
                                            <div class="text-sm font-medium">{{ $value }}</div>
                                            <div class="mt-1 text-xs text-gray-600 dark:text-gray-400">
                                                @if($key === 'low')
                                                    Non-urgent, can wait
                                                @elseif($key === 'medium')
                                                    Normal priority
                                                @elseif($key === 'high')
                                                    Needs attention soon
                                                @else
                                                    Immediate attention
                                                @endif
                                            </div>
                                        </div>
                                    </label>
                                @endforeach
                            </div>
                            @error('priority')
                                <div class="mt-2 text-sm text-red-600 dark:text-red-400">{{ $message }}</div>
                            @enderror
                        </div>

                        <!-- Description -->
                        <div>
                            <h4 class="mb-4 text-sm font-semibold text-gray-800 dark:text-white/90">
                                <i class="bi bi-chat-dots me-1"></i>Problem Description
                            </h4>
                            <div>
                                <label class="mb-2 block text-sm font-medium text-gray-700 dark:text-gray-300">
                                    Describe the problem <span class="text-red-500">*</span>
                                </label>
                                <textarea name="description" rows="4" required
                                    class="w-full rounded-lg border border-gray-300 bg-transparent px-4 py-3 text-sm text-gray-800 shadow-theme-xs placeholder:text-gray-400 focus:border-blue-300 focus:outline-none focus:ring-2 focus:ring-blue-500/10 dark:border-gray-700 dark:bg-gray-900 dark:text-white/90 dark:placeholder:text-white/30 dark:focus:border-blue-800 @error('description') border-red-500 dark:border-red-500 @enderror"
                                    placeholder="Please provide detailed description of the problem, including any error messages, when it started, and what you've tried...">{{ old('description') }}</textarea>
                                @error('description')
                                    <div class="mt-2 text-sm text-red-600 dark:text-red-400">{{ $message }}</div>
                                @enderror
                                <div class="mt-1 text-xs text-gray-500 dark:text-gray-400">
                                    Be as detailed as possible. Minimum 10 characters.
                                </div>
                            </div>
                        </div>

                        <!-- File Upload -->
                        <div>
                            <h4 class="mb-4 text-sm font-semibold text-gray-800 dark:text-white/90">
                                <i class="bi bi-paperclip me-1"></i>Attach Files (Optional)
                            </h4>
                            <div class="rounded-lg border-2 border-dashed border-gray-300 p-6 dark:border-gray-700">
                                <div class="text-center">
                                    <i class="bi bi-cloud-arrow-up text-3xl text-gray-400"></i>
                                    <div class="mt-2">
                                        <label for="file-upload" class="cursor-pointer font-medium text-blue-600 hover:text-blue-500">
                                            Click to upload files
                                        </label>
                                        <input id="file-upload" type="file" multiple 
                                               name="files[]"
                                               class="sr-only"
                                               accept=".jpg,.jpeg,.png,.pdf,.doc,.docx,.txt,.xls,.xlsx">
                                    </div>
                                    <p class="mt-1 text-xs text-gray-500 dark:text-gray-400">
                                        Maximum file size: 10MB. Allowed: JPG, PNG, PDF, DOC, DOCX, TXT, XLS, XLSX
                                    </p>
                                </div>
                                
                                <!-- File List -->
                                <div id="file-list" class="mt-4 space-y-2 hidden">
                                    <h5 class="text-sm font-medium text-gray-700 dark:text-gray-300">
                                        Attached Files (<span id="file-count">0</span>)
                                    </h5>
                                </div>
                            </div>
                            @error('files.*')
                                <div class="mt-2 text-sm text-red-600 dark:text-red-400">{{ $message }}</div>
                            @enderror
                        </div>

                        <!-- Submit Button -->
                        <div class="border-t border-gray-200 pt-6 dark:border-gray-700">
                            <div class="flex justify-end gap-3">
                                <button type="reset"
                                    class="rounded-lg border border-gray-300 bg-white px-6 py-3 text-sm font-semibold text-gray-700 shadow-theme-xs hover:bg-gray-50 dark:border-gray-700 dark:bg-gray-800 dark:text-gray-400 dark:hover:bg-white/[0.03]">
                                    <i class="bi bi-x-lg me-2"></i>Reset Form
                                </button>
                                <button type="submit"
                                    class="rounded-lg bg-blue-500 px-6 py-3 text-sm font-semibold text-white shadow-theme-xs hover:bg-blue-600 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:ring-offset-2 dark:focus:ring-offset-gray-900">
                                    <i class="bi bi-send me-2"></i>Submit Request
                                </button>
                            </div>
                        </div>
                    </div>
                </form>
            </div>
        </div>

        <!-- Sidebar - Recent Requests & Info -->
        <div>
            <!-- My Recent Requests -->
            <div class="rounded-2xl border border-gray-200 bg-white p-6 dark:border-gray-800 dark:bg-white/[0.03]">
                <h3 class="mb-6 text-lg font-semibold text-gray-800 dark:text-white/90">
                    <i class="bi bi-clock-history me-2"></i>My Recent Requests
                </h3>
                
                <div class="space-y-4">
                    @forelse($userRecentRequests as $request)
                        <div class="rounded-lg border border-gray-200 p-4 dark:border-gray-700">
                            <div class="flex items-start justify-between">
                                <div>
                                    <div class="font-medium text-gray-800 dark:text-white/90">{{ $request->ticket_number }}</div>
                                    <div class="text-sm text-gray-600 dark:text-gray-400">
                                        {{ $request->item?->name ?? 'N/A' }}
                                    </div>
                                </div>
                                <div class="text-right">
                                    <div class="mb-1">
                                        <span class="rounded-full px-2 py-0.5 text-xs font-medium {{ $request->getPriorityBadgeClass() }}">
                                            {{ strtoupper($request->priority) }}
                                        </span>
                                    </div>
                                    <div class="text-xs text-gray-500 dark:text-gray-400">
                                        {{ $request->getRequestedDate() }}
                                    </div>
                                </div>
                            </div>
                            <div class="mt-3 flex items-center justify-between">
                                <span class="rounded-full px-2 py-0.5 text-xs font-medium {{ $request->getStatusBadgeClass() }}">
                                    {{ $request->getStatusText() }}
                                </span>
                                <a href="{{ route('maintenance-requests.show', $request) }}" 
                                   class="text-xs text-blue-600 hover:text-blue-800 dark:text-blue-400 dark:hover:text-blue-300">
                                    View Details
                                </a>
                            </div>
                        </div>
                    @empty
                        <div class="rounded-lg border border-dashed border-gray-300 p-6 text-center dark:border-gray-700">
                            <i class="bi bi-inbox text-2xl text-gray-400"></i>
                            <p class="mt-2 text-sm text-gray-500 dark:text-gray-400">
                                No recent requests
                            </p>
                        </div>
                    @endforelse
                </div>
                
                <!-- Stats -->
                <div class="mt-6 grid grid-cols-2 gap-3">
                    <div class="rounded-lg border border-gray-200 bg-gray-50 p-4 dark:border-gray-700 dark:bg-gray-800">
                        <div class="text-sm text-gray-500 dark:text-gray-400">Active Equipment</div>
                        <div class="mt-1 text-2xl font-semibold text-gray-800 dark:text-white/90">
                            {{ App\Models\Item::active()->count() }}
                        </div>
                    </div>
                    <div class="rounded-lg border border-gray-200 bg-gray-50 p-4 dark:border-gray-700 dark:bg-gray-800">
                        <div class="text-sm text-gray-500 dark:text-gray-400">Avg. Response</div>
                        <div class="mt-1 text-2xl font-semibold text-gray-800 dark:text-white/90">2.5h</div>
                    </div>
                </div>
                
                <!-- Quick Tips -->
                <div class="mt-6 rounded-lg border border-blue-100 bg-blue-50 p-4 dark:border-blue-800 dark:bg-blue-900/20">
                    <h4 class="text-sm font-medium text-blue-800 dark:text-blue-200">
                        <i class="bi bi-lightbulb me-1"></i>Quick Tips
                    </h4>
                    <ul class="mt-2 space-y-1 text-xs text-blue-700 dark:text-blue-300">
                        <li>• Be specific about the problem</li>
                        <li>• Include error messages if any</li>
                        <li>• Attach screenshots if possible</li>
                        <li>• Select appropriate priority level</li>
                    </ul>
                </div>
            </div>
        </div>
    </div>

    <!-- Demo Notice -->
    <div class="mt-6 rounded-lg border border-blue-200 bg-blue-50 p-4 dark:border-blue-800 dark:bg-blue-900/20">
        <div class="flex items-start">
            <i class="bi bi-info-circle-fill me-2 mt-0.5 text-blue-500"></i>
            <div>
                <h4 class="text-sm font-medium text-blue-800 dark:text-blue-200">Maintenance Request Form</h4>
                <p class="mt-1 text-sm text-blue-700 dark:text-blue-300">
                    Use this form to request maintenance for registered equipment. 
                    All requests will receive a unique ticket number for tracking. 
                    Our team will respond based on the priority level selected.
                </p>
            </div>
        </div>
    </div>

    @push('scripts')
    <script>
    document.addEventListener('DOMContentLoaded', function() {
        const fileInput = document.getElementById('file-upload');
        const fileList = document.getElementById('file-list');
        const fileCount = document.getElementById('file-count');
        
        fileInput.addEventListener('change', function(event) {
            const files = Array.from(event.target.files);
            
            // Clear previous list
            fileList.innerHTML = '<h5 class="text-sm font-medium text-gray-700 dark:text-gray-300">Attached Files (<span id="file-count">0</span>)</h5>';
            fileList.classList.remove('hidden');
            
            // Update file count
            fileCount.textContent = files.length;
            
            // Create file list items
            files.forEach((file, index) => {
                const fileSizeMB = file.size / (1024 * 1024);
                const fileElement = document.createElement('div');
                fileElement.className = 'flex items-center justify-between rounded-lg border border-gray-200 p-3 dark:border-gray-700';
                fileElement.innerHTML = `
                    <div class="flex items-center">
                        <i class="${getFileIcon(file.type)} text-lg text-gray-500"></i>
                        <div class="ml-3">
                            <div class="text-sm font-medium text-gray-800 dark:text-white/90">${file.name}</div>
                            <div class="text-xs text-gray-500 dark:text-gray-400">${formatFileSize(fileSizeMB)} • ${file.type || 'Unknown type'}</div>
                        </div>
                    </div>
                    <button type="button" class="text-red-500 hover:text-red-700 remove-file" data-index="${index}">
                        <i class="bi bi-x-lg"></i>
                    </button>
                `;
                fileList.appendChild(fileElement);
            });
            
            // Add remove functionality
            document.querySelectorAll('.remove-file').forEach(button => {
                button.addEventListener('click', function() {
                    const index = this.getAttribute('data-index');
                    const dt = new DataTransfer();
                    const filesArray = Array.from(fileInput.files);
                    
                    filesArray.splice(index, 1);
                    filesArray.forEach(file => dt.items.add(file));
                    fileInput.files = dt.files;
                    
                    // Trigger change event to update list
                    fileInput.dispatchEvent(new Event('change'));
                });
            });
        });
        
        function formatFileSize(size) {
            if (size < 1) return (size * 1024).toFixed(0) + ' KB';
            return size.toFixed(2) + ' MB';
        }
        
        function getFileIcon(fileType) {
            if (fileType.includes('image')) return 'bi bi-file-image';
            if (fileType.includes('pdf')) return 'bi bi-file-pdf';
            if (fileType.includes('word') || fileType.includes('document')) return 'bi bi-file-word';
            if (fileType.includes('text')) return 'bi bi-file-text';
            if (fileType.includes('excel') || fileType.includes('spreadsheet')) return 'bi bi-file-excel';
            return 'bi bi-file';
        }
    });
    </script>
    @endpush
@endsection