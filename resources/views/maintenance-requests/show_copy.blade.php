@extends('layouts.app')

@section('content')
    <x-common.page-breadcrumb pageTitle="Maintenance Request Details" />
    @if (session('success'))
        <div id="alert-success"
            class="mb-6 flex items-center rounded-xl border border-green-200 bg-green-50 p-4 text-green-800 shadow-sm dark:border-green-900/30 dark:bg-green-900/20 dark:text-green-400">
            <i class="bi bi-check-circle-fill mr-3 text-xl"></i>
            <div class="text-sm font-bold">
                {{ session('success') }}
            </div>
            <button type="button" onclick="document.getElementById('alert-success').remove()"
                class="ml-auto text-green-600 hover:text-green-800">
                <i class="bi bi-x-lg"></i>
            </button>
        </div>
    @endif

    @if (session('error') || $errors->any())
        <div id="alert-error"
            class="mb-6 flex items-center rounded-xl border border-red-200 bg-red-50 p-4 text-red-800 shadow-sm dark:border-red-900/30 dark:bg-red-900/20 dark:text-red-400">
            <i class="bi bi-exclamation-triangle-fill mr-3 text-xl"></i>
            <div class="text-sm font-bold">
                {{ session('error') ?? 'Please correct the highlighted errors below.' }}
            </div>
            <button type="button" onclick="document.getElementById('alert-error').remove()"
                class="ml-auto text-red-600 hover:text-red-800">
                <i class="bi bi-x-lg"></i>
            </button>
        </div>
    @endif
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

                    <div
                        class="overflow-hidden rounded-lg border border-gray-200 bg-gray-50 p-4 dark:border-gray-700 dark:bg-gray-800">
                        <p class="break-words whitespace-pre-wrap text-gray-800 dark:text-white/90">
                            {{ $maintenanceRequest->description }}
                        </p>
                    </div>
                </div>

                <!-- Attached Files -->
                <!-- Attached Files -->
                @if ($maintenanceRequest->files->count() > 0)
                    <div class="mt-6">
                        <h4 class="mb-4 text-sm font-semibold text-gray-800 dark:text-white/90">
                            <i class="bi bi-paperclip me-2"></i>Attached Files
                        </h4>

                        <div class="grid grid-cols-1 gap-3 sm:grid-cols-2">
                            @foreach ($maintenanceRequest->files as $file)
                                @php
                                    $isImage = in_array(
                                        strtolower(pathinfo($file->original_name, PATHINFO_EXTENSION)),
                                        ['jpg', 'jpeg', 'png', 'gif', 'webp', 'svg'],
                                    );
                                    $isPdf = strtolower(pathinfo($file->original_name, PATHINFO_EXTENSION)) === 'pdf';
                                    $canPreview = $isImage || $isPdf;
                                @endphp

                                <div
                                    class="group relative flex items-center justify-between rounded-lg border border-gray-200 p-3 hover:bg-gray-50 dark:border-gray-700 dark:hover:bg-gray-800/50">
                                    <div class="flex items-center overflow-hidden">
                                        @if ($isImage)
                                            <div class="relative h-10 w-10 flex-shrink-0 overflow-hidden rounded-md">
                                                <img src="{{ Storage::url($file->path) }}"
                                                    alt="{{ $file->original_name }}" class="h-full w-full object-cover"
                                                    loading="lazy">
                                            </div>
                                        @elseif ($isPdf)
                                            <div
                                                class="flex h-10 w-10 flex-shrink-0 items-center justify-center rounded-md bg-red-100 dark:bg-red-900/30">
                                                <i
                                                    class="bi bi-file-earmark-pdf-fill text-lg text-red-600 dark:text-red-400"></i>
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
                                                method="POST"
                                                onsubmit="return confirm('Are you sure you want to delete this file?')"
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
                    </div>
                @endif

                <!-- Preview Modal (Place this at the bottom of your blade template, before closing body tag) -->
                <div id="previewModal" class="fixed inset-0 z-[9999] hidden overflow-y-auto bg-black/75 p-4">
                    <div class="relative mx-auto flex min-h-screen max-w-6xl items-center justify-center">
                        <div class="relative w-full rounded-xl bg-white shadow-2xl dark:bg-gray-900">
                            <!-- Modal Header -->
                            <div
                                class="flex items-center justify-between border-b border-gray-200 px-6 py-4 dark:border-gray-700">
                                <div class="flex items-center gap-3">
                                    <div id="fileTypeIcon" class="text-xl">
                                        <!-- Icon will be set dynamically -->
                                    </div>
                                    <div>
                                        <h3 id="previewTitle"
                                            class="text-lg font-semibold text-gray-800 dark:text-white/90"></h3>
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
                                            <div
                                                class="h-12 w-12 animate-spin rounded-full border-4 border-blue-200 border-t-blue-600">
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



                <!-- Technician Notes -->
                @if ($maintenanceRequest->technician_notes || $maintenanceRequest->resolution_notes)
                    <div class="mt-6">
                        <h4 class="mb-4 text-sm font-semibold text-gray-800 dark:text-white/90">
                            <i class="bi bi-journal-text me-2"></i>Assignment Remark
                        </h4>

                        <div class="space-y-4">
                            @if ($maintenanceRequest->technician_notes)
                                <div>
                                    <div class="text-xs text-gray-500 dark:text-gray-400">Remarks</div>
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
                                    <div class="text-sm font-medium text-gray-800 dark:text-white/90">Assigned to
                                        Technician
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
                        @if ($maintenanceRequest->assigned_to === null || $maintenanceRequest->status === 'not_fixed')
                            <div x-data="{ showAssignModal: false }">
                                <button @click="showAssignModal = true"
                                    class="flex w-full items-center rounded-lg border border-gray-200 bg-white px-4 py-3 text-sm font-medium text-gray-700 shadow-theme-xs hover:bg-gray-50 dark:border-gray-700 dark:bg-gray-800 dark:text-gray-400 dark:hover:bg-white/[0.03]">
                                    <i class="bi bi-person-plus me-3"></i>
                                    {{ is_null($maintenanceRequest->assigned_to) ? 'Assign to Technician' : 'Re-Assign Technician' }}
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
                                                        Only users with 'maintenance_requests.resolve' permission are listed
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
                    @if ($maintenanceRequest->status === 'confirmed' && auth()->id() === $maintenanceRequest->assigned_to)
                        <a href="{{ route('maintenance.report', $maintenanceRequest) }}"
                            class="flex w-full items-center rounded-lg border border-gray-200 bg-white px-4 py-3 text-sm font-medium text-gray-700 shadow-theme-xs hover:bg-gray-50 dark:border-gray-700 dark:bg-gray-800 dark:text-gray-400 dark:hover:bg-white/[0.03]">
                            <i class="bi bi-file-earmark-pdf"></i> Download Report
                        </a>
                    @endif

                    <!-- Update Status (only for users with reports.update-status permission) -->
                    @can('maintenance_requests.view_assigned' && 'maintenance_requests.update')
                        @if (in_array($maintenanceRequest->status, ['assigned', 'in_progress', 'not_fixed']))
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
                    @if (auth()->id() == $maintenanceRequest->user_id)
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
                                                                You can upload multiple files (PDF, Word, Excel,
                                                                Images).
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


                            </div>
                        @endif
                    @endif
                    @if (auth()->user()->can('maintenance_requests.approve' && 'maintenance_requests.reject'))
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

                                    <form action="{{ route('approvals.reject', $maintenanceRequest) }}" method="POST"
                                        id="reject-request-form">
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
                            in_array($maintenanceRequest->status, ['assigned', 'in_progress', 'approved', 'pending', 'not_fixed']))
                        <div x-data="{
                            showWorkLogModal: false,
                            hours: 0,
                            minutes: 0
                        }" @keydown.escape.window="showWorkLogModal = false">

                            <button @click="showWorkLogModal = true"
                                class="flex w-full items-center justify-center rounded-xl border border-green-200 bg-green-50 px-4 py-3 text-sm font-semibold text-green-700 shadow-sm transition-all hover:bg-green-100 dark:border-green-800/30 dark:bg-green-500/10 dark:text-green-400">
                                <i class="bi bi-journal-plus me-2 text-lg"></i>
                                Start Your Task
                            </button>

                            <div x-show="showWorkLogModal" x-cloak class="fixed inset-0 z-[999] overflow-y-auto"
                                aria-labelledby="modal-title" role="dialog" aria-modal="true">

                                <div x-show="showWorkLogModal" x-transition:enter="transition ease-out duration-300"
                                    x-transition:enter-start="opacity-0" x-transition:enter-end="opacity-100"
                                    x-transition:leave="transition ease-in duration-200" @click="showWorkLogModal = false"
                                    class="fixed inset-0 bg-gray-900/60 backdrop-blur-sm"></div>

                                <div class="flex min-h-full items-center justify-center p-4 text-center sm:p-0">
                                    <div x-show="showWorkLogModal" x-transition:enter="transition ease-out duration-300"
                                        x-transition:enter-start="opacity-0 translate-y-4 sm:translate-y-0 sm:scale-95"
                                        x-transition:enter-end="opacity-100 translate-y-0 sm:scale-100"
                                        class="relative w-full max-w-2xl transform overflow-hidden rounded-2xl bg-white text-left shadow-2xl transition-all dark:bg-gray-900">

                                        <div
                                            class="flex items-center justify-between border-b border-gray-100 px-6 py-4 dark:border-gray-800 mt-15">
                                            <h3 class="text-lg font-bold text-gray-900 dark:text-white">
                                                <i class="bi bi-file-earmark-medical me-2 text-blue-500"></i>Submit
                                                Work
                                                Progress
                                            </h3>
                                            <button @click="showWorkLogModal = false"
                                                class="text-gray-400 hover:text-gray-600 dark:hover:text-gray-200">
                                                <i class="bi bi-x-lg"></i>
                                            </button>
                                        </div>

                                        <form id="work-log-form" action="{{ route('work-logs.store') }}" method="POST"
                                            class="max-h-[80vh] overflow-y-auto p-6">
                                            @csrf
                                            <input type="hidden" name="request_id"
                                                value="{{ $maintenanceRequest->id }}">

                                            <div class="space-y-6">
                                                <div>
                                                    <label
                                                        class="mb-3 block text-sm font-semibold text-gray-700 dark:text-gray-300">Updated
                                                        Request Status *</label>
                                                    <div class="grid grid-cols-1 gap-3 sm:grid-cols-3">
                                                        @foreach (['in_progress' => ['blue', 'tools', 'In Progress'], 'completed' => ['green', 'check-circle', 'Completed'], 'not_fixed' => ['red', 'x-circle', 'Not Fixed']] as $val => $meta)
                                                            <label class="relative flex cursor-pointer group">
                                                                <input type="radio" name="new_status"
                                                                    value="{{ $val }}" class="peer sr-only"
                                                                    {{ $maintenanceRequest->status === $val ? 'checked' : '' }}
                                                                    required>
                                                                <div
                                                                    class="flex w-full flex-col items-center rounded-xl border border-gray-200 p-3 transition-all peer-checked:border-{{ $meta[0] }}-500 peer-checked:bg-{{ $meta[0] }}-50/50 dark:border-gray-800 dark:peer-checked:bg-{{ $meta[0] }}-500/10">
                                                                    <i
                                                                        class="bi bi-{{ $meta[1] }} mb-1 text-{{ $meta[0] }}-500 text-xl"></i>
                                                                    <span
                                                                        class="text-xs font-bold text-gray-700 dark:text-gray-300 uppercase tracking-tight">{{ $meta[2] }}</span>
                                                                </div>
                                                            </label>
                                                        @endforeach
                                                    </div>
                                                </div>

                                                <div>
                                                    <label
                                                        class="mb-2 block text-sm font-semibold text-gray-700 dark:text-gray-300">Detailed
                                                        Work Description *</label>
                                                    <textarea name="work_done" rows="3" required
                                                        class="w-full rounded-xl border border-gray-200 bg-gray-50/50 px-4 py-3 text-sm focus:border-blue-500 focus:ring-4 focus:ring-blue-500/10 dark:border-gray-800 dark:bg-gray-800/50 dark:text-white"
                                                        placeholder="Provide a professional summary of tasks completed..."></textarea>
                                                </div>

                                                <div class="grid grid-cols-1 gap-4 md:grid-cols-2">
                                                    <div>
                                                        <label
                                                            class="mb-2 block text-sm font-semibold text-gray-700 dark:text-gray-300">Materials
                                                            Used</label>
                                                        <textarea name="materials_used" rows="2"
                                                            class="w-full rounded-xl border border-gray-200 bg-gray-50/50 px-4 py-3 text-sm dark:border-gray-800 dark:bg-gray-800/50 dark:text-white"
                                                            placeholder="Parts or tools used..."></textarea>
                                                    </div>
                                                    <div>
                                                        <label
                                                            class="mb-2 block text-sm font-semibold text-gray-700 dark:text-gray-300">Additional
                                                            Notes</label>
                                                        <textarea name="completion_notes" rows="2"
                                                            class="w-full rounded-xl border border-gray-200 bg-gray-50/50 px-4 py-3 text-sm dark:border-gray-800 dark:bg-gray-800/50 dark:text-white"
                                                            placeholder="Challenges or observations..."></textarea>
                                                    </div>
                                                </div>

                                                <div class="grid grid-cols-1 gap-4 md:grid-cols-2">
                                                    <div
                                                        class="rounded-xl border border-gray-100 bg-gray-50/30 p-4 dark:border-gray-800 dark:bg-gray-800/30">
                                                        <label
                                                            class="mb-2 block text-sm font-semibold text-gray-700 dark:text-gray-300">Time
                                                            Spent</label>
                                                        <div class="flex items-center gap-2">
                                                            <input type="number" min="0" x-model.number="hours"
                                                                class="w-20 rounded-lg border-gray-200 text-center dark:bg-gray-900 dark:text-white"
                                                                placeholder="Hrs">
                                                            <span class="font-bold">:</span>
                                                            <select x-model.number="minutes"
                                                                class="flex-1 rounded-lg border-gray-200 dark:bg-gray-900 dark:text-white">
                                                                <option value="0">00 mins</option>
                                                                <option value="15">15 mins</option>
                                                                <option value="30">30 mins</option>
                                                                <option value="45">45 mins</option>
                                                            </select>
                                                        </div>
                                                        <input type="hidden" name="time_spent_minutes"
                                                            :value="(hours * 60) + minutes">
                                                    </div>

                                                    <div
                                                        class="rounded-xl border border-gray-100 bg-gray-50/30 p-4 dark:border-gray-800 dark:bg-gray-800/30">
                                                        <label
                                                            class="mb-2 block text-sm font-semibold text-gray-700 dark:text-gray-300">Completion
                                                            Date</label>
                                                        <input type="date" name="log_date" required
                                                            value="{{ date('Y-m-d') }}"
                                                            class="w-full rounded-lg border-gray-200 py-2 dark:bg-gray-900 dark:text-white">
                                                    </div>
                                                </div>
                                            </div>

                                            <div
                                                class="mt-8 flex items-center justify-end gap-3 border-t border-gray-100 pt-6 dark:border-gray-800">
                                                <button type="button" @click="showWorkLogModal = false"
                                                    class="text-sm font-medium text-gray-500 hover:text-gray-700">
                                                    Cancel
                                                </button>
                                                <button type="submit"
                                                    class="flex items-center rounded-xl bg-blue-600 px-6 py-2.5 text-sm font-bold text-white shadow-lg shadow-blue-500/20 transition-all hover:bg-blue-700 active:scale-95">
                                                    <i class="bi bi-cloud-arrow-up me-2"></i> Save Progress
                                                </button>
                                            </div>
                                        </form>
                                    </div>
                                </div>
                            </div>
                        </div>
                    @endif
                    <!-- Rejection Modal (add this near other modals) -->
                    <div x-data="{ showRejectWorkLogModal: false, selectedWorkLogId: null, rejectionReason: '' }">
                        <!-- Modal -->
                        <div x-show="showRejectWorkLogModal" x-cloak style="display: none;"
                            class="fixed inset-0 z-50 flex items-center justify-center bg-black bg-opacity-50">
                            <div class="bg-white dark:bg-gray-800 rounded-lg p-6 w-full max-w-md">
                                <div class="flex justify-between items-center mb-4">
                                    <h3 class="text-lg font-semibold text-gray-800 dark:text-white/90">
                                        <i class="bi bi-x-circle me-2 text-red-500"></i>Reject Work Log
                                    </h3>
                                    <button @click="showRejectWorkLogModal = false"
                                        class="text-gray-500 hover:text-gray-700 dark:text-gray-400 dark:hover:text-gray-200">
                                        <i class="bi bi-x-lg"></i>
                                    </button>
                                </div>

                                <div class="space-y-4">
                                    <div>
                                        <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                                            Reason for Rejection <span class="text-red-500">*</span>
                                        </label>
                                        <textarea x-model="rejectionReason" rows="4" required
                                            class="w-full rounded-lg border border-gray-300 bg-transparent px-4 py-2.5 text-sm text-gray-800 shadow-theme-xs placeholder:text-gray-400 focus:border-blue-300 focus:outline-none focus:ring-2 focus:ring-blue-500/10 dark:border-gray-700 dark:bg-gray-900 dark:text-white/90 dark:placeholder:text-white/30 dark:focus:border-blue-800"
                                            placeholder="Please explain why you're rejecting this work log..."></textarea>
                                    </div>

                                    <div>
                                        <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                                            Additional Notes (Optional)
                                        </label>
                                        <textarea x-model="rejectionNotes" rows="2"
                                            class="w-full rounded-lg border border-gray-300 bg-transparent px-4 py-2.5 text-sm text-gray-800 shadow-theme-xs placeholder:text-gray-400 focus:border-blue-300 focus:outline-none focus:ring-2 focus:ring-blue-500/10 dark:border-gray-700 dark:bg-gray-900 dark:text-white/90 dark:placeholder:text-white/30 dark:focus:border-blue-800"
                                            placeholder="Any additional feedback for the technician..."></textarea>
                                    </div>

                                    <div class="flex justify-end gap-3">
                                        <button
                                            @click="showRejectWorkLogModal = false; rejectionReason = ''; rejectionNotes = ''"
                                            class="rounded-lg border border-gray-300 bg-white px-4 py-2.5 text-sm font-medium text-gray-700 shadow-theme-xs hover:bg-gray-50 dark:border-gray-700 dark:bg-gray-800 dark:text-gray-400 dark:hover:bg-white/[0.03]">
                                            Cancel
                                        </button>
                                        <button @click="submitWorkLogRejection()"
                                            class="rounded-lg bg-red-500 px-4 py-2.5 text-sm font-medium text-white shadow-theme-xs hover:bg-red-600 focus:outline-none focus:ring-2 focus:ring-red-500 focus:ring-offset-2 dark:focus:ring-offset-gray-900">
                                            Reject Work Log
                                        </button>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <!-- Work Logs Section -->
                    @if ($maintenanceRequest->workLogs->count() > 0)
                        @php
                            $isRequester = auth()->id() == $maintenanceRequest->user_id;
                            $isAssignedTechnician = auth()->user()->id == $maintenanceRequest->assigned_to;
                            $showWorkLogSection =
                                $isRequester || $isAssignedTechnician || $maintenanceRequest->workLogs->count() > 0;
                        @endphp

                        @if ($showWorkLogSection)
                            <div
                                class="rounded-2xl border border-gray-200 bg-white p-6 dark:border-gray-800 dark:bg-white/[0.03]">
                                <div class="flex justify-between items-center mb-4">
                                    <h4 class="text-sm font-semibold text-gray-800 dark:text-white/90">
                                        <i class="bi bi-journal-text me-2"></i>Technician Activities
                                        <span class="text-xs text-gray-500 ml-2">
                                            ({{ $maintenanceRequest->workLogs->count() }} entries)
                                        </span>
                                    </h4>
                                </div>

                                <div class="space-y-4">
                                    @foreach ($maintenanceRequest->workLogs as $log)
                                        <div
                                            class="rounded-lg border p-4 {{ $log->is_rejected ? 'border-red-300 bg-red-50 dark:bg-red-900/10' : 'border-gray-200 dark:bg-gray-800' }}">
                                            <div class="flex justify-between items-start mb-2">
                                                <div>
                                                    <div class="font-medium text-gray-800 dark:text-white/90">
                                                        {{ $log->technician?->full_name ?? 'Technician' }}
                                                    </div>
                                                    <div class="text-sm text-gray-500 dark:text-gray-400">
                                                        {{ $log->created_at->format('M d, Y') }} at
                                                        {{ $log->created_at->format('h:i A') }}
                                                        @if ($log->status === 'rejected')
                                                            <span
                                                                class="ml-2 rounded-full bg-red-100 px-2 py-0.5 text-xs font-medium text-red-800 dark:bg-red-900 dark:text-red-200">
                                                                <i class="bi bi-x-circle me-1"></i>Rejected
                                                            </span>
                                                        @endif
                                                        @if ($log->status === 'accepted')
                                                            <span
                                                                class="ml-2 rounded-full bg-green-100 px-2 py-0.5 text-xs font-medium text-green-800 dark:bg-green-900 dark:text-green-200">
                                                                <i class="bi bi-check-circle me-1"></i>Confirmed
                                                            </span>
                                                        @endif

                                                    </div>
                                                </div>

                                                <!-- Action Buttons  waiting_confirmation-->
                                                <div class="flex gap-2">
                                                    <!-- Reject Modal -->
                                                    @if (auth()->user()->id === $maintenanceRequest->user_id &&
                                                            in_array($maintenanceRequest->status, ['completed', 'waiting_confirmation']) &&
                                                            $log->status === 'pending')
                                                        <div x-data="{ showRejectModal: false, reason: '' }">
                                                            <button @click="showRejectModal = true"
                                                                class="flex items-center gap-1 text-red-500 hover:text-red-700 text-xs">
                                                                <i class="bi bi-x-circle"></i> Reject
                                                            </button>

                                                            <div x-show="showRejectModal" x-cloak style="display: none;"
                                                                class="fixed inset-0 z-50 flex items-center justify-center bg-black bg-opacity-50">
                                                                <div
                                                                    class="bg-white dark:bg-gray-800 rounded-lg p-6 w-full max-w-md">
                                                                    <div class="flex justify-between items-center mb-4">
                                                                        <h3
                                                                            class="text-lg font-semibold text-gray-800 dark:text-white/90">
                                                                            Reject Technician Activities
                                                                        </h3>
                                                                        <button @click="showRejectModal = false"
                                                                            class="text-gray-500 hover:text-gray-700 dark:text-gray-400 dark:hover:text-gray-200">
                                                                            <i class="bi bi-x-lg"></i>
                                                                        </button>
                                                                    </div>

                                                                    <form
                                                                        @submit.prevent="
                                                                            if(reason.length < 10) { alert('Rejection reason must be at least 10 characters.'); return; }
                                                                            fetch('/work-logs/{{ $log->id }}/reject', {
                                                                                method: 'POST',
                                                                                headers: {
                                                                                    'X-CSRF-TOKEN': document.querySelector('meta[name=csrf-token]').content,
                                                                                    'Content-Type': 'application/json',
                                                                                    'Accept': 'application/json'
                                                                                },
                                                                                body: JSON.stringify({ rejection_reason: reason })
                                                                            })
                                                                            .then(res => res.json())
                                                                            .then(result => { if(result.success) location.reload(); else alert(result.message); });
                                                                        ">

                                                                        <div class="space-y-4">
                                                                            <div>
                                                                                <label
                                                                                    class="mb-2 block text-sm font-medium text-gray-700 dark:text-gray-300">
                                                                                    Reason for Rejection <span
                                                                                        class="text-red-500">*</span>
                                                                                </label>
                                                                                <textarea x-model="reason" rows="4" required
                                                                                    class="w-full rounded-lg border border-gray-300 bg-transparent px-4 py-2.5 text-sm text-gray-800 dark:border-gray-700 dark:bg-gray-900 dark:text-white/90"
                                                                                    placeholder="Please provide a reason for rejecting this work log..."></textarea>
                                                                            </div>

                                                                            <div class="flex justify-end gap-3">
                                                                                <button type="button"
                                                                                    @click="showRejectModal = false"
                                                                                    class="rounded-lg border border-gray-300 bg-white px-4 py-2.5 text-sm font-medium text-gray-700 dark:border-gray-700 dark:bg-gray-800 dark:text-gray-400">
                                                                                    Cancel
                                                                                </button>
                                                                                <button type="submit"
                                                                                    class="rounded-lg bg-red-500 px-4 py-2.5 text-sm font-medium text-white hover:bg-red-600">
                                                                                    Submit Rejection
                                                                                </button>
                                                                            </div>
                                                                        </div>
                                                                    </form>
                                                                </div>
                                                            </div>
                                                        </div>
                                                    @endif

                                                    <!-- Confirm Modal -->
                                                    @if (auth()->user()->id === $maintenanceRequest->user_id &&
                                                            in_array($maintenanceRequest->status, ['completed', 'waiting_confirmation']) &&
                                                            $log->status === 'pending')
                                                        <div x-data="{ showConfirmModal: false }">
                                                            <button @click="showConfirmModal = true"
                                                                class="flex items-center gap-1 text-green-500 hover:text-green-700 text-xs">
                                                                <i class="bi bi-check-circle"></i> Confirm
                                                            </button>

                                                            <div x-show="showConfirmModal" x-cloak style="display: none;"
                                                                class="fixed inset-0 z-50 flex items-center justify-center bg-black bg-opacity-50">
                                                                <div
                                                                    class="bg-white dark:bg-gray-800 rounded-lg p-6 w-full max-w-md">
                                                                    <div class="flex justify-between items-center mb-4">
                                                                        <h3
                                                                            class="text-lg font-semibold text-gray-800 dark:text-white/90">
                                                                            Confirm Technician Activity
                                                                        </h3>
                                                                        <button @click="showConfirmModal = false"
                                                                            class="text-gray-500 hover:text-gray-700 dark:text-gray-400 dark:hover:text-gray-200">
                                                                            <i class="bi bi-x-lg"></i>
                                                                        </button>
                                                                    </div>

                                                                    <div
                                                                        class="text-sm text-gray-700 dark:text-gray-300 mb-4">
                                                                        Are you sure you want to confirm this
                                                                        Activities?
                                                                    </div>

                                                                    <div class="flex justify-end gap-3">
                                                                        <button type="button"
                                                                            @click="showConfirmModal = false"
                                                                            class="rounded-lg border border-gray-300 bg-white px-4 py-2.5 text-sm font-medium text-gray-700 dark:border-gray-700 dark:bg-gray-800 dark:text-gray-400">
                                                                            Cancel
                                                                        </button>
                                                                        <button
                                                                            @click="
                                                                                fetch('/work-logs/{{ $log->id }}/accept', {
                                                                                    method: 'POST',
                                                                                    headers: {
                                                                                        'X-CSRF-TOKEN': document.querySelector('meta[name=csrf-token]').content,
                                                                                        'Content-Type': 'application/json',
                                                                                        'Accept': 'application/json'
                                                                                    }
                                                                                })
                                                                                .then(res => res.json())
                                                                                .then(result => { if(result.success) location.reload(); else alert(result.message); });
                                                                            "
                                                                            class="rounded-lg bg-green-500 px-4 py-2.5 text-sm font-medium text-white hover:bg-green-600">
                                                                            Confirm
                                                                        </button>
                                                                    </div>
                                                                </div>
                                                            </div>
                                                        </div>
                                                    @endif
                                                </div>
                                            </div>

                                            <!-- Work Done -->
                                            <div class="mt-3">
                                                <div class="text-sm font-medium text-gray-700 dark:text-gray-300">Work
                                                    Done:</div>
                                                <p
                                                    class="mt-1 text-sm text-gray-800 dark:text-white/90 whitespace-pre-line">
                                                    {{ $log->work_done }}
                                                </p>
                                            </div>
                                        </div>
                                    @endforeach


                                    <!-- Total Work Time -->
                                    @if (
                                        $maintenanceRequest->workLogs->count() > 0 &&
                                            auth()->user()->can('maintenance_requests.assign' || 'maintenance_requests.resolve'))
                                        <div class="rounded-lg bg-gray-50 p-4 dark:bg-gray-800">
                                            <div class="flex justify-between items-center">
                                                <div class="text-sm font-medium text-gray-700 dark:text-gray-300">
                                                    Total Work Time:
                                                </div>
                                                <div class="text-lg font-bold text-gray-800 dark:text-white/90">
                                                    {{ $maintenanceRequest->getTotalWorkTimeFormatted() }}
                                                </div>
                                            </div>
                                        </div>
                                    @endif
                                </div>
                            </div>
                        @endif
                    @endif



                    <!-- Similar Requests (only for users with maintenance_requests.view permission) -->
                    @if (
                        $similarRequests->count() > 0 &&
                            auth()->user()->can('maintenance_requests.assign' || 'maintenance_requests.resolve'))
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
                document.addEventListener('DOMContentLoaded', () => {
                    const form = document.getElementById('assign-technician-form');

                    if (!form) return;

                    form.addEventListener('submit', async (e) => {
                        e.preventDefault();

                        const formData = new FormData(form);

                        try {
                            const response = await fetch(form.action, {
                                method: 'POST',
                                headers: {
                                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')
                                        .content,
                                    'X-HTTP-Method-Override': 'PUT',
                                    'Accept': 'application/json'
                                },
                                body: formData
                            });

                            if (!response.ok) {
                                const error = await response.json();
                                alert(error.message || 'Assignment failed');
                                return;
                            }

                            // Success → reload page
                            window.location.reload();

                        } catch (err) {
                            console.error(err);
                            alert('Something went wrong');
                        }
                    });
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
                // Add these functions to your JavaScript:

                window.rejectWorkLog = async function(workLogId) {
                    const reason = prompt('Why are you rejecting this work log? (min 10 chars)');
                    if (!reason || reason.length < 10) {
                        alert('Rejection reason must be at least 10 characters.');
                        return;
                    }

                    const response = await fetch(`/work-logs/${workLogId}/reject`, {
                        method: 'POST',
                        headers: {
                            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                            'Content-Type': 'application/json',
                            'Accept': 'application/json'
                        },
                        body: JSON.stringify({
                            rejection_reason: reason
                        })
                    });

                    const result = await response.json();

                    if (result.success) {
                        alert('✅ Work log rejected');
                        location.reload();
                    } else {
                        alert('❌ ' + result.message);
                    }
                };


                window.acceptWorkLog = async function(workLogId) {
                    if (!confirm('Are you sure you want to accept and confirm this work log?')) {
                        return;
                    }

                    try {
                        const response = await fetch(`/work-logs/${workLogId}/accept`, {
                            method: 'POST',
                            headers: {
                                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute(
                                    'content'),
                                'Accept': 'application/json'
                            }
                        });

                        const result = await response.json();

                        if (result.success) {
                            // Show success message
                            const toast = document.createElement('div');
                            toast.className =
                                'fixed top-4 right-4 z-50 px-4 py-3 rounded-lg shadow-lg bg-green-500 text-white';
                            toast.innerHTML = `
                <div class="flex items-center">
                    <i class="bi bi-check-circle-fill mr-2"></i>
                    <span>✅ Work accepted! Technician and ICT directors have been notified.</span>
                </div>
            `;
                            document.body.appendChild(toast);

                            // Reload page after 1.5 seconds
                            setTimeout(() => {
                                window.location.reload();
                            }, 1500);
                        } else {
                            alert('❌ ' + result.message);
                        }
                    } catch (error) {
                        console.error('Error:', error);
                        alert('❌ An error occurred. Please try again.');
                    }
                };
                window.deleteWorkLog = async function(workLogId) {
                    if (!confirm('Are you sure you want to delete this work log?')) {
                        return;
                    }

                    try {
                        const response = await fetch(`/work-logs/${workLogId}`, {
                            method: 'DELETE',
                            headers: {
                                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute(
                                    'content'),
                                'Accept': 'application/json'
                            }
                        });

                        const result = await response.json();

                        if (result.success) {
                            alert('✅ Work log deleted successfully.');
                            window.location.reload();
                        } else {
                            alert('❌ ' + result.message);
                        }
                    } catch (error) {
                        console.error('Error:', error);
                        alert('❌ An error occurred. Please try again.');
                    }
                };

                // Submit work log rejection
                window.submitWorkLogRejection = async function() {
                    const modalElement = document.querySelector('[x-data*="showRejectWorkLogModal"]');
                    if (!modalElement || !modalElement.__x) return;

                    const data = modalElement.__x.$data;

                    if (!data.rejectionReason.trim()) {
                        alert('Please provide a reason for rejection.');
                        return;
                    }

                    try {
                        const response = await fetch(`/work-logs/${data.selectedWorkLogId}/reject`, {
                            method: 'POST',
                            headers: {
                                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute(
                                    'content'),
                                'Accept': 'application/json',
                                'Content-Type': 'application/json'
                            },
                            body: JSON.stringify({
                                rejection_reason: data.rejectionReason,
                                rejection_notes: data.rejectionNotes || ''
                            })
                        });

                        const result = await response.json();

                        if (result.success) {
                            // Close modal and reset
                            data.showRejectWorkLogModal = false;
                            data.rejectionReason = '';
                            data.rejectionNotes = '';
                            data.selectedWorkLogId = null;

                            // Show success message
                            alert('✅ Work log rejected successfully.');

                            // Reload work logs
                            loadWorkLogs();

                            // Reload page after 1 second to update status if needed
                            setTimeout(() => {
                                window.location.reload();
                            }, 1000);
                        } else {
                            alert('❌ ' + result.message);
                        }
                    } catch (error) {
                        console.error('Error:', error);
                        alert('❌ An error occurred. Please try again.');
                    }
                };
            </script>
            <script>
                function openPreview(fileUrl, fileName, fileType) {
                    console.log('Opening preview:', {
                        fileUrl,
                        fileName,
                        fileType
                    }); // Debug log

                    const modal = document.getElementById('previewModal');
                    const title = document.getElementById('previewTitle');
                    const content = document.getElementById('previewContent');
                    const loading = document.getElementById('previewLoading');
                    const fileTypeIcon = document.getElementById('fileTypeIcon');
                    const fileInfo = document.getElementById('fileInfo');
                    const downloadLink = document.getElementById('downloadLink');

                    // Show loading and clear content
                    loading.classList.remove('hidden');
                    content.innerHTML = '';
                    content.appendChild(loading);

                    // Set modal title and info
                    title.textContent = fileName;
                    downloadLink.href = fileUrl;
                    downloadLink.download = fileName;

                    if (fileType === 'image') {
                        fileTypeIcon.innerHTML = '<i class="bi bi-image text-blue-600 dark:text-blue-400"></i>';
                        fileInfo.textContent = 'Image File';

                        // Create image element with blob URL to handle authentication
                        const img = document.createElement('img');

                        // Use fetch to get the image with proper headers
                        fetch(fileUrl, {
                                headers: {
                                    'Accept': 'image/*'
                                },
                                credentials: 'same-origin' // Include cookies for auth
                            })
                            .then(response => {
                                if (!response.ok) {
                                    throw new Error(`HTTP ${response.status}: ${response.statusText}`);
                                }
                                return response.blob();
                            })
                            .then(blob => {
                                const blobUrl = URL.createObjectURL(blob);
                                img.src = blobUrl;
                                img.alt = fileName;
                                img.className = 'max-w-full h-auto rounded-lg shadow-lg';

                                img.onload = function() {
                                    loading.classList.add('hidden');
                                    content.innerHTML = '';

                                    // Create image container
                                    const imgContainer = document.createElement('div');
                                    imgContainer.className = 'relative';
                                    imgContainer.appendChild(img);

                                    // Add image dimensions
                                    const dimensions = document.createElement('div');
                                    dimensions.className = 'mt-4 text-center text-sm text-gray-600 dark:text-gray-400';
                                    dimensions.innerHTML = `
                        <div class="inline-flex items-center gap-4 rounded-full bg-gray-100 px-4 py-2 dark:bg-gray-800">
                            <span><i class="bi bi-arrows-angle-expand me-1"></i> ${this.naturalWidth} × ${this.naturalHeight} pixels</span>
                            <span><i class="bi bi-hdd me-1"></i> ${formatFileSize(blob.size)}</span>
                        </div>
                    `;
                                    imgContainer.appendChild(dimensions);
                                    content.appendChild(imgContainer);

                                    // Add zoom functionality
                                    img.addEventListener('click', function() {
                                        this.classList.toggle('cursor-zoom-out');
                                        if (this.classList.contains('cursor-zoom-out')) {
                                            this.style.maxWidth = 'none';
                                            this.style.width = 'auto';
                                            this.style.height = 'auto';
                                        } else {
                                            this.className = 'max-w-full h-auto rounded-lg shadow-lg';
                                        }
                                    });

                                    // Clean up blob URL when modal closes
                                    modal.addEventListener('hidden', () => {
                                        URL.revokeObjectURL(blobUrl);
                                    }, {
                                        once: true
                                    });
                                };

                                img.onerror = function() {
                                    showError('Failed to decode image data.');
                                };
                            })
                            .catch(error => {
                                console.error('Image loading error:', error);
                                showError(`Could not load image: ${error.message}`);
                            });

                    } else if (fileType === 'pdf') {
                        fileTypeIcon.innerHTML = '<i class="bi bi-file-earmark-pdf-fill text-red-600 dark:text-red-400"></i>';
                        fileInfo.textContent = 'PDF Document';

                        // Use PDF.js for better PDF preview (if available) or fallback to Google Docs
                        setTimeout(() => {
                            loading.classList.add('hidden');

                            // Try to use PDF.js if available
                            if (typeof pdfjsLib !== 'undefined') {
                                renderWithPdfJs(fileUrl, fileName);
                            } else {
                                // Fallback to Google Docs Viewer
                                renderWithGoogleDocs(fileUrl, fileName);
                            }
                        }, 500);
                    }

                    // Show modal
                    modal.classList.remove('hidden');
                    document.body.style.overflow = 'hidden';

                    function showError(message) {
                        loading.classList.add('hidden');
                        content.innerHTML = `
                <div class="flex flex-col items-center justify-center p-8 text-center">
                    <div class="mb-4 rounded-full bg-red-100 p-4 dark:bg-red-900/30">
                        <i class="bi bi-exclamation-triangle text-4xl text-red-600 dark:text-red-400"></i>
                    </div>
                    <h4 class="mb-2 text-lg font-semibold text-gray-800 dark:text-white/90">Preview Unavailable</h4>
                    <p class="mb-4 text-gray-600 dark:text-gray-400">${message}</p>
                    <div class="flex gap-3">
                        <a href="${fileUrl}" 
                           download="${fileName}"
                           class="rounded-lg border border-green-200 bg-green-50 px-4 py-2 text-sm font-medium text-green-600 shadow-theme-xs hover:bg-green-100 dark:border-green-800 dark:bg-green-900/20 dark:text-green-400 dark:hover:bg-green-900/30">
                            <i class="bi bi-download me-2"></i>Download File
                        </a>
                        <button onclick="closePreview()" 
                                class="rounded-lg border border-gray-300 bg-white px-4 py-2 text-sm font-medium text-gray-700 shadow-theme-xs hover:bg-gray-50 dark:border-gray-700 dark:bg-gray-800 dark:text-gray-400 dark:hover:bg-white/[0.03]">
                            Close
                        </button>
                    </div>
                </div>
            `;
                    }

                    function renderWithGoogleDocs(fileUrl, fileName) {
                        const googleDocsUrl = `https://docs.google.com/viewer?url=${encodeURIComponent(fileUrl)}&embedded=true`;

                        content.innerHTML = `
                <div class="w-full">
                    <iframe 
                        src="${googleDocsUrl}" 
                        class="w-full min-h-[600px] rounded-lg border border-gray-200 dark:border-gray-700"
                        title="${fileName}"
                        frameborder="0"
                        allowfullscreen>
                    </iframe>
                    
                    <div class="mt-4 flex flex-wrap items-center justify-between gap-4">
                        <div class="text-sm text-gray-600 dark:text-gray-400">
                            <i class="bi bi-info-circle me-1"></i>
                            PDF preview powered by Google Docs Viewer
                        </div>
                        <div class="flex gap-2">
                            <a href="${fileUrl}" 
                               target="_blank"
                               class="rounded-lg border border-blue-200 bg-blue-50 px-4 py-2 text-sm font-medium text-blue-600 shadow-theme-xs hover:bg-blue-100 dark:border-blue-800 dark:bg-blue-900/20 dark:text-blue-400 dark:hover:bg-blue-900/30">
                                <i class="bi bi-box-arrow-up-right me-2"></i>Open in New Tab
                            </a>
                            <a href="${fileUrl}" 
                               download="${fileName}"
                               class="rounded-lg border border-green-200 bg-green-50 px-4 py-2 text-sm font-medium text-green-600 shadow-theme-xs hover:bg-green-100 dark:border-green-800 dark:bg-green-900/20 dark:text-green-400 dark:hover:bg-green-900/30">
                                <i class="bi bi-download me-2"></i>Download PDF
                            </a>
                        </div>
                    </div>
                </div>
            `;
                    }
                }

                // Helper function to check PDF embed support
                function isPdfEmbedSupported() {
                    try {
                        const testIframe = document.createElement('iframe');
                        testIframe.style.display = 'none';
                        document.body.appendChild(testIframe);
                        const supportsPdf = 'PDFDocument' in window || testIframe.contentWindow.PDFDocument;
                        document.body.removeChild(testIframe);
                        return supportsPdf;
                    } catch (e) {
                        return false;
                    }
                }

                function openPdfInNewTab(url) {
                    window.open(url, '_blank', 'noopener,noreferrer');
                }

                function closePreview() {
                    const modal = document.getElementById('previewModal');
                    modal.classList.add('hidden');
                    document.body.style.overflow = 'auto';

                    // Dispatch custom event for cleanup
                    modal.dispatchEvent(new CustomEvent('hidden'));
                }

                // Helper function to format file size
                function formatFileSize(bytes) {
                    if (bytes === 0) return '0 Bytes';
                    const k = 1024;
                    const sizes = ['Bytes', 'KB', 'MB', 'GB'];
                    const i = Math.floor(Math.log(bytes) / Math.log(k));
                    return parseFloat((bytes / Math.pow(k, i)).toFixed(2)) + ' ' + sizes[i];
                }

                // Close modal when clicking on background
                document.getElementById('previewModal').addEventListener('click', function(e) {
                    if (e.target === this) {
                        closePreview();
                    }
                });

                // Close modal with Escape key
                document.addEventListener('keydown', function(e) {
                    if (e.key === 'Escape' && !document.getElementById('previewModal').classList.contains('hidden')) {
                        closePreview();
                    }
                });
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
                                        window.history.back();
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
