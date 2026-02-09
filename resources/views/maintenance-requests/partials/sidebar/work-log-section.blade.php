 @include('maintenance-requests.partials.alerts')
 @if ($maintenanceRequest->workLogs->count() > 0)
     @php
         $isRequester = auth()->id() == $maintenanceRequest->user_id;
         $isAssignedTechnician = auth()->user()->id == $maintenanceRequest->assigned_to;
         $showWorkLogSection = $isRequester || $isAssignedTechnician || $maintenanceRequest->workLogs->count() > 0;
     @endphp

     @if ($showWorkLogSection)
         <div class="rounded-2xl border border-gray-200 bg-white p-6 dark:border-gray-800 dark:bg-white/[0.03]">
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

          
                             <!-- Action Buttons -->
                             <div class="flex gap-2">
                                 @if (auth()->user()->id === $maintenanceRequest->user_id &&
                                         in_array($maintenanceRequest->status, ['completed', 'waiting_confirmation']) &&
                                         $log->status === 'pending')
                                     <!-- Reject Button -->
                                     <button
                                         @click="$dispatch('open-reject-worklog-modal', { workLogId: '{{ $log->id }}' })"
                                         class="flex items-center gap-1 text-red-500 hover:text-red-700 text-xs">
                                         <i class="bi bi-x-circle"></i> Reject
                                     </button>

                                     <!-- Confirm Button -->
                                     <button
                                         @click="$dispatch('open-confirm-worklog-modal', { workLogId: '{{ $log->id }}' })"
                                         class="flex items-center gap-1 text-green-500 hover:text-green-700 text-xs">
                                         <i class="bi bi-check-circle"></i> Confirm
                                     </button>
                                 @endif
                             </div>
                         </div>

                         <!-- Work Done -->
                         <div class="mt-3">
                             <div class="text-sm font-medium text-gray-700 dark:text-gray-300">Work Done:</div>
                             <p class="mt-1 text-sm text-gray-800 dark:text-white/90 whitespace-pre-line">
                                 {{ $log->work_done }}
                             </p>
                         </div>

                         <!-- Materials Used -->
                         @if ($log->materials_used)
                             <div class="mt-3">
                                 <div class="text-sm font-medium text-gray-700 dark:text-gray-300">Materials Used:
                                 </div>
                                 <p class="mt-1 text-sm text-gray-600 dark:text-gray-400">
                                     {{ $log->materials_used }}
                                 </p>
                             </div>
                         @endif

                         <!-- Time Spent -->
                         @if ($log->time_spent_minutes)
                             <div class="mt-3">
                                 <div class="text-sm font-medium text-gray-700 dark:text-gray-300">Time Spent:</div>
                                 <p class="mt-1 text-sm text-gray-600 dark:text-gray-400">
                                     {{ $log->getTimeSpentFormatted() }}
                                 </p>
                             </div>
                         @endif
                     </div>
                 @endforeach
                 <div x-data="{
                     show: false,
                     workLogId: null,
                     reason: '',
                     notes: ''
                 }"
                     @open-reject-worklog-modal.window="
        workLogId = $event.detail.workLogId;
        show = true;
    ">
                     <div x-show="show" x-cloak
                         class="fixed inset-0 z-50 flex items-center justify-center bg-black bg-opacity-50">
                         <div class="bg-white dark:bg-gray-800 rounded-lg p-6 w-full max-w-md">

                             <!-- Header -->
                             <div class="flex justify-between items-center mb-4">
                                 <h3 class="text-lg font-semibold text-gray-800 dark:text-white/90">
                                     <i class="bi bi-x-circle me-2 text-red-500"></i>
                                     Reject Work Log
                                 </h3>

                                 <button @click="show = false"
                                     class="text-gray-500 hover:text-gray-700 dark:text-gray-400 dark:hover:text-gray-200">
                                     <i class="bi bi-x-lg"></i>
                                 </button>
                             </div>

                             <!-- Body -->
                             <div class="space-y-4">

                                 <!-- Reason -->
                                 <div>
                                     <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                                         Reason for Rejection <span class="text-red-500">*</span>
                                     </label>

                                     <textarea x-model="reason" rows="4" required
                                         class="w-full rounded-lg border border-gray-300 bg-transparent px-4 py-2.5 text-sm
                               text-gray-800 shadow-theme-xs placeholder:text-gray-400
                               focus:border-blue-300 focus:outline-none focus:ring-2 focus:ring-blue-500/10
                               dark:border-gray-700 dark:bg-gray-900 dark:text-white/90
                               dark:placeholder:text-white/30 dark:focus:border-blue-800"
                                         placeholder="Please explain why you're rejecting this work log..."></textarea>
                                 </div>

                                 <!-- Notes -->
                                 <div>
                                     <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                                         Additional Notes (Optional)
                                     </label>

                                     <textarea x-model="notes" rows="2"
                                         class="w-full rounded-lg border border-gray-300 bg-transparent px-4 py-2.5 text-sm
                               text-gray-800 shadow-theme-xs placeholder:text-gray-400
                               focus:border-blue-300 focus:outline-none focus:ring-2 focus:ring-blue-500/10
                               dark:border-gray-700 dark:bg-gray-900 dark:text-white/90
                               dark:placeholder:text-white/30 dark:focus:border-blue-800"
                                         placeholder="Any additional feedback for the technician..."></textarea>
                                 </div>

                                 <!-- Actions -->
                                 <div class="flex justify-end gap-3">
                                     <button type="button" @click="show = false; reason = ''; notes = ''"
                                         class="rounded-lg border border-gray-300 bg-white px-4 py-2.5 text-sm font-medium
                               text-gray-700 shadow-theme-xs hover:bg-gray-50
                               dark:border-gray-700 dark:bg-gray-800 dark:text-gray-400
                               dark:hover:bg-white/[0.03]">
                                         Cancel
                                     </button>

                                     <button type="button"
                                         @click="
        $event.target.disabled = true;
        await submitWorkLogRejection(workLogId, reason, notes);
        reason = '';
        notes = '';
        show = false;
        $event.target.disabled = false;
    "
                                         class="rounded-lg bg-red-500 px-4 py-2.5 text-sm font-medium text-white
           shadow-theme-xs hover:bg-red-600 focus:outline-none
           focus:ring-2 focus:ring-red-500 focus:ring-offset-2
           dark:focus:ring-offset-gray-900">
                                         Reject Work Log
                                     </button>

                                 </div>
                             </div>
                         </div>
                     </div>
                 </div>

                 <div x-data="{
                     show: false,
                     workLogId: null
                 }"
                     @open-confirm-worklog-modal.window="
        workLogId = $event.detail.workLogId;
        show = true;
    ">
                     <div x-show="show" x-cloak
                         class="fixed inset-0 z-50 flex items-center justify-center bg-black/50">
                         <div class="bg-white dark:bg-gray-800 rounded-lg p-6 w-full max-w-md">
                             <h3 class="text-lg font-semibold mb-4 text-green-500">
                                 Confirm Work Log
                             </h3>

                             <p class="mb-4">Are you sure you want to confirm this work log?</p>

                             <div class="flex justify-end gap-2">
                                 <button @click="show = false">Cancel</button>

                                 <button
                                     @click="
                        acceptWorkLog(workLogId);
                        show = false;
                    "
                                     class="bg-green-500 text-white px-4 py-2 rounded">
                                     Confirm
                                 </button>
                             </div>
                         </div>
                     </div>
                 </div>

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
