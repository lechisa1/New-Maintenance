@extends('layouts.app')

@section('content')
<x-common.page-breadcrumb pageTitle="Maintenance Request" />

<div class="grid grid-cols-1 gap-6 lg:grid-cols-3">
    <!-- Maintenance Request Form -->
    <div class="lg:col-span-2">
        <div class="rounded-2xl border border-gray-200 bg-white p-6 dark:border-gray-800 dark:bg-white/[0.03]">
            <h3 class="mb-6 text-lg font-semibold text-gray-800 dark:text-white/90">
                <i class="bi bi-tools me-2"></i>Submit Maintenance Request
            </h3>
            <hr class="mb-6 border-gray-200 dark:border-gray-700">

            <form method="POST" action="#" enctype="multipart/form-data">
                @csrf

                <div class="space-y-6">
                    <!-- Item Selection -->
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
                                <select name="equipment" required
                                    class="h-11 w-full rounded-lg border border-gray-300 bg-transparent px-4 py-2.5 text-sm text-gray-800 shadow-theme-xs focus:border-blue-300 focus:outline-none focus:ring-2 focus:ring-blue-500/10 dark:border-gray-700 dark:bg-gray-900 dark:text-white/90 dark:focus:border-blue-800">
                                    <option value="" disabled selected>Select equipment</option>
                                    <option value="pc_001">PC-001 - Office PC (Room 101)</option>
                                    <option value="pc_002">PC-002 - Office PC (Room 102)</option>
                                    <option value="pc_003">PC-003 - Office PC (Room 103)</option>
                                    <option value="laptop_001">Laptop-001 - Dell XPS 13</option>
                                    <option value="laptop_002">Laptop-002 - MacBook Pro</option>
                                    <option value="printer_001">Printer-001 - HP LaserJet (Main Office)</option>
                                    <option value="printer_002">Printer-002 - Canon Printer (Reception)</option>
                                    <option value="monitor_001">Monitor-001 - 27" Dell Monitor</option>
                                    <option value="keyboard_001">Keyboard-001 - Mechanical Keyboard</option>
                                    <option value="other">Other Equipment</option>
                                </select>
                            </div>

                            <!-- Select Issue Type -->
                            <div>
                                <label class="mb-2 block text-sm font-medium text-gray-700 dark:text-gray-300">
                                    Issue Type <span class="text-red-500">*</span>
                                </label>
                                <select name="issue_type" required
                                    class="h-11 w-full rounded-lg border border-gray-300 bg-transparent px-4 py-2.5 text-sm text-gray-800 shadow-theme-xs focus:border-blue-300 focus:outline-none focus:ring-2 focus:ring-blue-500/10 dark:border-gray-700 dark:bg-gray-900 dark:text-white/90 dark:focus:border-blue-800">
                                    <option value="" disabled selected>Select issue type</option>
                                    <option value="hardware">Hardware Issue</option>
                                    <option value="software">Software Issue</option>
                                    <option value="network">Network/Connectivity</option>
                                    <option value="performance">Performance Problem</option>
                                    <option value="setup">New Setup/Installation</option>
                                    <option value="upgrade">Upgrade Request</option>
                                    <option value="other">Other</option>
                                </select>
                            </div>
                        </div>
                    </div>

                    <!-- Priority Selection -->
                    <div>
                        <h4 class="mb-4 text-sm font-semibold text-gray-800 dark:text-white/90">
                            <i class="bi bi-flag me-1"></i>Priority Level
                        </h4>
                        <div class="grid grid-cols-2 gap-3 sm:grid-cols-4">
                            <label class="cursor-pointer">
                                <input type="radio" name="priority" value="low" checked
                                       class="peer sr-only">
                                <div class="rounded-lg border-2 p-4 text-center transition-all peer-checked:border-blue-500 dark:border-gray-700 peer-checked:dark:border-blue-500 bg-green-100 text-green-800 dark:bg-green-900 dark:text-green-200">
                                    <div class="text-sm font-medium">Low Priority</div>
                                    <div class="mt-1 text-xs text-gray-600 dark:text-gray-400">
                                        Non-urgent, can wait
                                    </div>
                                </div>
                            </label>
                            
                            <label class="cursor-pointer">
                                <input type="radio" name="priority" value="medium"
                                       class="peer sr-only">
                                <div class="rounded-lg border-2 p-4 text-center transition-all peer-checked:border-blue-500 dark:border-gray-700 peer-checked:dark:border-blue-500 bg-yellow-100 text-yellow-800 dark:bg-yellow-900 dark:text-yellow-200">
                                    <div class="text-sm font-medium">Medium Priority</div>
                                    <div class="mt-1 text-xs text-gray-600 dark:text-gray-400">
                                        Normal priority
                                    </div>
                                </div>
                            </label>
                            
                            <label class="cursor-pointer">
                                <input type="radio" name="priority" value="high"
                                       class="peer sr-only">
                                <div class="rounded-lg border-2 p-4 text-center transition-all peer-checked:border-blue-500 dark:border-gray-700 peer-checked:dark:border-blue-500 bg-orange-100 text-orange-800 dark:bg-orange-900 dark:text-orange-200">
                                    <div class="text-sm font-medium">High Priority</div>
                                    <div class="mt-1 text-xs text-gray-600 dark:text-gray-400">
                                        Needs attention soon
                                    </div>
                                </div>
                            </label>
                            
                            <label class="cursor-pointer">
                                <input type="radio" name="priority" value="critical"
                                       class="peer sr-only">
                                <div class="rounded-lg border-2 p-4 text-center transition-all peer-checked:border-blue-500 dark:border-gray-700 peer-checked:dark:border-blue-500 bg-red-100 text-red-800 dark:bg-red-900 dark:text-red-200">
                                    <div class="text-sm font-medium">Critical</div>
                                    <div class="mt-1 text-xs text-gray-600 dark:text-gray-400">
                                        Immediate attention
                                    </div>
                                </div>
                            </label>
                        </div>
                    </div>

                    <!-- Description -->
                    <div>
                   
                        <div>
                            <label class="mb-2 block text-sm font-medium text-gray-700 dark:text-gray-300">
                                Describe the problem <span class="text-red-500">*</span>
                            </label>
                            <textarea name="description" rows="4" required
                                class="w-full rounded-lg border border-gray-300 bg-transparent px-4 py-3 text-sm text-gray-800 shadow-theme-xs placeholder:text-gray-400 focus:border-blue-300 focus:outline-none focus:ring-2 focus:ring-blue-500/10 dark:border-gray-700 dark:bg-gray-900 dark:text-white/90 dark:placeholder:text-white/30 dark:focus:border-blue-800"
                                placeholder="Please provide detailed description of the problem, including any error messages, when it started, and what you've tried..."></textarea>
                            <div class="mt-1 text-xs text-gray-500 dark:text-gray-400">
                                Be as detailed as possible
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
                                           accept=".jpg,.jpeg,.png,.pdf,.doc,.docx,.txt">
                                </div>
                                <p class="mt-1 text-xs text-gray-500 dark:text-gray-400">
                                    Maximum file size: 10MB. Allowed: JPG, PNG, PDF, DOC, TXT
                                </p>
                            </div>
                            
                            <!-- File List -->
                            <div id="file-list" class="mt-4 space-y-2 hidden">
                                <h5 class="text-sm font-medium text-gray-700 dark:text-gray-300">
                                    Attached Files (<span id="file-count">0</span>)
                                </h5>
                            </div>
                        </div>
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
        <div class="rounded-2xl border border-gray-200 bg-white p-6 dark:border-gray-800 dark:bg-white/[0.03]">
            <h3 class="mb-6 text-lg font-semibold text-gray-800 dark:text-white/90">
                <i class="bi bi-clock-history me-2"></i>Recent Requests
            </h3>
            
            <div class="space-y-4">
                <!-- Recent Request 1 -->
                <div class="rounded-lg border border-gray-200 p-4 dark:border-gray-700">
                    <div class="flex items-start justify-between">
                        <div>
                            <div class="font-medium text-gray-800 dark:text-white/90">MTN-2024-0456</div>
                            <div class="text-sm text-gray-600 dark:text-gray-400">PC-001 - Office PC</div>
                        </div>
                        <div class="text-right">
                            <div class="mb-1">
                                <span class="rounded-full px-2 py-0.5 text-xs font-medium bg-orange-100 text-orange-800 dark:bg-orange-900 dark:text-orange-200">
                                    HIGH
                                </span>
                            </div>
                            <div class="text-xs text-gray-500 dark:text-gray-400">
                                Mar 15, 2024
                            </div>
                        </div>
                    </div>
                    <div class="mt-3 flex items-center justify-between">
                        <span class="rounded-full px-2 py-0.5 text-xs font-medium bg-blue-100 text-blue-800 dark:bg-blue-900 dark:text-blue-200">
                            In Progress
                        </span>
                        <button class="text-xs text-blue-600 hover:text-blue-800 dark:text-blue-400 dark:hover:text-blue-300">
                            View Details
                        </button>
                    </div>
                </div>
                
                <!-- Recent Request 2 -->
                <div class="rounded-lg border border-gray-200 p-4 dark:border-gray-700">
                    <div class="flex items-start justify-between">
                        <div>
                            <div class="font-medium text-gray-800 dark:text-white/90">MTN-2024-0455</div>
                            <div class="text-sm text-gray-600 dark:text-gray-400">Laptop-002 - MacBook Pro</div>
                        </div>
                        <div class="text-right">
                            <div class="mb-1">
                                <span class="rounded-full px-2 py-0.5 text-xs font-medium bg-yellow-100 text-yellow-800 dark:bg-yellow-900 dark:text-yellow-200">
                                    MEDIUM
                                </span>
                            </div>
                            <div class="text-xs text-gray-500 dark:text-gray-400">
                                Mar 14, 2024
                            </div>
                        </div>
                    </div>
                    <div class="mt-3 flex items-center justify-between">
                        <span class="rounded-full px-2 py-0.5 text-xs font-medium bg-green-100 text-green-800 dark:bg-green-900 dark:text-green-200">
                            Completed
                        </span>
                        <button class="text-xs text-blue-600 hover:text-blue-800 dark:text-blue-400 dark:hover:text-blue-300">
                            View Details
                        </button>
                    </div>
                </div>
                
                <!-- Recent Request 3 -->
                <div class="rounded-lg border border-gray-200 p-4 dark:border-gray-700">
                    <div class="flex items-start justify-between">
                        <div>
                            <div class="font-medium text-gray-800 dark:text-white/90">MTN-2024-0454</div>
                            <div class="text-sm text-gray-600 dark:text-gray-400">Printer-001 - HP LaserJet</div>
                        </div>
                        <div class="text-right">
                            <div class="mb-1">
                                <span class="rounded-full px-2 py-0.5 text-xs font-medium bg-green-100 text-green-800 dark:bg-green-900 dark:text-green-200">
                                    LOW
                                </span>
                            </div>
                            <div class="text-xs text-gray-500 dark:text-gray-400">
                                Mar 13, 2024
                            </div>
                        </div>
                    </div>
                    <div class="mt-3 flex items-center justify-between">
                        <span class="rounded-full px-2 py-0.5 text-xs font-medium bg-yellow-100 text-yellow-800 dark:bg-yellow-900 dark:text-yellow-200">
                            Pending
                        </span>
                        <button class="text-xs text-blue-600 hover:text-blue-800 dark:text-blue-400 dark:hover:text-blue-300">
                            View Details
                        </button>
                    </div>
                </div>
                
                <!-- Recent Request 4 -->
                <div class="rounded-lg border border-gray-200 p-4 dark:border-gray-700">
                    <div class="flex items-start justify-between">
                        <div>
                            <div class="font-medium text-gray-800 dark:text-white/90">MTN-2024-0453</div>
                            <div class="text-sm text-gray-600 dark:text-gray-400">Monitor-001 - Dell Monitor</div>
                        </div>
                        <div class="text-right">
                            <div class="mb-1">
                                <span class="rounded-full px-2 py-0.5 text-xs font-medium bg-red-100 text-red-800 dark:bg-red-900 dark:text-red-200">
                                    CRITICAL
                                </span>
                            </div>
                            <div class="text-xs text-gray-500 dark:text-gray-400">
                                Mar 12, 2024
                            </div>
                        </div>
                    </div>
                    <div class="mt-3 flex items-center justify-between">
                        <span class="rounded-full px-2 py-0.5 text-xs font-medium bg-blue-100 text-blue-800 dark:bg-blue-900 dark:text-blue-200">
                            In Progress
                        </span>
                        <button class="text-xs text-blue-600 hover:text-blue-800 dark:text-blue-400 dark:hover:text-blue-300">
                            View Details
                        </button>
                    </div>
                </div>
                
                <!-- Recent Request 5 -->
                <div class="rounded-lg border border-gray-200 p-4 dark:border-gray-700">
                    <div class="flex items-start justify-between">
                        <div>
                            <div class="font-medium text-gray-800 dark:text-white/90">MTN-2024-0452</div>
                            <div class="text-sm text-gray-600 dark:text-gray-400">Keyboard-001 - Mechanical</div>
                        </div>
                        <div class="text-right">
                            <div class="mb-1">
                                <span class="rounded-full px-2 py-0.5 text-xs font-medium bg-yellow-100 text-yellow-800 dark:bg-yellow-900 dark:text-yellow-200">
                                    MEDIUM
                                </span>
                            </div>
                            <div class="text-xs text-gray-500 dark:text-gray-400">
                                Mar 11, 2024
                            </div>
                        </div>
                    </div>
                    <div class="mt-3 flex items-center justify-between">
                        <span class="rounded-full px-2 py-0.5 text-xs font-medium bg-green-100 text-green-800 dark:bg-green-900 dark:text-green-200">
                            Completed
                        </span>
                        <button class="text-xs text-blue-600 hover:text-blue-800 dark:text-blue-400 dark:hover:text-blue-300">
                            View Details
                        </button>
                    </div>
                </div>
            </div>
            
            <!-- Stats -->
            <div class="mt-6 grid grid-cols-2 gap-3">
                <div class="rounded-lg border border-gray-200 bg-gray-50 p-4 dark:border-gray-700 dark:bg-gray-800">
                    <div class="text-sm text-gray-500 dark:text-gray-400">Open Requests</div>
                    <div class="mt-1 text-2xl font-semibold text-gray-800 dark:text-white/90">8</div>
                </div>
                <div class="rounded-lg border border-gray-200 bg-gray-50 p-4 dark:border-gray-700 dark:bg-gray-800">
                    <div class="text-sm text-gray-500 dark:text-gray-400">Avg. Response</div>
                    <div class="mt-1 text-2xl font-semibold text-gray-800 dark:text-white/90">2.5h</div>
                </div>
            </div>
            
 
        </div>
    </div>
</div>

<!-- Demo Notice -->
<div class="mt-6 rounded-lg border border-blue-200 bg-blue-50 p-4 dark:border-blue-800 dark:bg-blue-900/20">
    <div class="flex items-start">
        <i class="bi bi-info-circle-fill me-2 mt-0.5 text-blue-500"></i>
        <div>
            <h4 class="text-sm font-medium text-blue-800 dark:text-blue-200">IT Maintenance Request Form</h4>
            <p class="mt-1 text-sm text-blue-700 dark:text-blue-300">
                Use this form to request maintenance for IT equipment such as computers, printers, and other office devices.
                All fields marked with * are required.
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
        return 'bi bi-file';
    }
});
</script>
@endpush
@endsection