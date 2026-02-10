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
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
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
                        window.history.back();
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

// Accept Work Log
async function acceptWorkLog(workLogId) {
    // if (!confirm('Are you sure you want to accept and confirm this work log?')) {
    //     return;
    // }

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
}

// Reject Work Log
async function rejectWorkLog(workLogId, rejectionReason) {
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
}

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

// Submit work log rejection from modal
window.submitWorkLogRejection = function(workLogId, reason, notes) {
    if (!reason || reason.trim().length < 5) {
        alert('Please provide a valid rejection reason.');
        return Promise.resolve(); // important so await doesn't hang
    }

    return fetch(`/work-logs/${workLogId}/reject`, {
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
    })
    .then(res => res.json())
    .then(result => {
        if (result.success) {
            showToast('✅ Work log rejected successfully.', 'success');
            setTimeout(() => window.location.reload(), 1000);
        } else {
            showToast('❌ ' + (result.message || 'Rejection failed'), 'error');
        }
    })
    .catch(err => {
        console.error(err);
        showToast('❌ An error occurred. Please try again.', 'error');
    });
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
    return document.querySelector('meta[name="csrf-token"]').getAttribute('content');
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