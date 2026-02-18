@extends('layouts.app')
@php
    $breadcrumbs = [
        ['label' => 'Home', 'url' => url('/')],
        ['label' => 'Maintenance Requests', 'url' => route('maintenance-requests.index')],
        ['label' => 'Edit Requests'], // current page, no URL
    ];
@endphp

@section('content')
    <x-common.page-breadcrumb :breadcrumbs="$breadcrumbs" />
    @include('maintenance-requests.partials.alerts')
    <div class="grid grid-cols-1 gap-6 lg:grid-cols-3">
        <!-- Edit Form -->
        <div class="lg:col-span-2">
            <div class="rounded-2xl border border-gray-200 bg-white p-6 dark:border-gray-800 dark:bg-white/[0.03]">
                <div class="mb-6 flex items-center justify-between">
                    <div>
                        <h3 class="text-lg font-semibold text-gray-800 dark:text-white/90">
                            <i class="bi bi-pencil-square me-2"></i>Edit Maintenance Request
                        </h3>
                        <p class="mt-1 text-sm text-gray-500 dark:text-gray-400">
                            Ticket: {{ $maintenanceRequest->ticket_number }}
                        </p>
                    </div>
                    <div class="flex items-center gap-2">
                        <span
                            class="rounded-full px-3 py-1 text-xs font-medium {{ $maintenanceRequest->getPriorityBadgeClass() }}">
                            {{ strtoupper($maintenanceRequest->priority) }}
                        </span>
                        <span
                            class="rounded-full px-3 py-1 text-xs font-medium {{ $maintenanceRequest->getStatusBadgeClass() }}">
                            {{ $maintenanceRequest->getStatusText() }}
                        </span>
                    </div>

                    <a href="{{ route('maintenance-requests.index') }}"
                        class="rounded-lg border border-gray-300 bg-white px-4 py-2 text-sm font-medium text-gray-700 shadow-theme-xs hover:bg-gray-50 dark:border-gray-700 dark:bg-gray-800 dark:text-gray-400 dark:hover:bg-white/[0.03]">
                        <i class="bi bi-arrow-left me-2"></i> Back to List

                    </a>

                </div>
                <hr class="mb-6 border-gray-200 dark:border-gray-700">

                <form method="POST" action="{{ route('maintenance-requests.update', $maintenanceRequest) }}"
                    enctype="multipart/form-data">
                    @csrf
                    @method('PUT')

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
                                        Select Equipment
                                    </label>
                                    <select name="item_id"
                                        class="h-11 w-full rounded-lg border border-gray-300 bg-transparent px-4 py-2.5 text-sm text-gray-800 shadow-theme-xs focus:border-blue-300 focus:outline-none focus:ring-2 focus:ring-blue-500/10 dark:border-gray-700 dark:bg-gray-900 dark:text-white/90 dark:focus:border-blue-800 @error('item_id') border-red-500 dark:border-red-500 @enderror">
                                        <option value="" disabled>Select equipment</option>
                                        @foreach ($items as $item)
                                            <option value="{{ $item->id }}"
                                                {{ old('item_id', $maintenanceRequest->item_id) == $item->id ? 'selected' : '' }}>
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
                                        Issue Type
                                    </label>
                                    <select name="issue_type_id"
                                        class="h-11 w-full rounded-lg border border-gray-300 bg-transparent px-4 py-2.5 text-sm text-gray-800 shadow-theme-xs focus:border-blue-300 focus:outline-none focus:ring-2 focus:ring-blue-500/10 dark:border-gray-700 dark:bg-gray-900 dark:text-white/90 dark:focus:border-blue-800 @error('issue_type') border-red-500 dark:border-red-500 @enderror">
                                        <option value="" disabled>Select issue type</option>
                                        @foreach ($issueTypes as $issueType)
                                            <option value="{{ $issueType->id }}"
                                                {{ old('issue_type_id', $maintenanceRequest->issue_type_id) == $issueType->id ? 'selected' : '' }}>
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
                                @foreach (App\Models\MaintenanceRequest::getPriorityOptions() as $key => $value)
                                    <label class="cursor-pointer">
                                        <input type="radio" name="priority" value="{{ $key }}"
                                            {{ old('priority', $maintenanceRequest->priority) == $key ? 'checked' : '' }}
                                            class="peer sr-only">
                                        @php
                                            $colors = [
                                                'low' => [
                                                    'border' => 'border-green-500',
                                                    'bg' => 'bg-green-100',
                                                    'text' => 'text-green-800',
                                                    'dark_bg' => 'dark:bg-green-900',
                                                    'dark_text' => 'dark:text-green-200',
                                                ],
                                                'medium' => [
                                                    'border' => 'border-yellow-500',
                                                    'bg' => 'bg-yellow-100',
                                                    'text' => 'text-yellow-800',
                                                    'dark_bg' => 'dark:bg-yellow-900',
                                                    'dark_text' => 'dark:text-yellow-200',
                                                ],
                                                'high' => [
                                                    'border' => 'border-orange-500',
                                                    'bg' => 'bg-orange-100',
                                                    'text' => 'text-orange-800',
                                                    'dark_bg' => 'dark:bg-orange-900',
                                                    'dark_text' => 'dark:text-orange-200',
                                                ],
                                                'emergency' => [
                                                    'border' => 'border-red-500',
                                                    'bg' => 'bg-red-100',
                                                    'text' => 'text-red-800',
                                                    'dark_bg' => 'dark:bg-red-900',
                                                    'dark_text' => 'dark:text-red-200',
                                                ],
                                            ];
                                            $color = $colors[$key] ?? $colors['medium'];
                                        @endphp
                                        <div
                                            class="rounded-lg border-2 p-4 text-center transition-all peer-checked:{{ $color['border'] }} {{ $color['bg'] }} {{ $color['text'] }} dark:border-gray-700 peer-checked:dark:{{ $color['border'] }} {{ $color['dark_bg'] }} {{ $color['dark_text'] }}">
                                            <div class="text-sm font-medium">{{ $value }}</div>
                                            <div class="mt-1 text-xs text-gray-600 dark:text-gray-400">
                                                @if ($key === 'low')
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
                            <textarea name="description" rows="4"
                                class="w-full rounded-lg border border-gray-300 bg-transparent px-4 py-3 text-sm text-gray-800 shadow-theme-xs placeholder:text-gray-400 focus:border-blue-300 focus:outline-none focus:ring-2 focus:ring-blue-500/10 dark:border-gray-700 dark:bg-gray-900 dark:text-white/90 dark:placeholder:text-white/30 dark:focus:border-blue-800 @error('description') border-red-500 dark:border-red-500 @enderror"
                                placeholder="Describe the problem...">{{ old('description', $maintenanceRequest->description) }}</textarea>
                            @error('description')
                                <div class="mt-2 text-sm text-red-600 dark:text-red-400">{{ $message }}</div>
                            @enderror
                        </div>

                        <!-- File Upload -->
                        <div>
                            <h4 class="mb-4 text-sm font-semibold text-gray-800 dark:text-white/90">
                                <i class="bi bi-paperclip me-1"></i>Add More Files
                            </h4>
                            <div class="rounded-lg border-2 border-dashed border-gray-300 p-6 dark:border-gray-700">
                                <div class="text-center">
                                    <i class="bi bi-cloud-arrow-up text-3xl text-gray-400"></i>
                                    <div class="mt-2">
                                        <label for="file-upload"
                                            class="cursor-pointer font-medium text-blue-600 hover:text-blue-500">
                                            Click to upload additional files
                                        </label>
                                        <input id="file-upload" type="file" multiple name="files[]" class="sr-only"
                                            accept=".jpg,.jpeg,.png,.pdf,.doc,.docx,.txt,.xls,.xlsx">
                                    </div>
                                    <p class="mt-1 text-xs text-gray-500 dark:text-gray-400">
                                        Maximum file size: 10MB. Allowed: JPG, PNG, PDF, DOC, DOCX, TXT, XLS, XLSX
                                    </p>
                                </div>

                                <!-- Existing Files -->
                                @if ($maintenanceRequest->files->count() > 0)
                                    <div class="mt-4">
                                        <h5 class="mb-2 text-sm font-medium text-gray-700 dark:text-gray-300">
                                            Existing Files
                                        </h5>
                                        <div class="space-y-2">
                                            @foreach ($maintenanceRequest->files as $file)
                                                <div
                                                    class="flex items-center justify-between rounded-lg border border-gray-200 p-3 dark:border-gray-700">
                                                    <div class="flex items-center">
                                                        <i class="bi {{ $file->getFileIcon() }} text-lg text-gray-500"></i>
                                                        <div class="ml-3">
                                                            <div
                                                                class="text-sm font-medium text-gray-800 dark:text-white/90">
                                                                {{ $file->original_name }}
                                                            </div>
                                                            <div class="text-xs text-gray-500 dark:text-gray-400">
                                                                {{ $file->getFileSize() }}
                                                            </div>
                                                        </div>
                                                    </div>
                                                    <a href="{{ route('maintenance-requests.download-file', [$maintenanceRequest, $file]) }}"
                                                        class="rounded-lg border border-gray-300 bg-white px-2 py-1 text-xs font-medium text-gray-700 shadow-theme-xs hover:bg-gray-50 dark:border-gray-700 dark:bg-gray-800 dark:text-gray-400 dark:hover:bg-white/[0.03]">
                                                        <i class="bi bi-download"></i>
                                                    </a>
                                                </div>
                                            @endforeach
                                        </div>
                                    </div>
                                @endif

                                <!-- New File List -->
                                <div id="file-list" class="mt-4 space-y-2 hidden">
                                    <h5 class="text-sm font-medium text-gray-700 dark:text-gray-300">
                                        New Files (<span id="file-count">0</span>)
                                    </h5>
                                </div>
                            </div>
                        </div>

                        <!-- Submit Buttons -->
                        <div class="border-t border-gray-200 pt-6 dark:border-gray-700">
                            <div class="flex justify-between">
                                @if (auth()->user()->hasAnyRole(['admin', 'technician']))
                                    <form action="{{ route('maintenance-requests.destroy', $maintenanceRequest) }}"
                                        method="POST"
                                        onsubmit="return confirm('Are you sure you want to delete this request?')">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit"
                                            class="rounded-lg border border-red-200 bg-red-50 px-6 py-3 text-sm font-medium text-red-600 shadow-theme-xs hover:bg-red-100 hover:text-red-700 dark:border-red-800 dark:bg-red-900/20 dark:text-red-400 dark:hover:bg-red-900/30">
                                            <i class="bi bi-trash me-2"></i> Delete Request
                                        </button>
                                    </form>
                                @endif

                                <div class="flex gap-3">
                                    <a href="{{ route('maintenance-requests.show', $maintenanceRequest) }}"
                                        class="rounded-lg border border-gray-300 bg-white px-6 py-3 text-sm font-medium text-gray-700 shadow-theme-xs hover:bg-gray-50 dark:border-gray-700 dark:bg-gray-800 dark:text-gray-400 dark:hover:bg-white/[0.03]">
                                        <i class="bi bi-x-lg me-2"></i> Cancel
                                    </a>
                                    <button type="submit"
                                        class="rounded-lg bg-blue-500 px-6 py-3 text-sm font-medium text-white shadow-theme-xs hover:bg-blue-600 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:ring-offset-2 dark:focus:ring-offset-gray-900">
                                        <i class="bi bi-save me-2"></i> Update Request
                                    </button>
                                </div>
                            </div>
                        </div>
                    </div>
                </form>
            </div>
        </div>

        <!-- Sidebar - Request Info -->
        <div class="space-y-6">
            <!-- Request Information -->
            <div class="rounded-2xl border border-gray-200 bg-white p-6 dark:border-gray-800 dark:bg-white/[0.03]">
                <h3 class="mb-4 text-sm font-semibold text-gray-800 dark:text-white/90">
                    <i class="bi bi-info-circle me-2"></i>Request Information
                </h3>

                <div class="space-y-4">
                    <div>
                        <div class="text-xs text-gray-500 dark:text-gray-400">Ticket Number</div>
                        <div class="mt-1 font-medium text-gray-800 dark:text-white/90">
                            {{ $maintenanceRequest->ticket_number }}
                        </div>
                    </div>

                    <div>
                        <div class="text-xs text-gray-500 dark:text-gray-400">Submitted By</div>
                        <div class="mt-1 font-medium text-gray-800 dark:text-white/90">
                            {{ $maintenanceRequest->user?->full_name ?? 'Unknown' }}
                        </div>
                        <div class="text-xs text-gray-500 dark:text-gray-400">
                            {{ $maintenanceRequest->user?->email ?? '' }}
                        </div>
                    </div>

                    <div>
                        <div class="text-xs text-gray-500 dark:text-gray-400">Submission Date</div>
                        <div class="mt-1 font-medium text-gray-800 dark:text-white/90">
                            {{ $maintenanceRequest->getRequestedDate() }}
                        </div>
                        <div class="text-xs text-gray-500 dark:text-gray-400">
                            {{ $maintenanceRequest->requested_at->diffForHumans() }}
                        </div>
                    </div>

                    @if ($maintenanceRequest->assigned_to)
                        <div>
                            <div class="text-xs text-gray-500 dark:text-gray-400">Current Technician</div>
                            <div class="mt-1 font-medium text-gray-800 dark:text-white/90">
                                {{ $maintenanceRequest->assignedTechnician?->full_name ?? 'Unknown' }}
                            </div>
                            <div class="text-xs text-gray-500 dark:text-gray-400">
                                {{ $maintenanceRequest->assigned_at?->format('M d, Y') ?? '' }}
                            </div>
                        </div>
                    @endif

                    <div>
                        <div class="text-xs text-gray-500 dark:text-gray-400">Last Updated</div>
                        <div class="mt-1 font-medium text-gray-800 dark:text-white/90">
                            {{ $maintenanceRequest->updated_at->format('M d, Y h:i A') }}
                        </div>
                        <div class="text-xs text-gray-500 dark:text-gray-400">
                            {{ $maintenanceRequest->updated_at->diffForHumans() }}
                        </div>
                    </div>
                </div>
            </div>

            <!-- Quick Actions -->
            <div class="rounded-2xl border border-gray-200 bg-white p-6 dark:border-gray-800 dark:bg-white/[0.03]">
                <h3 class="mb-4 text-sm font-semibold text-gray-800 dark:text-white/90">
                    <i class="bi bi-lightning me-2"></i>Quick Actions
                </h3>

                <div class="space-y-3">
                    <a href="{{ route('maintenance-requests.show', $maintenanceRequest) }}"
                        class="flex items-center rounded-lg border border-gray-200 bg-white px-4 py-3 text-sm font-medium text-gray-700 shadow-theme-xs hover:bg-gray-50 dark:border-gray-700 dark:bg-gray-800 dark:text-gray-400 dark:hover:bg-white/[0.03]">
                        <i class="bi bi-eye me-3"></i>
                        View Request
                    </a>

                    <a href="{{ route('maintenance-requests.create') }}"
                        class="flex items-center rounded-lg border border-gray-200 bg-white px-4 py-3 text-sm font-medium text-gray-700 shadow-theme-xs hover:bg-gray-50 dark:border-gray-700 dark:bg-gray-800 dark:text-gray-400 dark:hover:bg-white/[0.03]">
                        <i class="bi bi-plus-lg me-3"></i>
                        New Request
                    </a>

                    <button onclick="printRequest()"
                        class="flex w-full items-center rounded-lg border border-gray-200 bg-white px-4 py-3 text-sm font-medium text-gray-700 shadow-theme-xs hover:bg-gray-50 dark:border-gray-700 dark:bg-gray-800 dark:text-gray-400 dark:hover:bg-white/[0.03]">
                        <i class="bi bi-printer me-3"></i>
                        Print Request
                    </button>
                </div>
            </div>

            <!-- Response Times -->
            @if ($maintenanceRequest->assigned_at || $maintenanceRequest->completed_at)
                <div class="rounded-2xl border border-gray-200 bg-white p-6 dark:border-gray-800 dark:bg-white/[0.03]">
                    <h3 class="mb-4 text-sm font-semibold text-gray-800 dark:text-white/90">
                        <i class="bi bi-speedometer2 me-2"></i>Response Times
                    </h3>

                    <div class="space-y-3">
                        @if ($maintenanceRequest->assigned_at)
                            <div class="rounded-lg bg-gray-50 p-3 dark:bg-gray-800">
                                <div class="text-xs text-gray-500 dark:text-gray-400">Response Time</div>
                                <div class="mt-1 text-lg font-semibold text-green-600 dark:text-green-400">
                                    {{ $maintenanceRequest->getResponseTime() ?? '0' }} hours
                                </div>
                                <div class="text-xs text-gray-500 dark:text-gray-400">
                                    Assigned on {{ $maintenanceRequest->assigned_at->format('M d') }}
                                </div>
                            </div>
                        @endif

                        @if ($maintenanceRequest->completed_at)
                            <div class="rounded-lg bg-gray-50 p-3 dark:bg-gray-800">
                                <div class="text-xs text-gray-500 dark:text-gray-400">Resolution Time</div>
                                <div class="mt-1 text-lg font-semibold text-blue-600 dark:text-blue-400">
                                    {{ $maintenanceRequest->getResolutionTime() ?? '0' }} hours
                                </div>
                                <div class="text-xs text-gray-500 dark:text-gray-400">
                                    Completed on {{ $maintenanceRequest->completed_at->format('M d') }}
                                </div>
                            </div>
                        @endif
                    </div>
                </div>
            @endif
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
                    fileList.innerHTML =
                        '<h5 class="text-sm font-medium text-gray-700 dark:text-gray-300">New Files (<span id="file-count">0</span>)</h5>';
                    fileList.classList.remove('hidden');

                    // Update file count
                    fileCount.textContent = files.length;

                    // Create file list items
                    files.forEach((file, index) => {
                        const fileSizeMB = file.size / (1024 * 1024);
                        const fileElement = document.createElement('div');
                        fileElement.className =
                            'flex items-center justify-between rounded-lg border border-gray-200 p-3 dark:border-gray-700';
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

            function printRequest() {
                const printContent = `
            <html>
                <head>
                    <title>Maintenance Request - {{ $maintenanceRequest->ticket_number }}</title>
                    <style>
                        body { font-family: Arial, sans-serif; padding: 20px; }
                        h1 { color: #333; border-bottom: 2px solid #eee; padding-bottom: 10px; }
                        .section { margin: 20px 0; }
                        .label { font-weight: bold; color: #666; margin-right: 10px; }
                        .value { margin-bottom: 8px; }
                        .badge { display: inline-block; padding: 4px 8px; border-radius: 4px; font-size: 12px; }
                        .description { background: #f5f5f5; padding: 15px; border-radius: 5px; margin: 15px 0; }
                        .footer { margin-top: 30px; padding-top: 20px; border-top: 1px solid #eee; color: #666; font-size: 12px; }
                    </style>
                </head>
                <body>
                    <h1>Maintenance Request: {{ $maintenanceRequest->ticket_number }}</h1>
                    
                    <div class="section">
                        <div class="value"><span class="label">Priority:</span> {{ $maintenanceRequest->getPriorityText() }}</div>
                        <div class="value"><span class="label">Status:</span> {{ $maintenanceRequest->getStatusText() }}</div>
                        <div class="value"><span class="label">Submitted:</span> {{ $maintenanceRequest->getRequestedDate() }}</div>
                        <div class="value"><span class="label">By:</span> {{ $maintenanceRequest->user?->full_name ?? 'Unknown' }}</div>
                    </div>
                    
                    <div class="section">
                        <h3>Equipment Information</h3>
                        <div class="value"><span class="label">Equipment:</span> {{ $maintenanceRequest->item?->name ?? 'Not specified' }}</div>
                        <div class="value"><span class="label">Type:</span> {{ $maintenanceRequest->item?->getTypeText() ?? 'N/A' }}</div>
                        <div class="value"><span class="label">Issue Type:</span> {{ $maintenanceRequest->getIssueTypeText() }}</div>
                    </div>
                    
                    <div class="section">
                        <h3>Problem Description</h3>
                        <div class="description">{{ $maintenanceRequest->description }}</div>
                    </div>
                    
                    @if ($maintenanceRequest->assigned_to)
                    <div class="section">
                        <h3>Assignment</h3>
                        <div class="value"><span class="label">Assigned To:</span> {{ $maintenanceRequest->assignedTechnician?->full_name ?? 'Unknown' }}</div>
                        <div class="value"><span class="label">Assigned Date:</span> {{ $maintenanceRequest->assigned_at?->format('M d, Y h:i A') ?? '' }}</div>
                    </div>
                    @endif
                    
                    @if ($maintenanceRequest->technician_notes)
                    <div class="section">
                        <h3>Technician Notes</h3>
                        <div class="description">{{ $maintenanceRequest->technician_notes }}</div>
                    </div>
                    @endif
                    
                    @if ($maintenanceRequest->resolution_notes)
                    <div class="section">
                        <h3>Resolution Notes</h3>
                        <div class="description">{{ $maintenanceRequest->resolution_notes }}</div>
                    </div>
                    @endif
                    
                    <div class="footer">
                        Printed on: ${new Date().toLocaleDateString()} ${new Date().toLocaleTimeString()}
                    </div>
                </body>
            </html>
        `;

                const printWindow = window.open('', '_blank');
                printWindow.document.write(printContent);
                printWindow.document.close();
                printWindow.print();
            }
        </script>
    @endpush
@endsection
