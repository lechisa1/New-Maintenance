<div class="rounded-xl bg-white dark:bg-gray-800/50 border border-gray-200 dark:border-gray-700 overflow-hidden">
    <!-- Header with accent bar -->
    <div class="relative px-5 pt-5 pb-2">
        <div class="absolute left-0 top-0 h-1 w-20 bg-gradient-to-r from-blue-500 to-blue-400 rounded-tl-xl"></div>
        <h4 class="flex items-center text-sm font-semibold text-gray-800 dark:text-white/90">
            <span
                class="flex h-6 w-6 items-center justify-center rounded-lg bg-blue-100 text-blue-600 dark:bg-blue-900/30 dark:text-blue-400 mr-2">
                <i class="bi bi-info-circle text-sm"></i>
            </span>
            Request Details
        </h4>
    </div>

    <!-- Content with improved spacing -->
    <div class="p-5 pt-2 space-y-5">
        <!-- Issue Type Section -->
        <div>
            <div class="flex items-center gap-1.5 mb-2.5">
                <div class="h-4 w-1 bg-blue-500 rounded-full"></div>
                <span class="text-xs font-medium uppercase tracking-wider text-gray-500 dark:text-gray-400">Issue
                    Types</span>
            </div>

            <div class="space-y-2 pl-2.5">
                @forelse ($maintenanceRequest->items as $requestItem)
                    <div class="flex items-start gap-2.5 group hover:translate-x-0.5 transition-transform">
                        <!-- Bullet point -->
                        <span
                            class="mt-1.5 h-1.5 w-1.5 flex-shrink-0 rounded-full bg-blue-500 group-hover:bg-blue-600"></span>

                        <!-- Content -->
                        <div class="flex-1">
                            <div class="flex flex-wrap items-center gap-x-2 gap-y-1">
                                <span
                                    class="inline-flex items-center rounded-full bg-blue-50 px-2.5 py-0.5 text-xs font-medium text-blue-700 dark:bg-blue-900/20 dark:text-blue-300 border border-blue-200 dark:border-blue-800">
                                    {{ $requestItem->issueType?->name ?? 'N/A' }}
                                </span>
                                <span class="text-xs text-gray-400 dark:text-gray-500">on</span>
                                <span class="text-sm font-medium text-gray-800 dark:text-white/90">
                                    {{ $requestItem->item?->name ?? 'Unknown Item' }}
                                </span>
                            </div>
                            @if ($requestItem->description)
                                <p
                                    class="mt-1.5 text-xs text-gray-600 dark:text-gray-400 bg-gray-50 dark:bg-gray-800/50 p-2 rounded-lg border-l-2 border-gray-300 dark:border-gray-600">
                                    "{{ $requestItem->description }}"
                                </p>
                            @endif
                        </div>
                    </div>
                @empty
                    <div
                        class="flex items-center gap-2 rounded-lg bg-gray-50 dark:bg-gray-800/50 p-3 text-sm text-gray-500 dark:text-gray-400 border border-gray-200 dark:border-gray-700">
                        <i class="bi bi-exclamation-circle text-amber-500"></i>
                        <span>No issue types recorded.</span>
                    </div>
                @endforelse
            </div>
        </div>

        <!-- Divider with gradient -->
        <div class="relative">
            <div class="absolute inset-0 flex items-center">
                <div class="w-full border-t border-gray-200 dark:border-gray-700"></div>
            </div>
            <div class="relative flex justify-center">
                <span class="bg-white dark:bg-gray-800/50 px-2 text-xs text-gray-400 dark:text-gray-500">
                    <i class="bi bi-three-dots"></i>
                </span>
            </div>
        </div>

        <!-- Submitted By Section -->
        <div>
            <div class="flex items-center gap-1.5 mb-3">
                <div class="h-4 w-1 bg-purple-500 rounded-full"></div>
                <span class="text-xs font-medium uppercase tracking-wider text-gray-500 dark:text-gray-400">Submitted
                    By</span>
            </div>

            <div class="flex items-center gap-3 pl-2.5">
                <!-- Avatar with gradient -->
                <div
                    class="flex h-10 w-10 flex-shrink-0 items-center justify-center rounded-full bg-gradient-to-br from-purple-500 to-blue-500 text-sm font-bold text-white shadow-sm">
                    {{ strtoupper(substr($maintenanceRequest->user?->full_name ?? 'U', 0, 1)) }}
                </div>

                <div class="flex-1">
                    <div class="font-semibold text-gray-800 dark:text-white/90">
                        {{ $maintenanceRequest->user?->full_name ?? 'Unknown User' }}
                    </div>
                    <div class="flex items-center gap-1.5 mt-0.5">
                        <i class="bi bi-envelope text-xs text-gray-400"></i>
                        <span class="text-xs text-gray-500 dark:text-gray-400">
                            {{ $maintenanceRequest->user?->email ?? 'No email' }}
                        </span>
                    </div>

                    <div class="flex items-center gap-1.5 mt-0.5">
                        <i class="bi bi-phone text-xs text-gray-400"></i>
                        <span class="text-xs text-gray-500 dark:text-gray-400">
                            {{ $maintenanceRequest->user?->phone ?? 'No phone' }}
                        </span>
                    </div>

                </div>
            </div>
        </div>

        <!-- Submitted On Section -->
        <div>
            <div class="flex items-center gap-1.5 mb-2">
                <div class="h-4 w-1 bg-amber-500 rounded-full"></div>
                <span class="text-xs font-medium uppercase tracking-wider text-gray-500 dark:text-gray-400">Submitted
                    On</span>
            </div>

            <div class="flex items-center gap-3 pl-2.5">
                <div
                    class="flex h-9 w-9 flex-shrink-0 items-center justify-center rounded-lg bg-amber-100 text-amber-600 dark:bg-amber-900/30 dark:text-amber-400">
                    <i class="bi bi-calendar3"></i>
                </div>
                <div>
                    <div class="font-medium text-gray-800 dark:text-white/90">
                        {{ $maintenanceRequest->getRequestedDate() }}
                    </div>
                    <div class="flex items-center gap-1 text-xs text-gray-500 dark:text-gray-400">
                        <i class="bi bi-clock"></i>
                        {{ $maintenanceRequest->getRequestedTime() }}
                    </div>
                </div>
            </div>
        </div>

        <!-- Assigned Technicians Section -->
        @if ($maintenanceRequest->assignedTechnicians->count() > 0)
            <div>
                <div class="flex items-center gap-1.5 mb-2">
                    <div class="h-4 w-1 bg-emerald-500 rounded-full"></div>
                    <span class="text-xs font-medium uppercase tracking-wider text-gray-500 dark:text-gray-400">
                        Assigned Technicians
                    </span>
                    <span class="text-xs bg-gray-100 dark:bg-gray-700 px-2 py-0.5 rounded-full">
                        {{ $maintenanceRequest->assignedTechnicians->count() }}
                    </span>
                </div>

                <div class="space-y-3 pl-2.5">
                    @foreach ($maintenanceRequest->assignedTechnicians as $assignment)
                        @php
                            $assignedItems = $assignment->getItems();
                            $itemCount = $assignedItems->count();
                        @endphp

                        <div
                            class="flex items-start gap-3 p-2 rounded-lg hover:bg-gray-50 dark:hover:bg-gray-800/50 transition">
                            <!-- Technician Avatar/Icon -->
                            <div
                                class="flex h-9 w-9 flex-shrink-0 items-center justify-center rounded-lg 
                        @if ($assignment->status === 'completed') bg-green-100 text-green-600 dark:bg-green-900/30
                        @elseif($assignment->status === 'in_progress') bg-blue-100 text-blue-600 dark:bg-blue-900/30
                        @else bg-emerald-100 text-emerald-600 dark:bg-emerald-900/30 @endif">
                                @if ($assignment->status === 'completed')
                                    <i class="bi bi-check-lg"></i>
                                @elseif($assignment->status === 'in_progress')
                                    <i class="bi bi-gear"></i>
                                @else
                                    <i class="bi bi-person-check"></i>
                                @endif
                            </div>

                            <div class="flex-1">
                                <!-- Technician Name and Status -->
                                <div class="flex items-center flex-wrap gap-2">
                                    <span class="font-medium text-gray-800 dark:text-white/90">
                                        {{ $assignment->technician?->full_name ?? 'Unknown' }}
                                    </span>
                                    <span
                                        class="text-xs px-2 py-0.5 rounded-full 
                                @if ($assignment->status === 'completed') bg-green-100 text-green-700 dark:bg-green-900/30 dark:text-green-400
                                @elseif($assignment->status === 'in_progress') bg-blue-100 text-blue-700 dark:bg-blue-900/30 dark:text-blue-400
                                @else bg-gray-100 text-gray-700 dark:bg-gray-700 dark:text-gray-300 @endif">
                                        {{ ucfirst($assignment->status) }}
                                    </span>
                                </div>

                                <!-- Assigned Items -->
                                @if ($itemCount > 0)
                                    <div class="mt-1.5">
                                        <div class="text-xs text-gray-500 dark:text-gray-400 mb-1">
                                            Assigned Items ({{ $itemCount }}):
                                        </div>
                                        <div class="flex flex-wrap gap-1">
                                            @foreach ($assignedItems as $item)
                                                <span
                                                    class="inline-flex items-center text-xs bg-gray-100 dark:bg-gray-700 px-2 py-0.5 rounded">
                                                    {{ $item->name }}
                                                </span>
                                            @endforeach
                                        </div>
                                    </div>
                                @endif

                                <!-- Assignment Date -->
                                @if ($assignment->assigned_at)
                                    <div
                                        class="flex items-center gap-1 mt-1.5 text-xs text-gray-500 dark:text-gray-400">
                                        <i class="bi bi-clock"></i>
                                        Assigned: {{ $assignment->assigned_at->format('M d, Y h:i A') }}
                                        @if ($assignment->started_at)
                                            <span class="mx-1">•</span>
                                            <i class="bi bi-play-circle"></i>
                                            Started: {{ $assignment->started_at->format('M d, Y h:i A') }}
                                        @endif
                                    </div>
                                @endif

                                {{-- <!-- Notes -->
                                @if ($assignment->notes)
                                    <div
                                        class="mt-1.5 text-xs text-gray-600 dark:text-gray-400 bg-gray-50 dark:bg-gray-800/50 p-2 rounded border-l-2 border-emerald-300">
                                        <i class="bi bi-chat-text me-1"></i>
                                        {{ $assignment->notes }}
                                    </div>
                                @endif --}}
                            </div>
                        </div>

                        @if (!$loop->last)
                            <div class="border-b border-gray-100 dark:border-gray-700 my-2"></div>
                        @endif
                    @endforeach
                </div>
            </div>
        @endif


    </div>
</div>
