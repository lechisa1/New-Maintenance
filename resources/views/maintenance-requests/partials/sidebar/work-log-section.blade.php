<!-- @include('maintenance-requests.partials.alerts') -->
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
                
                <!-- Reject Modal -->
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
                                            await window.submitWorkLogRejection(workLogId, reason, notes);
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

                                 <button type="button"
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
                @if ($maintenanceRequest->workLogs->count() > 0 &&
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
            </div>
        </div>
    @endif
@endif
<script>
    // Work Logs JavaScript
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
                        'X-CSRF-TOKEN': getCsrfToken(),
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
                    showToast(result.message, 'success');
                    
                    // Reload page after 3 seconds
                    setTimeout(() => {
                        window.location.reload();
                    }, 3000);
                } else {
                    // Show error toast
                    showToast(result.message || 'Failed to save work log.', 'error');
                    
                    // Re-enable submit button on error
                    submitBtn.innerHTML = originalText;
                    submitBtn.disabled = false;
                }
            } catch (error) {
                console.error('Error:', error);
                showToast('An error occurred. Please try again.', 'error');
                submitBtn.innerHTML = originalText;
                submitBtn.disabled = false;
            }
        });
    }

    // Individual Work Log Actions
    setupWorkLogActions();
});

// Setup work log action buttons
function setupWorkLogActions() {
    // Accept work log buttons
    document.querySelectorAll('[data-accept-worklog]').forEach(button => {
        button.addEventListener('click', async function() {
            const workLogId = this.getAttribute('data-accept-worklog');
            await acceptWorkLog(workLogId);
        });
    });

    // Reject work log buttons
    document.querySelectorAll('[data-reject-worklog]').forEach(button => {
        button.addEventListener('click', async function() {
            const workLogId = this.getAttribute('data-reject-worklog');
            const reason = prompt('Why are you rejecting this work log? (min 10 chars)');
            if (!reason || reason.length < 10) {
                alert('Rejection reason must be at least 10 characters.');
                return;
            }
            await rejectWorkLog(workLogId, reason);
        });
    });

    // Delete work log buttons
    document.querySelectorAll('[data-delete-worklog]').forEach(button => {
        button.addEventListener('click', async function() {
            const workLogId = this.getAttribute('data-delete-worklog');
            if (confirm('Are you sure you want to delete this work log?')) {
                await deleteWorkLog(workLogId);
            }
        });
    });
}

// Accept Work Log - Make sure this is globally available
window.acceptWorkLog = async function(workLogId) {
    try {
        const response = await fetch(`/work-logs/${workLogId}/accept`, {
            method: 'POST',
            headers: {
                'X-CSRF-TOKEN': getCsrfToken(),
                'Accept': 'application/json'
            }
        });

        const result = await response.json();

        if (result.success) {
            showToast('✅ Work accepted! Technician and ICT directors have been notified.', 'success');
            
            // Reload page after 1.5 seconds
            setTimeout(() => {
                window.location.reload();
            }, 1500);
        } else {
            showToast('❌ ' + result.message, 'error');
        }
    } catch (error) {
        console.error('Error:', error);
        showToast('❌ An error occurred. Please try again.', 'error');
    }
};

// Reject Work Log - Make sure this is globally available
window.rejectWorkLog = async function(workLogId, rejectionReason) {
    try {
        const response = await fetch(`/work-logs/${workLogId}/reject`, {
            method: 'POST',
            headers: {
                'X-CSRF-TOKEN': getCsrfToken(),
                'Content-Type': 'application/json',
                'Accept': 'application/json'
            },
            body: JSON.stringify({
                rejection_reason: rejectionReason
            })
        });

        const result = await response.json();

        if (result.success) {
            showToast('✅ Work log rejected successfully.', 'success');
            setTimeout(() => {
                window.location.reload();
            }, 1000);
        } else {
            showToast('❌ ' + result.message, 'error');
        }
    } catch (error) {
        console.error('Error:', error);
        showToast('❌ An error occurred. Please try again.', 'error');
    }
};

// Delete Work Log
async function deleteWorkLog(workLogId) {
    try {
        const response = await fetch(`/work-logs/${workLogId}`, {
            method: 'DELETE',
            headers: {
                'X-CSRF-TOKEN': getCsrfToken(),
                'Accept': 'application/json'
            }
        });

        const result = await response.json();

        if (result.success) {
            showToast('✅ Work log deleted successfully.', 'success');
            setTimeout(() => {
                window.location.reload();
            }, 1000);
        } else {
            showToast('❌ ' + result.message, 'error');
        }
    } catch (error) {
        console.error('Error:', error);
        showToast('❌ An error occurred. Please try again.', 'error');
    }
}

// Submit work log rejection from modal - Make sure this is globally available
window.submitWorkLogRejection = async function(workLogId, reason, notes) {
    if (!reason || reason.trim().length < 5) {
        alert('Please provide a valid rejection reason (minimum 5 characters).');
        return;
    }

    try {
        const response = await fetch(`/work-logs/${workLogId}/reject`, {
            method: 'POST',
            headers: {
                'X-CSRF-TOKEN': getCsrfToken(),
                'Accept': 'application/json',
                'Content-Type': 'application/json'
            },
            body: JSON.stringify({
                rejection_reason: reason,
                rejection_notes: notes || ''
            })
        });

        const result = await response.json();

        if (result.success) {
            showToast('✅ Work log rejected successfully.', 'success');
            setTimeout(() => window.location.reload(), 1000);
        } else {
            showToast('❌ ' + (result.message || 'Rejection failed'), 'error');
        }
    } catch (err) {
        console.error(err);
        showToast('❌ An error occurred. Please try again.', 'error');
    }
};

// Open reject work log modal
window.openRejectWorkLogModal = function(workLogId) {
    const modalElement = document.querySelector('[x-data*="showRejectWorkLogModal"]');
    if (modalElement && modalElement.__x) {
        modalElement.__x.$data.selectedWorkLogId = workLogId;
        modalElement.__x.$data.showRejectWorkLogModal = true;
    }
};

// Open confirm work log modal
window.openConfirmWorkLogModal = function(workLogId) {
    const modalElement = document.querySelector('[x-data*="showConfirmWorkLogModal"]');
    if (modalElement && modalElement.__x) {
        modalElement.__x.$data.selectedWorkLogId = workLogId;
        modalElement.__x.$data.showConfirmWorkLogModal = true;
    }
};

// Helper Functions
function getCsrfToken() {
    const metaTag = document.querySelector('meta[name="csrf-token"]');
    return metaTag ? metaTag.getAttribute('content') : '';
}

function showToast(message, type = 'info') {
    const colors = {
        success: 'bg-green-500',
        error: 'bg-red-500',
        info: 'bg-blue-500',
        warning: 'bg-yellow-500'
    };

    const icons = {
        success: 'bi-check-circle-fill',
        error: 'bi-exclamation-triangle-fill',
        info: 'bi-info-circle-fill',
        warning: 'bi-exclamation-circle-fill'
    };

    const toast = document.createElement('div');
    toast.className = `fixed top-4 right-4 z-50 px-4 py-3 rounded-lg shadow-lg ${colors[type]} text-white transform transition-all duration-300 translate-x-full`;
    toast.innerHTML = `
        <div class="flex items-center">
            <i class="bi ${icons[type]} mr-2"></i>
            <span>${message}</span>
        </div>
    `;
    
    document.body.appendChild(toast);
    
    // Animate in
    requestAnimationFrame(() => {
        toast.classList.remove('translate-x-full');
    });

    // Remove after 5 seconds
    setTimeout(() => {
        toast.classList.add('translate-x-full');
        setTimeout(() => toast.remove(), 300);
    }, 5000);

    // Close on click
    toast.addEventListener('click', () => {
        toast.classList.add('translate-x-full');
        setTimeout(() => toast.remove(), 300);
    });
}

// Initialize when Alpine.js is ready
document.addEventListener('alpine:initialized', () => {
    setupWorkLogActions();
});
</script>