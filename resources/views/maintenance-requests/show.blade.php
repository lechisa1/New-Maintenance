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
                            <span
                                class="rounded-full px-3 py-1 text-xs font-medium {{ $maintenanceRequest->getPriorityBadgeClass() }}">
                                {{ strtoupper($maintenanceRequest->priority) }} PRIORITY
                            </span>
                            <span
                                class="rounded-full px-3 py-1 text-xs font-medium {{ $maintenanceRequest->getStatusBadgeClass() }}">
                                {{ $maintenanceRequest->getStatusText() }}
                            </span>
                            <span
                                class="rounded-full bg-gray-100 px-3 py-1 text-xs font-medium text-gray-800 dark:bg-gray-900 dark:text-gray-200">
                                <i class="bi bi-calendar me-1"></i>{{ $maintenanceRequest->getRequestedDate() }}
                            </span>
                        </div>
                    </div>

                    <div class="flex gap-2">
                        @if ($maintenanceRequest->user_id === auth()->id())
                            @if ($maintenanceRequest->status === App\Models\MaintenanceRequest::STATUS_PENDING)
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




                                <a href="{{ route('maintenance-requests.edit', $maintenanceRequest) }}"
                                    class="rounded-lg border border-gray-300 bg-white px-4 py-2 text-sm font-medium text-gray-700 shadow-theme-xs hover:bg-gray-50 dark:border-gray-700 dark:bg-gray-800 dark:text-gray-400 dark:hover:bg-white/[0.03]">
                                    <i class="bi bi-pencil me-2"></i> Edit
                                </a>
                            @endif
                        @endif

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
                                    <span
                                        class="inline-flex items-center rounded-lg bg-blue-50 px-3 py-1.5 text-sm font-medium text-blue-700 dark:bg-blue-900/30 dark:text-blue-300">
                                        <i
                                            class="bi bi-{{ $maintenanceRequest->item?->type === 'computer' ? 'pc-display' : 'box' }} me-2"></i>
                                        {{ $maintenanceRequest->item?->getTypeText() ?? 'N/A' }}
                                    </span>
                                </div>
                            </div>

                            <div>
                                <div class="text-xs text-gray-500 dark:text-gray-400">Equipment Status</div>
                                <div class="mt-1">
                                    <span
                                        class="rounded-full px-2 py-1 text-xs font-medium {{ $maintenanceRequest->item?->getStatusBadgeClass() ?? 'bg-gray-100 text-gray-800' }}">
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
                                    {{ $maintenanceRequest->getRequestedDate() }} at
                                    {{ $maintenanceRequest->getRequestedTime() }}
                                </div>
                            </div>

                            @if ($maintenanceRequest->assigned_to)
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
                @if ($maintenanceRequest->files->count() > 0)
                    <div class="mt-6">
                        <h4 class="mb-4 text-sm font-semibold text-gray-800 dark:text-white/90">
                            <i class="bi bi-paperclip me-2"></i>Attached Files
                        </h4>

                        <div class="grid grid-cols-1 gap-3 sm:grid-cols-2">
                            @foreach ($maintenanceRequest->files as $file)
                                <div
                                    class="flex items-center justify-between rounded-lg border border-gray-200 p-3 dark:border-gray-700">
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

                                        <form
                                            action="{{ route('maintenance-requests.delete-file', [$maintenanceRequest, $file]) }}"
                                            method="POST" onsubmit="return confirm('Delete this file?')" class="inline">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit"
                                                class="rounded-lg border border-red-200 bg-red-50 px-2 py-1 text-xs font-medium text-red-600 shadow-theme-xs hover:bg-red-100 hover:text-red-700 dark:border-red-800 dark:bg-red-900/20 dark:text-red-400 dark:hover:bg-red-900/30">
                                                <i class="bi bi-trash"></i>
                                            </button>
                                        </form>

                                    </div>
                                </div>
                            @endforeach
                        </div>
                    </div>
                @endif

                <!-- Technician Notes -->
                @if ($maintenanceRequest->technician_notes || $maintenanceRequest->resolution_notes)
                    <div class="mt-6">
                        <h4 class="mb-4 text-sm font-semibold text-gray-800 dark:text-white/90">
                            <i class="bi bi-journal-text me-2"></i>Technician Notes
                        </h4>

                        <div class="space-y-4">
                            @if ($maintenanceRequest->technician_notes)
                                <div>
                                    <div class="text-xs text-gray-500 dark:text-gray-400">Work Notes</div>
                                    <div
                                        class="mt-2 rounded-lg border border-gray-200 bg-blue-50 p-4 dark:border-gray-700 dark:bg-blue-900/20">
                                        <p class="whitespace-pre-line text-sm text-gray-800 dark:text-white/90">
                                            {{ $maintenanceRequest->technician_notes }}
                                        </p>
                                    </div>
                                </div>
                            @endif

                            @if ($maintenanceRequest->resolution_notes)
                                <div>
                                    <div class="text-xs text-gray-500 dark:text-gray-400">Resolution Notes</div>
                                    <div
                                        class="mt-2 rounded-lg border border-gray-200 bg-green-50 p-4 dark:border-gray-700 dark:bg-green-900/20">
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
                            <div
                                class="mr-4 flex h-8 w-8 items-center justify-center rounded-full bg-green-100 dark:bg-green-900">
                                <i class="bi bi-plus-lg text-green-600 dark:text-green-300"></i>
                            </div>
                            <div class="flex-1">
                                <div class="text-sm font-medium text-gray-800 dark:text-white/90">Request Submitted</div>
                                <div class="text-xs text-gray-500 dark:text-gray-400">
                                    {{ $maintenanceRequest->requested_at->format('M d, Y h:i A') }}
                                </div>
                            </div>
                        </div>

                        @if ($maintenanceRequest->assigned_at)
                            <div class="flex items-center">
                                <div
                                    class="mr-4 flex h-8 w-8 items-center justify-center rounded-full bg-blue-100 dark:bg-blue-900">
                                    <i class="bi bi-person-check text-blue-600 dark:text-blue-300"></i>
                                </div>
                                <div class="flex-1">
                                    <div class="text-sm font-medium text-gray-800 dark:text-white/90">Assigned to Technician
                                    </div>
                                    <div class="text-xs text-gray-500 dark:text-gray-400">
                                        {{ $maintenanceRequest->assigned_at->format('M d, Y h:i A') }}
                                        @if ($maintenanceRequest->getResponseTime())
                                            <span class="ml-2 text-green-600 dark:text-green-400">
                                                (Response: {{ $maintenanceRequest->getResponseTime() }} hours)
                                            </span>
                                        @endif
                                    </div>
                                </div>
                            </div>
                        @endif

                        @if ($maintenanceRequest->started_at)
                            <div class="flex items-center">
                                <div
                                    class="mr-4 flex h-8 w-8 items-center justify-center rounded-full bg-indigo-100 dark:bg-indigo-900">
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

                        @if ($maintenanceRequest->completed_at)
                            <div class="flex items-center">
                                <div
                                    class="mr-4 flex h-8 w-8 items-center justify-center rounded-full bg-green-100 dark:bg-green-900">
                                    <i class="bi bi-check-circle text-green-600 dark:text-green-300"></i>
                                </div>
                                <div class="flex-1">
                                    <div class="text-sm font-medium text-gray-800 dark:text-white/90">Completed</div>
                                    <div class="text-xs text-gray-500 dark:text-gray-400">
                                        {{ $maintenanceRequest->completed_at->format('M d, Y h:i A') }}
                                        @if ($maintenanceRequest->getResolutionTime())
                                            <span class="ml-2 text-green-600 dark:text-green-400">
                                                (Resolution: {{ $maintenanceRequest->getResolutionTime() }} hours)
                                            </span>
                                        @endif
                                    </div>
                                </div>
                            </div>
                        @endif

                        @if ($maintenanceRequest->rejected_at)
                            <div class="flex items-center">
                                <div
                                    class="mr-4 flex h-8 w-8 items-center justify-center rounded-full bg-red-100 dark:bg-red-900">
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

                    <!-- Assign to Technician (only for users with maintenance_requests.assign permission) -->
                    @can('maintenance_requests.assign')
                        @if ($maintenanceRequest->assigned_to === null)
                            <div x-data="{ showAssignModal: false }">
                                <button @click="showAssignModal = true"
                                    class="flex w-full items-center rounded-lg border border-gray-200 bg-white px-4 py-3 text-sm font-medium text-gray-700 shadow-theme-xs hover:bg-gray-50 dark:border-gray-700 dark:bg-gray-800 dark:text-gray-400 dark:hover:bg-white/[0.03]">
                                    <i class="bi bi-person-plus me-3"></i>
                                    Assign to Technician
                                </button>

                                <!-- Simple Modal (not using x-ui.modal) -->
                                <div x-show="showAssignModal" x-cloak style="display: none;"
                                    class="fixed inset-0 z-50 flex items-center justify-center bg-black bg-opacity-50">
                                    <div class="bg-white dark:bg-gray-800 rounded-lg p-6 w-full max-w-md">
                                        <div class="flex justify-between items-center mb-4">
                                            <h3 class="text-lg font-semibold text-gray-800 dark:text-white/90">
                                                Assign Request to Technician
                                            </h3>
                                            <button @click="showAssignModal = false"
                                                class="text-gray-500 hover:text-gray-700 dark:text-gray-400 dark:hover:text-gray-200">
                                                <i class="bi bi-x-lg"></i>
                                            </button>
                                        </div>

                                        <form action="{{ route('maintenance-requests.assign', $maintenanceRequest) }}"
                                            method="POST" class="mt-4" id="assign-technician-form">
                                            @csrf
                                            @method('PUT')
                                            <div class="space-y-4">
                                                <div>
                                                    <label
                                                        class="mb-2 block text-sm font-medium text-gray-700 dark:text-gray-300">
                                                        Select Technician
                                                    </label>
                                                    <select name="assigned_to" required
                                                        class="h-11 w-full rounded-lg border border-gray-300 bg-transparent px-4 py-2.5 text-sm text-gray-800 shadow-theme-xs focus:border-blue-300 focus:outline-none focus:ring-2 focus:ring-blue-500/10 dark:border-gray-700 dark:bg-gray-900 dark:text-white/90 dark:focus:border-blue-800">
                                                        <option value="" disabled
                                                            {{ !$maintenanceRequest->assigned_to ? 'selected' : '' }}>Select
                                                            technician...</option>
                                                        @foreach ($technicians as $id => $name)
                                                            <option value="{{ $id }}"
                                                                {{ $maintenanceRequest->assigned_to == $id ? 'selected' : '' }}>
                                                                {{ $name }}
                                                            </option>
                                                        @endforeach
                                                    </select>
                                                    <p class="mt-1 text-xs text-gray-500 dark:text-gray-400">
                                                        Only users with 'reports.assign' permission are listed
                                                    </p>
                                                </div>

                                                <div>
                                                    <label
                                                        class="mb-2 block text-sm font-medium text-gray-700 dark:text-gray-300">
                                                        Technician Notes (Optional)
                                                    </label>
                                                    <textarea name="technician_notes" rows="3"
                                                        class="w-full rounded-lg border border-gray-300 bg-transparent px-4 py-2.5 text-sm text-gray-800 shadow-theme-xs placeholder:text-gray-400 focus:border-blue-300 focus:outline-none focus:ring-2 focus:ring-blue-500/10 dark:border-gray-700 dark:bg-gray-900 dark:text-white/90 dark:placeholder:text-white/30 dark:focus:border-blue-800"
                                                        placeholder="Add any notes for the technician...">{{ $maintenanceRequest->technician_notes }}</textarea>
                                                </div>

                                                <div class="flex justify-end gap-3">
                                                    <button @click="showAssignModal = false" type="button"
                                                        class="rounded-lg border border-gray-300 bg-white px-4 py-2.5 text-sm font-medium text-gray-700 shadow-theme-xs hover:bg-gray-50 dark:border-gray-700 dark:bg-gray-800 dark:text-gray-400 dark:hover:bg-white/[0.03]">
                                                        Cancel
                                                    </button>
                                                    <button type="submit"
                                                        class="rounded-lg bg-blue-500 px-4 py-2.5 text-sm font-medium text-white shadow-theme-xs hover:bg-blue-600 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:ring-offset-2 dark:focus:ring-offset-gray-900">
                                                        Assign Technician
                                                    </button>
                                                </div>
                                            </div>
                                        </form>
                                    </div>
                                </div>
                            </div>
                        @endif
                    @endcan

                    <!-- Update Status (only for users with reports.update-status permission) -->
                    @can('reports.update-status')
                        @if (in_array($maintenanceRequest->status, ['assigned', 'in_progress']))
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
                                        <form action="{{ route('maintenance-requests.update-status', $maintenanceRequest) }}"
                                            method="POST" class="mt-4">
                                            @csrf
                                            <div class="space-y-4">
                                                <div>
                                                    <label
                                                        class="mb-2 block text-sm font-medium text-gray-700 dark:text-gray-300">
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
                                                    <label
                                                        class="mb-2 block text-sm font-medium text-gray-700 dark:text-gray-300">
                                                        Resolution Notes (Required for completion)
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
                        @endif
                    @endcan

                    <!-- Submit New Request (only for users with maintenance_requests.create permission) -->
                    @can('maintenance_requests.create')
                        <a href="{{ route('maintenance-requests.create') }}"
                            class="flex items-center rounded-lg border border-gray-200 bg-white px-4 py-3 text-sm font-medium text-gray-700 shadow-theme-xs hover:bg-gray-50 dark:border-gray-700 dark:bg-gray-800 dark:text-gray-400 dark:hover:bg-white/[0.03]">
                            <i class="bi bi-plus-lg me-3"></i>
                            Submit New Request
                        </a>
                    @endcan

                    <!-- View All Requests (only for users with maintenance_requests.view-all permission) -->
                    @can('maintenance_requests.view-all')
                        <a href="{{ route('maintenance-requests.index') }}"
                            class="flex items-center rounded-lg border border-gray-200 bg-white px-4 py-3 text-sm font-medium text-gray-700 shadow-theme-xs hover:bg-gray-50 dark:border-gray-700 dark:bg-gray-800 dark:text-gray-400 dark:hover:bg-white/[0.03]">
                            <i class="bi bi-list-ul me-3"></i>
                            View All Requests
                        </a>
                    @endcan

                    <!-- Edit Request (only for requester or users with maintenance_requests.update permission) -->
                    @if (auth()->id() == $maintenanceRequest->user_id && auth()->user()->can('maintenance_requests.update'))
                        @if (in_array($maintenanceRequest->status, ['pending', 'waiting_approval']))
                            <a href="{{ route('maintenance-requests.edit', $maintenanceRequest) }}"
                                class="flex items-center rounded-lg border border-gray-200 bg-white px-4 py-3 text-sm font-medium text-gray-700 shadow-theme-xs hover:bg-gray-50 dark:border-gray-700 dark:bg-gray-800 dark:text-gray-400 dark:hover:bg-white/[0.03]">
                                <i class="bi bi-pencil me-3"></i>
                                Edit Request
                            </a>
                        @endif
                    @endif

                    <!-- Delete Request (only for requester or users with maintenance_requests.delete permission) -->
                    @if (auth()->id() == $maintenanceRequest->user_id && auth()->user()->can('maintenance_requests.delete'))
                        @if (in_array($maintenanceRequest->status, ['pending', 'waiting_approval']))
                            <form action="{{ route('maintenance-requests.destroy', $maintenanceRequest) }}"
                                method="POST" onsubmit="return confirm('Are you sure you want to delete this request?')"
                                class="w-full">
                                @csrf
                                @method('DELETE')
                                <button type="submit"
                                    class="flex w-full items-center rounded-lg border border-red-200 bg-red-50 px-4 py-3 text-sm font-medium text-red-600 shadow-theme-xs hover:bg-red-100 hover:text-red-700 dark:border-red-800 dark:bg-red-900/20 dark:text-red-400 dark:hover:bg-red-900/30">
                                    <i class="bi bi-trash me-3"></i>
                                    Delete Request
                                </button>
                            </form>
                        @endif
                    @endif


                    <!-- Approve/Reject Request (only for approvers) -->
                    @if ($maintenanceRequest->status === 'waiting_approval')
                        @php
                            $canApprove = false;
                            // Check if user is the approver based on your logic
                            $approver = $maintenanceRequest->getNextApprover();
                            if ($approver && auth()->id() == $approver->id) {
                                $canApprove = true;
                            }
                        @endphp

                        @if ($canApprove || auth()->user()->can('maintenance_requests.approve'))
                            <div class="grid grid-cols-2 gap-3">
                                <!-- Approve Button -->
                                <!-- Approve Button with Modal -->
                                <div x-data="{ showApproveModal: false }">
                                    <button @click="showApproveModal = true"
                                        class="flex w-full items-center justify-center rounded-lg border border-green-200 bg-green-50 px-4 py-3 text-sm font-medium text-green-700 shadow-theme-xs hover:bg-green-100 hover:text-green-800 dark:border-green-800 dark:bg-green-900/20 dark:text-green-400 dark:hover:bg-green-900/30">
                                        <i class="bi bi-check-circle me-2"></i>
                                        Approve
                                    </button>

                                    <!-- Approve Modal -->
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
                                                        <label
                                                            class="mb-2 block text-sm font-medium text-gray-700 dark:text-gray-300">
                                                            Approval Notes (Optional)
                                                        </label>
                                                        <textarea name="approval_notes" rows="3"
                                                            class="w-full rounded-lg border border-gray-300 bg-transparent px-4 py-2.5 text-sm text-gray-800 shadow-theme-xs placeholder:text-gray-400 focus:border-blue-300 focus:outline-none focus:ring-2 focus:ring-blue-500/10 dark:border-gray-700 dark:bg-gray-900 dark:text-white/90 dark:placeholder:text-white/30 dark:focus:border-blue-800"
                                                            placeholder="Add any notes or comments about this approval..."></textarea>
                                                    </div>

                                                    <!-- File Attachments -->
                                                    <div>
                                                        <label
                                                            class="mb-2 block text-sm font-medium text-gray-700 dark:text-gray-300">
                                                            Attach Files (Optional)
                                                        </label>
                                                        <div class="mt-1">
                                                            <input type="file" name="attachments[]" multiple
                                                                accept=".pdf,.doc,.docx,.xls,.xlsx,.jpg,.jpeg,.png,.gif"
                                                                class="block w-full text-sm text-gray-500 file:mr-4 file:py-2 file:px-4 file:rounded-lg file:border-0 file:text-sm file:font-medium file:bg-blue-50 file:text-blue-700 hover:file:bg-blue-100 dark:file:bg-blue-900/30 dark:file:text-blue-300">
                                                            <p class="mt-1 text-xs text-gray-500 dark:text-gray-400">
                                                                You can upload multiple files (PDF, Word, Excel, Images).
                                                                Max 5MB each.
                                                            </p>
                                                        </div>

                                                        <!-- Preview container -->
                                                        <div id="file-preview" class="mt-2 space-y-2 hidden">
                                                            <div
                                                                class="text-xs font-medium text-gray-700 dark:text-gray-300">
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

                                <!-- Reject Button with Modal -->
                                <div x-data="{ showRejectModal: false }">
                                    <button @click="showRejectModal = true"
                                        class="flex w-full items-center justify-center rounded-lg border border-red-200 bg-red-50 px-4 py-3 text-sm font-medium text-red-700 shadow-theme-xs hover:bg-red-100 hover:text-red-800 dark:border-red-800 dark:bg-red-900/20 dark:text-red-400 dark:hover:bg-red-900/30">
                                        <i class="bi bi-x-circle me-2"></i>
                                        Reject
                                    </button>

                                    <!-- Reject Modal -->
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

                                            <form action="{{ route('approvals.reject', $maintenanceRequest) }}"
                                                method="POST" id="reject-request-form">
                                                @csrf
                                                <div class="space-y-4">
                                                    <div>
                                                        <label
                                                            class="mb-2 block text-sm font-medium text-gray-700 dark:text-gray-300">
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
                            </div>
                        @endif
                    @endif
                    @if ($maintenanceRequest->approved_at)
                        <!-- Approved By Information -->
                        <div class="mt-4 p-4 rounded-lg bg-green-50 dark:bg-green-900/20">
                            <div class="flex items-center">
                                <div
                                    class="mr-3 flex h-8 w-8 items-center justify-center rounded-full bg-green-100 dark:bg-green-900/30">
                                    <i class="bi bi-check-circle text-green-600 dark:text-green-300"></i>
                                </div>
                                <div>
                                    <div class="text-sm font-medium text-gray-800 dark:text-white/90">
                                        Approved by
                                        {{ $maintenanceRequest->approvedByUser ? $maintenanceRequest->approvedByUser->full_name : 'N/A' }}
                                    </div>
                                    <div class="text-xs text-gray-600 dark:text-gray-400">
                                        on {{ $maintenanceRequest->approved_at->format('M d, Y h:i A') }}
                                    </div>
                                </div>

                            </div>
                        </div>
                    @endif

                    <!-- Add Work Log Button (only for assigned technician) -->
                    @if (auth()->user()->id == $maintenanceRequest->assigned_to &&
                            in_array($maintenanceRequest->status, ['assigned', 'in_progress', 'approved', 'pending']))
                        <div x-data="{ showWorkLogModal: false }">
                            <!-- ADD THIS BUTTON -->
                            <button @click="showWorkLogModal = true"
                                class="flex w-full items-center rounded-lg border border-green-200 bg-green-50 px-4 py-3 text-sm font-medium text-green-700 shadow-theme-xs hover:bg-green-100 hover:text-green-800 dark:border-green-800 dark:bg-green-900/20 dark:text-green-400 dark:hover:bg-green-900/30">
                                <i class="bi bi-journal-plus me-3"></i>
                                Add Work Log
                            </button>

                            <!-- Work Log Modal -->
                            <div x-show="showWorkLogModal" x-cloak style="display: none;"
                                class="fixed inset-0 z-50 flex items-center justify-center bg-black bg-opacity-50 mt-20">
                                <div
                                    class="bg-white dark:bg-gray-800 rounded-lg p-6 w-full max-w-2xl max-h-[90vh] overflow-y-auto">
                                    <div class="flex justify-between items-center mb-4">
                                        <h3 class="text-lg font-semibold text-gray-800 dark:text-white/90">
                                            <i class="bi bi-journal-text me-2"></i>Add Work Log
                                        </h3>
                                        <button @click="showWorkLogModal = false"
                                            class="text-gray-500 hover:text-gray-700 dark:text-gray-400 dark:hover:text-gray-200">
                                            <i class="bi bi-x-lg"></i>
                                        </button>
                                    </div>

                                    <form action="{{ route('work-logs.store') }}" method="POST" id="work-log-form">
                                        @csrf
                                        <input type="hidden" name="request_id" value="{{ $maintenanceRequest->id }}">

                                        <div class="space-y-4">
                                            <!-- Work Status -->
                                            <div>
                                                <label
                                                    class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                                                    Update Request Status *
                                                </label>
                                                <div class="grid grid-cols-1 md:grid-cols-3 gap-3">
                                                    <!-- In Progress -->
                                                    <label
                                                        class="relative flex cursor-pointer rounded-lg border border-gray-200 p-4 hover:border-blue-300 dark:border-gray-700">
                                                        <input type="radio" name="new_status" value="in_progress"
                                                            required
                                                            {{ $maintenanceRequest->status === 'in_progress' ? 'checked' : '' }}
                                                            class="peer sr-only">
                                                        <div class="flex-1">
                                                            <div class="flex items-center">
                                                                <div
                                                                    class="mr-3 flex h-6 w-6 items-center justify-center rounded-full border border-gray-300 peer-checked:border-blue-500 peer-checked:bg-blue-500 dark:border-gray-600">
                                                                    <div
                                                                        class="h-2 w-2 rounded-full bg-white opacity-0 peer-checked:opacity-100">
                                                                    </div>
                                                                </div>
                                                                <div>
                                                                    <div
                                                                        class="text-sm font-medium text-gray-800 dark:text-white/90">
                                                                        <i class="bi bi-tools me-1"></i> In Progress
                                                                    </div>
                                                                    <div class="text-xs text-gray-500 dark:text-gray-400">
                                                                        Still working on it
                                                                    </div>
                                                                </div>
                                                            </div>
                                                        </div>
                                                    </label>

                                                    <!-- Completed -->
                                                    <label
                                                        class="relative flex cursor-pointer rounded-lg border border-gray-200 p-4 hover:border-green-300 dark:border-gray-700">
                                                        <input type="radio" name="new_status" value="completed"
                                                            required
                                                            {{ $maintenanceRequest->status === 'completed' ? 'checked' : '' }}
                                                            class="peer sr-only">
                                                        <div class="flex-1">
                                                            <div class="flex items-center">
                                                                <div
                                                                    class="mr-3 flex h-6 w-6 items-center justify-center rounded-full border border-gray-300 peer-checked:border-green-500 peer-checked:bg-green-500 dark:border-gray-600">
                                                                    <div
                                                                        class="h-2 w-2 rounded-full bg-white opacity-0 peer-checked:opacity-100">
                                                                    </div>
                                                                </div>
                                                                <div>
                                                                    <div
                                                                        class="text-sm font-medium text-gray-800 dark:text-white/90">
                                                                        <i class="bi bi-check-circle me-1"></i> Completed
                                                                    </div>
                                                                    <div class="text-xs text-gray-500 dark:text-gray-400">
                                                                        Issue is fixed
                                                                    </div>
                                                                </div>
                                                            </div>
                                                        </div>
                                                    </label>

                                                    <!-- Not Fixed -->
                                                    <label
                                                        class="relative flex cursor-pointer rounded-lg border border-gray-200 p-4 hover:border-red-300 dark:border-gray-700">
                                                        <input type="radio" name="new_status" value="not_fixed"
                                                            required
                                                            {{ $maintenanceRequest->status === 'not_fixed' ? 'checked' : '' }}
                                                            class="peer sr-only">
                                                        <div class="flex-1">
                                                            <div class="flex items-center">
                                                                <div
                                                                    class="mr-3 flex h-6 w-6 items-center justify-center rounded-full border border-gray-300 peer-checked:border-red-500 peer-checked:bg-red-500 dark:border-gray-600">
                                                                    <div
                                                                        class="h-2 w-2 rounded-full bg-white opacity-0 peer-checked:opacity-100">
                                                                    </div>
                                                                </div>
                                                                <div>
                                                                    <div
                                                                        class="text-sm font-medium text-gray-800 dark:text-white/90">
                                                                        <i class="bi bi-x-circle me-1"></i> Not Fixed
                                                                    </div>
                                                                    <div class="text-xs text-gray-500 dark:text-gray-400">
                                                                        Could not fix
                                                                    </div>
                                                                </div>
                                                            </div>
                                                        </div>
                                                    </label>
                                                </div>
                                            </div>

                                            <!-- Work Summary -->
                                            <div>
                                                <label
                                                    class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                                                    Work Done * <span class="text-xs text-gray-500">(Describe the work
                                                        performed)</span>
                                                </label>
                                                <textarea name="work_done" id="work_done" rows="4" required
                                                    class="w-full rounded-lg border border-gray-300 bg-transparent px-4 py-2.5 text-sm text-gray-800 shadow-theme-xs placeholder:text-gray-400 focus:border-blue-300 focus:outline-none focus:ring-2 focus:ring-blue-500/10 dark:border-gray-700 dark:bg-gray-900 dark:text-white/90 dark:placeholder:text-white/30 dark:focus:border-blue-800"
                                                    placeholder="Describe the work you performed in detail..."></textarea>
                                                <div class="mt-1 text-xs text-gray-500 dark:text-gray-400">
                                                    Please be specific about what was done, steps taken, and results.
                                                </div>
                                            </div>

                                            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                                                <!-- Materials Used -->
                                                <div>
                                                    <label
                                                        class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                                                        Materials Used (Optional)
                                                    </label>
                                                    <textarea name="materials_used" id="materials_used" rows="3"
                                                        class="w-full rounded-lg border border-gray-300 bg-transparent px-4 py-2.5 text-sm text-gray-800 shadow-theme-xs placeholder:text-gray-400 focus:border-blue-300 focus:outline-none focus:ring-2 focus:ring-blue-500/10 dark:border-gray-700 dark:bg-gray-900 dark:text-white/90 dark:placeholder:text-white/30 dark:focus:border-blue-800"
                                                        placeholder="List any materials, parts, or tools used..."></textarea>
                                                </div>

                                                <!-- Completion Notes -->
                                                <div>
                                                    <label
                                                        class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                                                        Additional Notes (Optional)
                                                    </label>
                                                    <textarea name="completion_notes" id="completion_notes" rows="3"
                                                        class="w-full rounded-lg border border-gray-300 bg-transparent px-4 py-2.5 text-sm text-gray-800 shadow-theme-xs placeholder:text-gray-400 focus:border-blue-300 focus:outline-none focus:ring-2 focus:ring-blue-500/10 dark:border-gray-700 dark:bg-gray-900 dark:text-white/90 dark:placeholder:text-white/30 dark:focus:border-blue-800"
                                                        placeholder="Any additional observations, challenges, or recommendations..."></textarea>
                                                </div>
                                            </div>

                                            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                                                <!-- Time Spent -->
                                                <div>
                                                    <label
                                                        class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                                                        Time Spent (minutes) *
                                                    </label>
                                                    <div class="relative">
                                                        <input type="number" name="time_spent_minutes"
                                                            id="time_spent_minutes" required min="1"
                                                            max="480" step="15"
                                                            class="w-full rounded-lg border border-gray-300 bg-transparent px-4 py-2.5 text-sm text-gray-800 shadow-theme-xs focus:border-blue-300 focus:outline-none focus:ring-2 focus:ring-blue-500/10 dark:border-gray-700 dark:bg-gray-900 dark:text-white/90 dark:focus:border-blue-800"
                                                            placeholder="e.g., 60 for 1 hour">
                                                        <div
                                                            class="absolute right-3 top-1/2 transform -translate-y-1/2 text-gray-500">
                                                            minutes
                                                        </div>
                                                    </div>
                                                    <div class="mt-1 flex flex-wrap gap-1">
                                                        <span class="text-xs text-gray-500 dark:text-gray-400">Quick
                                                            select:</span>
                                                        <button type="button"
                                                            onclick="document.getElementById('time_spent_minutes').value=30"
                                                            class="text-xs px-2 py-1 bg-gray-100 rounded hover:bg-gray-200 dark:bg-gray-700 dark:hover:bg-gray-600">30m</button>
                                                        <button type="button"
                                                            onclick="document.getElementById('time_spent_minutes').value=60"
                                                            class="text-xs px-2 py-1 bg-gray-100 rounded hover:bg-gray-200 dark:bg-gray-700 dark:hover:bg-gray-600">1h</button>
                                                        <button type="button"
                                                            onclick="document.getElementById('time_spent_minutes').value=120"
                                                            class="text-xs px-2 py-1 bg-gray-100 rounded hover:bg-gray-200 dark:bg-gray-700 dark:hover:bg-gray-600">2h</button>
                                                        <button type="button"
                                                            onclick="document.getElementById('time_spent_minutes').value=240"
                                                            class="text-xs px-2 py-1 bg-gray-100 rounded hover:bg-gray-200 dark:bg-gray-700 dark:hover:bg-gray-600">4h</button>
                                                    </div>
                                                </div>

                                                <!-- Work Date -->
                                                <div>
                                                    <label
                                                        class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                                                        Date of Work *
                                                    </label>
                                                    <input type="date" name="log_date" id="log_date" required
                                                        class="w-full rounded-lg border border-gray-300 bg-transparent px-4 py-2.5 text-sm text-gray-800 shadow-theme-xs focus:border-blue-300 focus:outline-none focus:ring-2 focus:ring-blue-500/10 dark:border-gray-700 dark:bg-gray-900 dark:text-white/90 dark:focus:border-blue-800"
                                                        value="{{ date('Y-m-d') }}">
                                                </div>
                                            </div>

                                            <!-- Status Explanation -->
                                            <div class="mt-4 p-3 bg-blue-50 dark:bg-blue-900/20 rounded-lg">
                                                <div class="flex items-start">
                                                    <i
                                                        class="bi bi-info-circle text-blue-600 dark:text-blue-400 mt-0.5 mr-2"></i>
                                                    <div class="text-sm">
                                                        <p class="font-medium text-blue-800 dark:text-blue-300 mb-1">Status
                                                            Explanation</p>
                                                        <ul
                                                            class="list-disc list-inside text-blue-700 dark:text-blue-400 space-y-1">
                                                            <li><strong>In Progress:</strong> Still working on the issue
                                                            </li>
                                                            <li><strong>Completed:</strong> Issue is fixed and working</li>
                                                            <li><strong>Not Fixed:</strong> Tried but unable to fix</li>
                                                        </ul>
                                                    </div>
                                                </div>
                                            </div>

                                            <!-- Form Actions -->
                                            <div
                                                class="flex justify-end gap-3 pt-4 border-t border-gray-200 dark:border-gray-700">
                                                <button type="button" @click="showWorkLogModal = false"
                                                    class="rounded-lg border border-gray-300 bg-white px-4 py-2.5 text-sm font-medium text-gray-700 shadow-theme-xs hover:bg-gray-50 dark:border-gray-700 dark:bg-gray-800 dark:text-gray-400 dark:hover:bg-white/[0.03]">
                                                    Cancel
                                                </button>
                                                <button type="submit"
                                                    class="rounded-lg bg-green-500 px-4 py-2.5 text-sm font-medium text-white shadow-theme-xs hover:bg-green-600 focus:outline-none focus:ring-2 focus:ring-green-500 focus:ring-offset-2 dark:focus:ring-offset-gray-900">
                                                    <i class="bi bi-save me-2"></i> Save Work Log & Update Status
                                                </button>
                                            </div>
                                        </div>
                                    </form>
                                </div>
                            </div>
                        </div>
                    @endif

                    <!-- Work Logs Section -->
                    @if ($maintenanceRequest->workLogs->count() > 0 || auth()->user()->id == $maintenanceRequest->assigned_to)
                        <div x-data="{ workLogs: {{ $maintenanceRequest->workLogs->toJson() }}, totalTime: '{{ $maintenanceRequest->getTotalWorkTimeFormatted() }}' }" x-init="loadWorkLogs()">

                            <div class="flex justify-between items-center mb-4">
                                <h4 class="text-sm font-semibold text-gray-800 dark:text-white/90">
                                    <i class="bi bi-journal-text me-2"></i>Work Logs
                                    <span x-show="workLogs.length > 0" class="text-xs text-gray-500 ml-2">
                                        (<span x-text="workLogs.length"></span> entries)
                                    </span>
                                </h4>

                                <!-- Add Work Log Button moved to Quick Actions section -->
                            </div>

                            <div class="space-y-4" id="work-logs-container">
                                <template x-for="log in workLogs" :key="log.id">
                                    <div class="rounded-lg border border-gray-200 p-4 dark:border-gray-700">
                                        <div class="flex justify-between items-start mb-2">
                                            <div>
                                                <div class="font-medium text-gray-800 dark:text-white/90"
                                                    x-text="log.technician ? log.technician.full_name : 'Unknown Technician'">
                                                </div>
                                                <div class="text-sm text-gray-500 dark:text-gray-400">
                                                    <span x-text="log.log_date_formatted"></span> at
                                                    <span x-text="log.log_time_formatted"></span>
                                                </div>
                                            </div>
                                            <div class="flex items-center gap-2">
                                                <span
                                                    class="rounded-full bg-blue-100 px-3 py-1 text-xs font-medium text-blue-800 dark:bg-blue-900/30 dark:text-blue-300"
                                                    x-text="log.time_spent_formatted"></span>
                                                @if (auth()->user()->id == $maintenanceRequest->assigned_to)
                                                    <button @click="deleteWorkLog(log.id)"
                                                        class="text-red-500 hover:text-red-700" title="Delete work log">
                                                        <i class="bi bi-trash text-xs"></i>
                                                    </button>
                                                @endif
                                            </div>
                                        </div>

                                        <div class="mt-3">
                                            <div class="text-sm font-medium text-gray-700 dark:text-gray-300">Work Done:
                                            </div>
                                            <p class="mt-1 text-sm text-gray-800 dark:text-white/90 whitespace-pre-line"
                                                x-text="log.work_done"></p>
                                        </div>

                                        <div x-show="log.materials_used" class="mt-3">
                                            <div class="text-sm font-medium text-gray-700 dark:text-gray-300">Materials
                                                Used:</div>
                                            <p class="mt-1 text-sm text-gray-800 dark:text-white/90"
                                                x-text="log.materials_used"></p>
                                        </div>

                                        <div x-show="log.completion_notes" class="mt-3">
                                            <div class="text-sm font-medium text-gray-700 dark:text-gray-300">Notes:</div>
                                            <p class="mt-1 text-sm text-gray-800 dark:text-white/90"
                                                x-text="log.completion_notes"></p>
                                        </div>
                                    </div>
                                </template>

                                <!-- Empty state -->
                                <div x-show="workLogs.length === 0"
                                    class="text-center py-8 text-gray-500 dark:text-gray-400">
                                    <i class="bi bi-journal-x text-2xl mb-2"></i>
                                    <p>No work logs yet. Add your first work log!</p>
                                </div>

                                <!-- Total Work Time -->
                                <div x-show="workLogs.length > 0" class="rounded-lg bg-gray-50 p-4 dark:bg-gray-800">
                                    <div class="flex justify-between items-center">
                                        <div class="text-sm font-medium text-gray-700 dark:text-gray-300">
                                            Total Work Time:
                                        </div>
                                        <div class="text-lg font-bold text-gray-800 dark:text-white/90"
                                            id="total-work-time" x-text="totalTime"></div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    @endif



                    <!-- Similar Requests (only for users with maintenance_requests.view permission) -->
                    @if ($similarRequests->count() > 0 && auth()->user()->can('maintenance_requests.assign'))
                        <div
                            class="rounded-2xl border border-gray-200 bg-white p-6 dark:border-gray-800 dark:bg-white/[0.03]">
                            <h3 class="mb-4 text-sm font-semibold text-gray-800 dark:text-white/90">
                                <i class="bi bi-diagram-3 me-2"></i>Similar Requests
                            </h3>

                            <div class="space-y-3">
                                @foreach ($similarRequests as $similarRequest)
                                    <a href="{{ route('maintenance-requests.show', $similarRequest) }}"
                                        class="block rounded-lg border border-gray-200 p-3 hover:border-blue-300 hover:bg-blue-50 dark:border-gray-700 dark:hover:border-blue-800 dark:hover:bg-blue-900/20">
                                        <div class="flex items-center justify-between">
                                            <div>
                                                <div class="font-medium text-gray-800 dark:text-white/90">
                                                    {{ $similarRequest->ticket_number }}</div>
                                                <div class="text-sm text-gray-600 dark:text-gray-400">
                                                    {{ $similarRequest->getRequestedDate() }}
                                                </div>
                                            </div>
                                            <span
                                                class="rounded-full px-2 py-0.5 text-xs font-medium {{ $similarRequest->getStatusBadgeClass() }}">
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
            @if (session('success'))
                <div class="fixed bottom-4 right-4 z-50">
                    <x-ui.alert variant="success" title="Success" :message="session('success')" />
                </div>
            @endif
        @endsection
        @push('scripts')
            <script>
                document.addEventListener('DOMContentLoaded', function() {
                    // Handle assign technician form submission
                    const assignForm = document.getElementById('assign-technician-form');
                    if (assignForm) {
                        assignForm.addEventListener('submit', function(e) {
                            e.preventDefault();

                            const formData = new FormData(this);

                            fetch(this.action, {
                                    method: 'POST',
                                    headers: {
                                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')
                                            .getAttribute('content'),
                                        'X-HTTP-Method-Override': 'PUT'
                                    },
                                    body: formData
                                })
                                .then(response => response.text())
                                .then(html => {
                                    // Close the modal
                                    const modal = document.querySelector('[x-data] [x-model="open"]');
                                    if (modal) {
                                        modal.setAttribute('x-data', '{ open: false }');
                                    }

                                    // Reload the page to see changes
                                    window.location.reload();
                                })
                                .catch(error => {
                                    console.error('Error:', error);
                                    alert('An error occurred while assigning technician.');
                                });
                        });
                    }
                });
                document.addEventListener('DOMContentLoaded', function() {
                    // File preview for approval form
                    const fileInput = document.querySelector('input[name="attachments[]"]');
                    const filePreview = document.getElementById('file-preview');
                    const fileList = document.getElementById('file-list');

                    if (fileInput) {
                        fileInput.addEventListener('change', function(e) {
                            const files = Array.from(this.files);

                            if (files.length > 0) {
                                filePreview.classList.remove('hidden');
                                fileList.innerHTML = '';

                                files.forEach((file, index) => {
                                    const fileSize = formatFileSize(file.size);
                                    const fileItem = document.createElement('div');
                                    fileItem.className =
                                        'flex items-center justify-between text-xs bg-gray-50 dark:bg-gray-800 rounded p-2';
                                    fileItem.innerHTML = `
                            <div class="flex items-center">
                                <i class="bi ${getFileIcon(file.type)} me-2 text-gray-500"></i>
                                <span class="text-gray-700 dark:text-gray-300">${file.name}</span>
                            </div>
                            <div class="flex items-center">
                                <span class="text-gray-500 me-2">${fileSize}</span>
                                <button type="button" onclick="removeFile(${index})" class="text-red-500 hover:text-red-700">
                                    <i class="bi bi-x"></i>
                                </button>
                            </div>
                        `;
                                    fileList.appendChild(fileItem);
                                });
                            } else {
                                filePreview.classList.add('hidden');
                            }
                        });
                    }

                    // Format file size
                    function formatFileSize(bytes) {
                        if (bytes === 0) return '0 Bytes';
                        const k = 1024;
                        const sizes = ['Bytes', 'KB', 'MB', 'GB'];
                        const i = Math.floor(Math.log(bytes) / Math.log(k));
                        return parseFloat((bytes / Math.pow(k, i)).toFixed(2)) + ' ' + sizes[i];
                    }

                    // Get file icon based on type
                    function getFileIcon(mimeType) {
                        if (mimeType.includes('pdf')) return 'bi-file-earmark-pdf';
                        if (mimeType.includes('word') || mimeType.includes('doc')) return 'bi-file-earmark-word';
                        if (mimeType.includes('excel') || mimeType.includes('sheet')) return 'bi-file-earmark-excel';
                        if (mimeType.includes('image')) return 'bi-file-earmark-image';
                        return 'bi-file-earmark';
                    }
                });

                // Remove file from input
                function removeFile(index) {
                    const fileInput = document.querySelector('input[name="attachments[]"]');
                    const dt = new DataTransfer();
                    const files = Array.from(fileInput.files);

                    files.forEach((file, i) => {
                        if (i !== index) {
                            dt.items.add(file);
                        }
                    });

                    fileInput.files = dt.files;
                    fileInput.dispatchEvent(new Event('change'));
                }
            </script>
            <script>
                document.addEventListener('DOMContentLoaded', function() {
                    // Work Log Form Submission
                    const workLogForm = document.getElementById('work-log-form');
                    if (workLogForm) {
                        workLogForm.addEventListener('submit', async function(e) {
                            e.preventDefault();

                            // Disable submit button
                            const submitBtn = this.querySelector('button[type="submit"]');
                            const originalText = submitBtn.innerHTML;
                            submitBtn.innerHTML = '<i class="bi bi-hourglass-split me-2"></i>Saving...';
                            submitBtn.disabled = true;

                            try {
                                const formData = new FormData(this);

                                const response = await fetch(this.action, {
                                    method: 'POST',
                                    headers: {
                                        'X-CSRF-TOKEN': document.querySelector(
                                            'meta[name="csrf-token"]').getAttribute('content'),
                                        'Accept': 'application/json'
                                    },
                                    body: formData
                                });

                                const result = await response.json();

                                if (result.success) {
                                    // Close modal
                                    const alpineElement = this.closest('[x-data]');
                                    if (alpineElement && alpineElement.__x) {
                                        alpineElement.__x.$data.showWorkLogModal = false;
                                    }

                                    // Show success toast
                                    const toast = document.createElement('div');
                                    toast.className =
                                        'fixed top-4 right-4 z-50 px-4 py-3 rounded-lg shadow-lg bg-green-500 text-white';
                                    toast.innerHTML = `
                        <div class="flex items-center">
                            <i class="bi bi-check-circle me-2"></i>
                            <span>${result.message}</span>
                        </div>
                    `;
                                    document.body.appendChild(toast);

                                    // Remove toast after 3 seconds
                                    setTimeout(() => {
                                        toast.remove();
                                        // Reload page to show updated status
                                        window.location.reload();
                                    }, 3000);
                                } else {
                                    // Show error toast
                                    const toast = document.createElement('div');
                                    toast.className =
                                        'fixed top-4 right-4 z-50 px-4 py-3 rounded-lg shadow-lg bg-red-500 text-white';
                                    toast.innerHTML = `
                        <div class="flex items-center">
                            <i class="bi bi-exclamation-triangle me-2"></i>
                            <span>${result.message || 'Failed to save work log.'}</span>
                        </div>
                    `;
                                    document.body.appendChild(toast);

                                    setTimeout(() => toast.remove(), 5000);

                                    // Re-enable submit button on error
                                    submitBtn.innerHTML = originalText;
                                    submitBtn.disabled = false;
                                }
                            } catch (error) {
                                console.error('Error:', error);

                                // Show error toast
                                const toast = document.createElement('div');
                                toast.className =
                                    'fixed top-4 right-4 z-50 px-4 py-3 rounded-lg shadow-lg bg-red-500 text-white';
                                toast.innerHTML = `
                    <div class="flex items-center">
                        <i class="bi bi-exclamation-triangle me-2"></i>
                        <span>An error occurred. Please try again.</span>
                    </div>
                `;
                                document.body.appendChild(toast);

                                setTimeout(() => toast.remove(), 5000);

                                submitBtn.innerHTML = originalText;
                                submitBtn.disabled = false;
                            }
                        });
                    }
                });
            </script>
        @endpush
