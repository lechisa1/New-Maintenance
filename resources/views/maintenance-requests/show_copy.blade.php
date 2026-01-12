@extends('layouts.app')

@section('content')
    <x-common.page-breadcrumb pageTitle="Maintenance Request Details" />

    <div class="grid grid-cols-1 gap-6 lg:grid-cols-3">
        <!-- Main Content -->
        <div class="lg:col-span-2">
            <!-- Request Details Card -->
            <div class="rounded-2xl border border-gray-200 bg-white p-6 dark:border-gray-800 dark:bg-white/[0.03]">
                <div class="flex flex-col items-start justify-between gap-4 sm:flex-row sm:items-center">
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
                            <span class="rounded-full bg-gray-100 px-3 py-1 text-xs font-medium text-gray-800 dark:bg-gray-900 dark:text-gray-200">
                                <i class="bi bi-calendar me-1"></i>{{ $maintenanceRequest->getRequestedDate() }}
                            </span>
                        </div>
                    </div>
                    
                    <div class="flex gap-2">
                        <!-- @if(auth()->user()->hasRole('user') && $maintenanceRequest->user_id === auth()->id())
                            @if($maintenanceRequest->status === App\Models\MaintenanceRequest::STATUS_PENDING) -->
                                <form action="{{ route('maintenance-requests.destroy', $maintenanceRequest) }}" 
                                      method="POST" 
                                      onsubmit="return confirm('Are you sure you want to delete this request?')">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit"
                                        class="rounded-lg border border-red-200 bg-red-50 px-4 py-2 text-sm font-medium text-red-600 shadow-theme-xs hover:bg-red-100 hover:text-red-700 dark:border-red-800 dark:bg-red-900/20 dark:text-red-400 dark:hover:bg-red-900/30">
                                        <i class="bi bi-trash me-2"></i> Delete
                                    </button>
                                </form>
                            <!-- @endif
                        @endif -->
                        
                        <!-- @if(auth()->user()->hasAnyRole(['admin', 'technician'])) -->
                            <a href="{{ route('maintenance-requests.edit', $maintenanceRequest) }}" 
                                class="rounded-lg border border-gray-300 bg-white px-4 py-2 text-sm font-medium text-gray-700 shadow-theme-xs hover:bg-gray-50 dark:border-gray-700 dark:bg-gray-800 dark:text-gray-400 dark:hover:bg-white/[0.03]">
                                <i class="bi bi-pencil me-2"></i> Edit
                            </a>
                        <!-- @endif -->
                    </div>
                </div>

                <hr class="my-6 border-gray-200 dark:border-gray-700">

                <!-- Request Information -->
                <div class="grid grid-cols-1 gap-6 md:grid-cols-2">
                    <!-- Equipment Information -->
                    <div>
                        <h4 class="mb-4 text-sm font-semibold text-gray-800 dark:text-white/90">
                            <i class="bi bi-pc-display me-2"></i>Equipment Information
                        </h4>
                        
                        <div class="space-y-4">
                            <div>
                                <div class="text-xs text-gray-500 dark:text-gray-400">Equipment</div>
                                <div class="mt-1 font-medium text-gray-800 dark:text-white/90">
                                    {{ $maintenanceRequest->item?->name ?? 'Not specified' }}
                                </div>
                            </div>
                            
                            <div>
                                <div class="text-xs text-gray-500 dark:text-gray-400">Equipment Type</div>
                                <div class="mt-1">
                                    <span class="inline-flex items-center rounded-lg bg-blue-50 px-3 py-1.5 text-sm font-medium text-blue-700 dark:bg-blue-900/30 dark:text-blue-300">
                                        <i class="bi bi-{{ $maintenanceRequest->item?->type === 'computer' ? 'pc-display' : 'box' }} me-2"></i>
                                        {{ $maintenanceRequest->item?->getTypeText() ?? 'N/A' }}
                                    </span>
                                </div>
                            </div>
                            
                            <div>
                                <div class="text-xs text-gray-500 dark:text-gray-400">Equipment Status</div>
                                <div class="mt-1">
                                    <span class="rounded-full px-2 py-1 text-xs font-medium {{ $maintenanceRequest->item?->getStatusBadgeClass() ?? 'bg-gray-100 text-gray-800' }}">
                                        {{ $maintenanceRequest->item?->getStatusText() ?? 'Unknown' }}
                                    </span>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Request Details -->
                    <div>
                        <h4 class="mb-4 text-sm font-semibold text-gray-800 dark:text-white/90">
                            <i class="bi bi-info-circle me-2"></i>Request Details
                        </h4>
                        
                        <div class="space-y-4">
                            <div>
                                <div class="text-xs text-gray-500 dark:text-gray-400">Issue Type</div>
                                <div class="mt-1 font-medium text-gray-800 dark:text-white/90">
                                    {{ $maintenanceRequest->getIssueTypeText() }}
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
                                <div class="text-xs text-gray-500 dark:text-gray-400">Submitted On</div>
                                <div class="mt-1 font-medium text-gray-800 dark:text-white/90">
                                    {{ $maintenanceRequest->getRequestedDate() }} at {{ $maintenanceRequest->getRequestedTime() }}
                                </div>
                            </div>
                            
                            @if($maintenanceRequest->assigned_to)
                            <div>
                                <div class="text-xs text-gray-500 dark:text-gray-400">Assigned To</div>
                                <div class="mt-1 font-medium text-gray-800 dark:text-white/90">
                                    {{ $maintenanceRequest->assignedTechnician?->full_name ?? 'Unknown' }}
                                </div>
                                <div class="text-xs text-gray-500 dark:text-gray-400">
                                    {{ $maintenanceRequest->assigned_at?->format('M d, Y h:i A') ?? '' }}
                                </div>
                            </div>
                            @endif
                        </div>
                    </div>
                </div>

                <!-- Problem Description -->
                <div class="mt-6">
                    <h4 class="mb-4 text-sm font-semibold text-gray-800 dark:text-white/90">
                        <i class="bi bi-chat-dots me-2"></i>Problem Description
                    </h4>
                    
                    <div class="rounded-lg border border-gray-200 bg-gray-50 p-4 dark:border-gray-700 dark:bg-gray-800">
                        <p class="whitespace-pre-line text-gray-800 dark:text-white/90">
                            {{ $maintenanceRequest->description }}
                        </p>
                    </div>
                </div>

                <!-- Attached Files -->
                @if($maintenanceRequest->files->count() > 0)
                <div class="mt-6">
                    <h4 class="mb-4 text-sm font-semibold text-gray-800 dark:text-white/90">
                        <i class="bi bi-paperclip me-2"></i>Attached Files
                    </h4>
                    
                    <div class="grid grid-cols-1 gap-3 sm:grid-cols-2">
                        @foreach($maintenanceRequest->files as $file)
                        <div class="flex items-center justify-between rounded-lg border border-gray-200 p-3 dark:border-gray-700">
                            <div class="flex items-center">
                                <i class="bi {{ $file->getFileIcon() }} text-lg text-gray-500"></i>
                                <div class="ml-3">
                                    <div class="text-sm font-medium text-gray-800 dark:text-white/90">
                                        {{ $file->original_name }}
                                    </div>
                                    <div class="text-xs text-gray-500 dark:text-gray-400">
                                        {{ $file->getFileSize() }}
                                    </div>
                                </div>
                            </div>
                            <div class="flex gap-2">
                                <a href="{{ route('maintenance-requests.download-file', [$maintenanceRequest, $file]) }}" 
                                   class="rounded-lg border border-gray-300 bg-white px-2 py-1 text-xs font-medium text-gray-700 shadow-theme-xs hover:bg-gray-50 dark:border-gray-700 dark:bg-gray-800 dark:text-gray-400 dark:hover:bg-white/[0.03]">
                                    <i class="bi bi-download"></i>
                                </a>
                                <!-- @if(auth()->user()->hasAnyRole(['admin', 'technician']) || 
                                    (auth()->user()->hasRole('user') && $maintenanceRequest->user_id === auth()->id())) -->
                                <form action="{{ route('maintenance-requests.delete-file', [$maintenanceRequest, $file]) }}" 
                                      method="POST" 
                                      onsubmit="return confirm('Delete this file?')"
                                      class="inline">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit"
                                        class="rounded-lg border border-red-200 bg-red-50 px-2 py-1 text-xs font-medium text-red-600 shadow-theme-xs hover:bg-red-100 hover:text-red-700 dark:border-red-800 dark:bg-red-900/20 dark:text-red-400 dark:hover:bg-red-900/30">
                                        <i class="bi bi-trash"></i>
                                    </button>
                                </form>
                                <!-- @endif -->
                            </div>
                        </div>
                        @endforeach
                    </div>
                </div>
                @endif

                <!-- Technician Notes -->
                @if($maintenanceRequest->technician_notes || $maintenanceRequest->resolution_notes)
                <div class="mt-6">
                    <h4 class="mb-4 text-sm font-semibold text-gray-800 dark:text-white/90">
                        <i class="bi bi-journal-text me-2"></i>Technician Notes
                    </h4>
                    
                    <div class="space-y-4">
                        @if($maintenanceRequest->technician_notes)
                        <div>
                            <div class="text-xs text-gray-500 dark:text-gray-400">Work Notes</div>
                            <div class="mt-2 rounded-lg border border-gray-200 bg-blue-50 p-4 dark:border-gray-700 dark:bg-blue-900/20">
                                <p class="whitespace-pre-line text-sm text-gray-800 dark:text-white/90">
                                    {{ $maintenanceRequest->technician_notes }}
                                </p>
                            </div>
                        </div>
                        @endif
                        
                        @if($maintenanceRequest->resolution_notes)
                        <div>
                            <div class="text-xs text-gray-500 dark:text-gray-400">Resolution Notes</div>
                            <div class="mt-2 rounded-lg border border-gray-200 bg-green-50 p-4 dark:border-gray-700 dark:bg-green-900/20">
                                <p class="whitespace-pre-line text-sm text-gray-800 dark:text-white/90">
                                    {{ $maintenanceRequest->resolution_notes }}
                                </p>
                            </div>
                        </div>
                        @endif
                    </div>
                </div>
                @endif

                <!-- Status Timeline -->
                <div class="mt-6">
                    <h4 class="mb-4 text-sm font-semibold text-gray-800 dark:text-white/90">
                        <i class="bi bi-clock-history me-2"></i>Status Timeline
                    </h4>
                    
                    <div class="space-y-4">
                        <div class="flex items-center">
                            <div class="mr-4 flex h-8 w-8 items-center justify-center rounded-full bg-green-100 dark:bg-green-900">
                                <i class="bi bi-plus-lg text-green-600 dark:text-green-300"></i>
                            </div>
                            <div class="flex-1">
                                <div class="text-sm font-medium text-gray-800 dark:text-white/90">Request Submitted</div>
                                <div class="text-xs text-gray-500 dark:text-gray-400">
                                    {{ $maintenanceRequest->requested_at->format('M d, Y h:i A') }}
                                </div>
                            </div>
                        </div>
                        
                        @if($maintenanceRequest->assigned_at)
                        <div class="flex items-center">
                            <div class="mr-4 flex h-8 w-8 items-center justify-center rounded-full bg-blue-100 dark:bg-blue-900">
                                <i class="bi bi-person-check text-blue-600 dark:text-blue-300"></i>
                            </div>
                            <div class="flex-1">
                                <div class="text-sm font-medium text-gray-800 dark:text-white/90">Assigned to Technician</div>
                                <div class="text-xs text-gray-500 dark:text-gray-400">
                                    {{ $maintenanceRequest->assigned_at->format('M d, Y h:i A') }}
                                    @if($maintenanceRequest->getResponseTime())
                                    <span class="ml-2 text-green-600 dark:text-green-400">
                                        (Response: {{ $maintenanceRequest->getResponseTime() }} hours)
                                    </span>
                                    @endif
                                </div>
                            </div>
                        </div>
                        @endif
                        
                        @if($maintenanceRequest->started_at)
                        <div class="flex items-center">
                            <div class="mr-4 flex h-8 w-8 items-center justify-center rounded-full bg-indigo-100 dark:bg-indigo-900">
                                <i class="bi bi-tools text-indigo-600 dark:text-indigo-300"></i>
                            </div>
                            <div class="flex-1">
                                <div class="text-sm font-medium text-gray-800 dark:text-white/90">Work Started</div>
                                <div class="text-xs text-gray-500 dark:text-gray-400">
                                    {{ $maintenanceRequest->started_at->format('M d, Y h:i A') }}
                                </div>
                            </div>
                        </div>
                        @endif
                        
                        @if($maintenanceRequest->completed_at)
                        <div class="flex items-center">
                            <div class="mr-4 flex h-8 w-8 items-center justify-center rounded-full bg-green-100 dark:bg-green-900">
                                <i class="bi bi-check-circle text-green-600 dark:text-green-300"></i>
                            </div>
                            <div class="flex-1">
                                <div class="text-sm font-medium text-gray-800 dark:text-white/90">Completed</div>
                                <div class="text-xs text-gray-500 dark:text-gray-400">
                                    {{ $maintenanceRequest->completed_at->format('M d, Y h:i A') }}
                                    @if($maintenanceRequest->getResolutionTime())
                                    <span class="ml-2 text-green-600 dark:text-green-400">
                                        (Resolution: {{ $maintenanceRequest->getResolutionTime() }} hours)
                                    </span>
                                    @endif
                                </div>
                            </div>
                        </div>
                        @endif
                        
                        @if($maintenanceRequest->rejected_at)
                        <div class="flex items-center">
                            <div class="mr-4 flex h-8 w-8 items-center justify-center rounded-full bg-red-100 dark:bg-red-900">
                                <i class="bi bi-x-circle text-red-600 dark:text-red-300"></i>
                            </div>
                            <div class="flex-1">
                                <div class="text-sm font-medium text-gray-800 dark:text-white/90">Rejected</div>
                                <div class="text-xs text-gray-500 dark:text-gray-400">
                                    {{ $maintenanceRequest->rejected_at->format('M d, Y h:i A') }}
                                </div>
                            </div>
                        </div>
                        @endif
                    </div>
                </div>
            </div>
        </div>

        <!-- Sidebar - Actions & Info -->
        <div class="space-y-6">
            <!-- Quick Actions -->
            <div class="rounded-2xl border border-gray-200 bg-white p-6 dark:border-gray-800 dark:bg-white/[0.03]">
                <h3 class="mb-4 text-sm font-semibold text-gray-800 dark:text-white/90">
                    <i class="bi bi-lightning me-2"></i>Quick Actions
                </h3>
                
                <div class="space-y-3">
                    <!-- @if(auth()->user()->hasAnyRole(['admin', 'technician_lead']) && $maintenanceRequest->status === App\Models\MaintenanceRequest::STATUS_PENDING) -->
                    <!-- Assign to Technician -->
                    <div x-data="{ open: false }">
                        <button @click="open = true"
                            class="flex w-full items-center rounded-lg border border-gray-200 bg-white px-4 py-3 text-sm font-medium text-gray-700 shadow-theme-xs hover:bg-gray-50 dark:border-gray-700 dark:bg-gray-800 dark:text-gray-400 dark:hover:bg-white/[0.03]">
                            <i class="bi bi-person-plus me-3"></i>
                            Assign to Technician
                        </button>
                        
                        <x-ui.modal :isOpen="false" x-model="open">
                            <div class="p-6">
                                <h3 class="text-lg font-semibold text-gray-800 dark:text-white/90">
                                    Assign Request to Technician
                                </h3>
                                <form action="{{ route('maintenance-requests.assign', $maintenanceRequest) }}" method="POST" class="mt-4">
                                    @csrf
                                    <div class="space-y-4">
                                        <div>
                                            <label class="mb-2 block text-sm font-medium text-gray-700 dark:text-gray-300">
                                                Select Technician
                                            </label>
                                            <select name="assigned_to" required
                                                class="h-11 w-full rounded-lg border border-gray-300 bg-transparent px-4 py-2.5 text-sm text-gray-800 shadow-theme-xs focus:border-blue-300 focus:outline-none focus:ring-2 focus:ring-blue-500/10 dark:border-gray-700 dark:bg-gray-900 dark:text-white/90 dark:focus:border-blue-800">
                                                <option value="" disabled selected>Select technician</option>
                                                @foreach($technicians as $tech)
                                                    <option value="{{ $tech->id }}">
                                                        {{ $tech->full_name }} ({{ $tech->email }})
                                                    </option>
                                                @endforeach
                                            </select>
                                        </div>
                                        <div class="flex justify-end gap-3">
                                            <button @click="open = false" type="button"
                                                class="rounded-lg border border-gray-300 bg-white px-4 py-2.5 text-sm font-medium text-gray-700 shadow-theme-xs hover:bg-gray-50 dark:border-gray-700 dark:bg-gray-800 dark:text-gray-400 dark:hover:bg-white/[0.03]">
                                                Cancel
                                            </button>
                                            <button type="submit"
                                                class="rounded-lg bg-blue-500 px-4 py-2.5 text-sm font-medium text-white shadow-theme-xs hover:bg-blue-600 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:ring-offset-2 dark:focus:ring-offset-gray-900">
                                                Assign
                                            </button>
                                        </div>
                                    </div>
                                </form>
                            </div>
                        </x-ui.modal>
                    </div>
                    <!-- @endif -->
                    
                    <!-- @if(auth()->user()->hasRole('technician') && 
                        $maintenanceRequest->assigned_to === auth()->id() && 
                        $maintenanceRequest->isOpen()) -->
                    <!-- Update Status (Technician) -->
                    <div x-data="{ open: false }">
                        <button @click="open = true"
                            class="flex w-full items-center rounded-lg border border-gray-200 bg-white px-4 py-3 text-sm font-medium text-gray-700 shadow-theme-xs hover:bg-gray-50 dark:border-gray-700 dark:bg-gray-800 dark:text-gray-400 dark:hover:bg-white/[0.03]">
                            <i class="bi bi-arrow-repeat me-3"></i>
                            Update Status
                        </button>
                        
                        <x-ui.modal :isOpen="false" x-model="open">
                            <div class="p-6">
                                <h3 class="text-lg font-semibold text-gray-800 dark:text-white/90">
                                    Update Request Status
                                </h3>
                                <form action="{{ route('maintenance-requests.update-status', $maintenanceRequest) }}" method="POST" class="mt-4">
                                    @csrf
                                    <div class="space-y-4">
                                        <div>
                                            <label class="mb-2 block text-sm font-medium text-gray-700 dark:text-gray-300">
                                                New Status
                                            </label>
                                            <select name="status" required
                                                class="h-11 w-full rounded-lg border border-gray-300 bg-transparent px-4 py-2.5 text-sm text-gray-800 shadow-theme-xs focus:border-blue-300 focus:outline-none focus:ring-2 focus:ring-blue-500/10 dark:border-gray-700 dark:bg-gray-900 dark:text-white/90 dark:focus:border-blue-800">
                                                <option value="" disabled selected>Select status</option>
                                                <option value="in_progress">Start Work (In Progress)</option>
                                                <option value="completed">Mark as Completed</option>
                                                <option value="not_fixed">Could Not Fix</option>
                                            </select>
                                        </div>
                                        <div>
                                            <label class="mb-2 block text-sm font-medium text-gray-700 dark:text-gray-300">
                                                Resolution Notes (Optional)
                                            </label>
                                            <textarea name="resolution_notes" rows="3"
                                                class="w-full rounded-lg border border-gray-300 bg-transparent px-4 py-2.5 text-sm text-gray-800 shadow-theme-xs placeholder:text-gray-400 focus:border-blue-300 focus:outline-none focus:ring-2 focus:ring-blue-500/10 dark:border-gray-700 dark:bg-gray-900 dark:text-white/90 dark:placeholder:text-white/30 dark:focus:border-blue-800"
                                                placeholder="Describe what was done or why it couldn't be fixed..."></textarea>
                                        </div>
                                        <div class="flex justify-end gap-3">
                                            <button @click="open = false" type="button"
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
                        </x-ui.modal>
                    </div>
                    <!-- @endif -->
                    
                    <a href="{{ route('maintenance-requests.create') }}" 
                        class="flex items-center rounded-lg border border-gray-200 bg-white px-4 py-3 text-sm font-medium text-gray-700 shadow-theme-xs hover:bg-gray-50 dark:border-gray-700 dark:bg-gray-800 dark:text-gray-400 dark:hover:bg-white/[0.03]">
                        <i class="bi bi-plus-lg me-3"></i>
                        Submit New Request
                    </a>
                    
                    <a href="{{ route('maintenance-requests.index') }}" 
                        class="flex items-center rounded-lg border border-gray-200 bg-white px-4 py-3 text-sm font-medium text-gray-700 shadow-theme-xs hover:bg-gray-50 dark:border-gray-700 dark:bg-gray-800 dark:text-gray-400 dark:hover:bg-white/[0.03]">
                        <i class="bi bi-list-ul me-3"></i>
                        View All Requests
                    </a>
                </div>
            </div>

            <!-- Equipment Information -->
            @if($maintenanceRequest->item)
            <div class="rounded-2xl border border-gray-200 bg-white p-6 dark:border-gray-800 dark:bg-white/[0.03]">
                <h3 class="mb-4 text-sm font-semibold text-gray-800 dark:text-white/90">
                    <i class="bi bi-pc-display me-2"></i>Equipment Details
                </h3>
                
                <div class="space-y-3">
                    <div class="rounded-lg border border-gray-200 p-3 dark:border-gray-700">
                        <div class="flex items-center">
                            <div class="mr-3 flex h-10 w-10 items-center justify-center rounded-full bg-blue-100 dark:bg-blue-900/30">
                                <i class="bi bi-{{ $maintenanceRequest->item->type === 'computer' ? 'pc-display' : 'box' }} text-blue-600 dark:text-blue-400"></i>
                            </div>
                            <div>
                                <div class="font-medium text-gray-800 dark:text-white/90">{{ $maintenanceRequest->item->name }}</div>
                                <div class="text-sm text-gray-600 dark:text-gray-400">
                                    {{ $maintenanceRequest->item->getTypeText() }} • {{ $maintenanceRequest->item->getUnitText() }}
                                </div>
                            </div>
                        </div>
                    </div>
                    
                    <div class="grid grid-cols-2 gap-3">
                        <div class="rounded-lg bg-gray-50 p-3 dark:bg-gray-800">
                            <div class="text-xs text-gray-500 dark:text-gray-400">Current Status</div>
                            <div class="mt-1">
                                <span class="inline-flex items-center rounded-full px-2 py-0.5 text-xs font-medium {{ $maintenanceRequest->item->getStatusBadgeClass() }}">
                                    {{ $maintenanceRequest->item->getStatusText() }}
                                </span>
                            </div>
                        </div>
                        
                        <div class="rounded-lg bg-gray-50 p-3 dark:bg-gray-800">
                            <div class="text-xs text-gray-500 dark:text-gray-400">Equipment ID</div>
                            <div class="mt-1 font-medium text-gray-800 dark:text-white/90">
                                #{{ str_pad($maintenanceRequest->item->id, 6, '0', STR_PAD_LEFT) }}
                            </div>
                        </div>
                    </div>
                    
                    <a href="{{ route('items.show', $maintenanceRequest->item) }}" 
                        class="flex w-full items-center justify-center rounded-lg border border-gray-300 bg-white px-4 py-2.5 text-sm font-medium text-gray-700 shadow-theme-xs hover:bg-gray-50 dark:border-gray-700 dark:bg-gray-800 dark:text-gray-400 dark:hover:bg-white/[0.03]">
                        <i class="bi bi-eye me-2"></i> View Full Equipment Details
                    </a>
                </div>
            </div>
            @endif

            <!-- Similar Requests -->
            @if($similarRequests->count() > 0)
            <div class="rounded-2xl border border-gray-200 bg-white p-6 dark:border-gray-800 dark:bg-white/[0.03]">
                <h3 class="mb-4 text-sm font-semibold text-gray-800 dark:text-white/90">
                    <i class="bi bi-diagram-3 me-2"></i>Similar Requests
                </h3>
                
                <div class="space-y-3">
                    @foreach($similarRequests as $similarRequest)
                    <a href="{{ route('maintenance-requests.show', $similarRequest) }}" 
                        class="block rounded-lg border border-gray-200 p-3 hover:border-blue-300 hover:bg-blue-50 dark:border-gray-700 dark:hover:border-blue-800 dark:hover:bg-blue-900/20">
                        <div class="flex items-center justify-between">
                            <div>
                                <div class="font-medium text-gray-800 dark:text-white/90">{{ $similarRequest->ticket_number }}</div>
                                <div class="text-sm text-gray-600 dark:text-gray-400">
                                    {{ $similarRequest->getRequestedDate() }}
                                </div>
                            </div>
                            <span class="rounded-full px-2 py-0.5 text-xs font-medium {{ $similarRequest->getStatusBadgeClass() }}">
                                {{ $similarRequest->getStatusText() }}
                            </span>
                        </div>
                    </a>
                    @endforeach
                </div>
            </div>
            @endif
        </div>
    </div>

    <!-- Success Message -->
    @if(session('success'))
        <div class="fixed bottom-4 right-4 z-50">
            <x-ui.alert variant="success" title="Success" :message="session('success')" />
        </div>
    @endif
@endsection