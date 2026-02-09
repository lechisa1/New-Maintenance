 @if ($maintenanceRequest->status === 'waiting_approval')
     @php
         $canApprove = false;
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

                         <form action="{{ route('approvals.approve', $maintenanceRequest) }}" method="POST"
                             enctype="multipart/form-data" id="approve-request-form">
                             @csrf
                             <div class="space-y-4">
                                 <!-- Approval Notes -->
                                 <div>
                                     <label class="mb-2 block text-sm font-medium text-gray-700 dark:text-gray-300">
                                         Approval Notes (Optional)
                                     </label>
                                     <textarea name="approval_notes" rows="3"
                                         class="w-full rounded-lg border border-gray-300 bg-transparent px-4 py-2.5 text-sm text-gray-800 shadow-theme-xs placeholder:text-gray-400 focus:border-blue-300 focus:outline-none focus:ring-2 focus:ring-blue-500/10 dark:border-gray-700 dark:bg-gray-900 dark:text-white/90 dark:placeholder:text-white/30 dark:focus:border-blue-800"
                                         placeholder="Add any notes or comments about this approval..."></textarea>
                                 </div>

                                 <!-- File Attachments -->
                                 <div>
                                     <label class="mb-2 block text-sm font-medium text-gray-700 dark:text-gray-300">
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
                                         <div class="text-xs font-medium text-gray-700 dark:text-gray-300">
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




             <div x-data="{ showRejectModal: false, reason: '' }">
                 <!-- Reject Button -->
                 <button @click="showRejectModal = true"
                     class="flex w-full items-center justify-center rounded-lg border border-red-200 bg-red-50 px-4 py-3 text-sm font-medium text-red-700 shadow-theme-xs hover:bg-red-100 hover:text-red-800 dark:border-red-800 dark:bg-red-900/20 dark:text-red-400 dark:hover:bg-red-900/30">
                     <i class="bi bi-x-circle me-2"></i> Reject
                 </button>

                 <!-- Reject Modal -->
                 <div x-show="showRejectModal" x-cloak
                     class="fixed inset-0 z-50 flex items-center justify-center bg-black bg-opacity-50">
                     <div class="bg-white dark:bg-gray-800 rounded-lg p-6 w-full max-w-md">
                         <div class="flex justify-between items-center mb-4">
                             <h3 class="text-lg font-semibold text-gray-800 dark:text-white/90">
                                 Reject Maintenance Request
                             </h3>
                             <button @click="showRejectModal = false" class="text-gray-600 dark:text-gray-300">
                                 <i class="bi bi-x-lg"></i>
                             </button>
                         </div>

                         <div class="space-y-4">
                             <textarea x-model="reason" rows="4" placeholder="Enter rejection reason..."
                                 class="w-full border rounded p-2 dark:bg-gray-900 dark:text-white"></textarea>
                         </div>

                         <div class="flex justify-end gap-2 mt-4">
                             <button @click="showRejectModal = false; reason=''"
                                 class="px-4 py-2 border rounded">Cancel</button>

                             <button
                                 @click="
                        if(reason.trim().length < 5){ alert('Reason is required'); return; }
                        fetch('{{ route('approvals.reject', $maintenanceRequest) }}', {
                            method: 'POST',
                            headers: {
                                'X-CSRF-TOKEN': '{{ csrf_token() }}',
                                'Content-Type': 'application/json',
                                'Accept': 'application/json'
                            },
                            body: JSON.stringify({ rejection_reason: reason })
                        })
                        .then(res => res.json())
                        .then(res => {
                            if(res.success){
                                alert('Request rejected successfully');
                                location.reload();
                            } else {
                                alert(res.error || 'Failed to reject request');
                            }
                        })
                        .catch(err => {
                            console.error(err);
                            alert('An error occurred');
                        });
                    "
                                 class="px-4 py-2 bg-red-500 text-white rounded">
                                 Reject
                             </button>
                         </div>
                     </div>
                 </div>
             </div>



         </div>
     @endif
 @endif

 @if ($maintenanceRequest->approved_at)
     <div class="mt-4 p-4 rounded-lg bg-green-50 dark:bg-green-900/20">
         <div class="flex items-center">
             <div class="mr-3 flex h-8 w-8 items-center justify-center rounded-full bg-green-100 dark:bg-green-900/30">
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
 @if ($maintenanceRequest->status === App\Models\MaintenanceRequest::STATUS_REJECTED)
     <div class="mt-4 p-4 rounded-lg bg-red-50 dark:bg-red-900/20">
         <div class="flex items-center">
             <div class="mr-3 flex h-8 w-8 items-center justify-center rounded-full bg-red-100 dark:bg-red-900/30">
                 <i class="bi bi-x-circle text-red-600 dark:text-red-300"></i>
             </div>
             <div>
                 <div class="text-sm font-medium text-gray-800 dark:text-white/90">
                     Rejected by
                     {{ $maintenanceRequest->approvedByUser ? $maintenanceRequest->approvedByUser->full_name : 'N/A' }}
                 </div>
                 <div class="text-xs text-gray-600 dark:text-gray-400">
                     on {{ $maintenanceRequest->rejected_at?->format('M d, Y h:i A') }}
                 </div>
                 @if ($maintenanceRequest->rejection_reason)
                     <div class="text-xs text-gray-600 dark:text-gray-400 mt-1">
                         Reason: {{ $maintenanceRequest->rejection_reason }}
                     </div>
                 @endif
             </div>
         </div>
     </div>
 @endif
