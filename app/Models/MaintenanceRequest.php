<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Support\Str;
use App\Notifications\MaintenanceRequestCreated;
use App\Notifications\MaintenanceRequestApproval;
use App\Notifications\MaintenanceRequestForwardedToIctDirector;
use App\Models\User;

class MaintenanceRequest extends Model
{
    use HasFactory, SoftDeletes, HasUuids;

    protected $keyType = 'string';
    public $incrementing = false;

    protected $fillable = [
        'user_id',
        'item_id',
        'is_approved',
        'description',
        'issue_type_id',
        'priority',
        'status',
        'ticket_number',
        'assigned_to',
        'technician_notes',
        'resolution_notes',
        'requested_at',
        'assigned_at',
        'started_at',
        'completed_at',
        'rejected_at',
        'approved_at',
        'approved_by',
        'forwarded_to_ict_director_at',
        'rejection_reason',
        'approval_notes',
        'rejected_by',
    ];

    protected $casts = [
        'requested_at' => 'datetime',
        'assigned_at' => 'datetime',
        'started_at' => 'datetime',
        'completed_at' => 'datetime',
        'rejected_at' => 'datetime',
        'approved_at' => 'datetime',
        'forwarded_to_ict_director_at' => 'datetime',
        'is_approved' => 'boolean',
    ];

    // Constants for priority
    const PRIORITY_LOW = 'low';
    const PRIORITY_MEDIUM = 'medium';
    const PRIORITY_HIGH = 'high';
    const PRIORITY_EMERGENCY = 'emergency';

    // Constants for status
    const STATUS_PENDING = 'pending';
    const STATUS_ASSIGNED = 'assigned';
    const STATUS_APPROVED = 'approved';
    const STATUS_WAITING_CONFIRMATION = 'waiting_confirmation';
    const STATUS_IN_PROGRESS = 'in_progress';
    const STATUS_COMPLETED = 'completed';
    const STATUS_REJECTED = 'rejected';
    const STATUS_NOT_FIXED = 'not_fixed';
    const STATUS_WAITING_APPROVAL = 'waiting_approval';
    const STATUS_CONFIRMED = 'confirmed';
    //the below is for the case when request is waiting for approval review after being forwarded to ICT director
    const STATUS_PENDING_APPROVAL_REVIEW = 'pending_approval_review';


    protected static function boot()
    {
        parent::boot();

        static::creating(function ($model) {
            $model->id = Str::uuid();
            $model->ticket_number = $model->generateTicketNumber();
        });
        // Add this to automatically handle notifications
        static::created(function ($model) {
            $model->handleNotifications();
        });

        static::updated(function ($model) {
            $model->handleStatusChangeNotifications();
        });
    }

    /**
     * Generate unique ticket number
     */
    public function generateTicketNumber(): string
    {
        $prefix = 'MTN';
        $year = date('Y');
        $month = date('m');

        do {
            $sequence = str_pad(rand(1, 9999), 4, '0', STR_PAD_LEFT);
            $ticket = "{$prefix}-{$year}-{$month}-{$sequence}";
        } while (self::where('ticket_number', $ticket)->exists());

        return $ticket;
    }

    /**
     * Get priority options
     */
    public static function getPriorityOptions(): array
    {
        return [
            self::PRIORITY_LOW => 'Low Priority',
            self::PRIORITY_MEDIUM => 'Medium Priority',
            self::PRIORITY_HIGH => 'High Priority',
            self::PRIORITY_EMERGENCY => 'Emergency',
        ];
    }

    /**
     * Get status options
     */
    public static function getStatusOptions(): array
    {
        return [
            self::STATUS_PENDING => 'Pending',
            self::STATUS_ASSIGNED => 'Assigned',
            self::STATUS_IN_PROGRESS => 'In Progress',
            self::STATUS_WAITING_CONFIRMATION => 'waiting_confirmation',
            self::STATUS_COMPLETED => 'Completed',
            self::STATUS_WAITING_APPROVAL => 'Waiting Approval',
            self::STATUS_REJECTED => 'Rejected',
            self::STATUS_NOT_FIXED => 'Not Fixed',
            self::STATUS_APPROVED => 'Approved',
            self::STATUS_CONFIRMED => 'Confirmed',
            self::STATUS_PENDING_APPROVAL_REVIEW => 'Pending Approval Review',
        ];
    }
    public function approver(): BelongsTo
    {
        return $this->belongsTo(User::class, 'approved_by');
    }

    public function needsApproval(): bool
    {
        // Check if ANY item in the request has an issue type that needs approval
        foreach ($this->items as $requestItem) {
            if ($requestItem->issueType && $requestItem->issueType->is_need_approval) {
                return true;
            }
        }

        return false;
    }
    // Helper to notify ICT directors
    public function notifyIctDirectors()
    {
        if ($this->forwarded_to_ict_director_at) {
            return; // already notified
        }
        foreach ($this->getGeneralIctDirectors() as $director) {
            $director->notify(
                new MaintenanceRequestForwardedToIctDirector($this)
            );
        }

        // Update request status as assigned directly to ICT
        $this->update([

            'approved_at' => now(),
            'forwarded_to_ict_director_at' => now(),
        ]);
    }
    public function isApproved(): bool
    {
        return $this->is_approved === true;
    }
    // app/Models/MaintenanceRequest.php

    public function assignedTechnicians()
    {
        return $this->hasMany(MaintenanceRequestTechnician::class);
    }

    public function getAssignedTechniciansListAttribute()
    {
        return $this->assignedTechnicians()
            ->with('technician')
            ->get()
            ->map(function ($assignment) {
                return [
                    'technician' => $assignment->technician,
                    'items' => $assignment->getItems(),
                    'status' => $assignment->status,
                    'assigned_at' => $assignment->assigned_at,
                ];
            });
    }

    // Helper to check if a technician is assigned to specific items
    public function isTechnicianAssignedToItem($technicianId, $itemId)
    {
        return $this->assignedTechnicians()
            ->where('user_id', $technicianId)
            ->get()
            ->contains(function ($assignment) use ($itemId) {
                return in_array($itemId, $assignment->item_ids ?? []);
            });
    }
    public function getNextApprover(): ?User
    {
        $user = $this->user;
        if (!$user) return null;

        // Normal user in a division (not chairman) → division chairman
        if ($user->division && !$user->isDivisionChairman() && $user->division->chairman) {
            return $user->division->chairman;
        }

        // Division chairman or user without division → cluster chairman
        if ($user->cluster && $user->cluster->chairman && $user->cluster->chairman->id !== $user->id) {
            return $user->cluster->chairman;
        }

        // If no division/cluster chairman, check if user has a supervisor
        if ($user->supervisor) {
            return $user->supervisor;
        }

        // Otherwise, no approver
        return null;
    }

    public function isDivisionChairman(): bool
    {
        return Division::where('division_chairman', $this->id)->exists();
    }


    public function isClusterChairman(): bool
    {
        return Cluster::where('cluster_chairman', $this->id)->exists();
    }
    public function getRequiredApprover(): ?User
    {
        $user = $this->user;

        // 1️⃣ Division chairman has priority
        if ($user->division && $user->division->chairman) {
            return $user->division->chairman;
        }

        // 2️⃣ Otherwise cluster chairman
        if ($user->cluster && $user->cluster->chairman) {
            return $user->cluster->chairman;
        }

        return null;
    }
    public function scopeVisibleToIct($query)
    {
        return $query->where(function ($q) {
            $q->whereHas('items.issueType', function ($sub) {
                $sub->where('is_need_approval', false);
            })
                ->orWhere('is_approved', true)
                ->orWhereIn('status', ['assigned', 'in_progress', 'pending_approval_review']);
        });
    }




    /**
     * Get priority badge class
     */
    public function getPriorityBadgeClass(): string
    {
        return match ($this->priority) {
            self::PRIORITY_LOW => 'bg-green-100 text-green-800 dark:bg-green-900 dark:text-green-200',
            self::PRIORITY_MEDIUM => 'bg-yellow-100 text-yellow-800 dark:bg-yellow-900 dark:text-yellow-200',
            self::PRIORITY_HIGH => 'bg-orange-100 text-orange-800 dark:bg-orange-900 dark:text-orange-200',
            self::PRIORITY_EMERGENCY => 'bg-red-100 text-red-800 dark:bg-red-900 dark:text-red-200',
            default => 'bg-gray-100 text-gray-800 dark:bg-gray-900 dark:text-gray-200',
        };
    }
    public function getGeneralIctDirectors()
    {
        return User::permission('maintenance_requests.assign')->get();
    }
    /**
     * Get status badge class
     */
    public function getStatusBadgeClass(): string
    {
        return match ($this->status) {
            self::STATUS_PENDING => 'bg-yellow-100 text-yellow-800 dark:bg-yellow-900 dark:text-yellow-200',
            self::STATUS_ASSIGNED => 'bg-blue-100 text-blue-800 dark:bg-blue-900 dark:text-blue-200',
            self::STATUS_IN_PROGRESS => 'bg-indigo-100 text-indigo-800 dark:bg-indigo-900 dark:text-indigo-200',
            self::STATUS_COMPLETED => 'bg-green-100 text-green-800 dark:bg-green-900 dark:text-green-200',
            self::STATUS_REJECTED => 'bg-red-100 text-red-800 dark:bg-red-900 dark:text-red-200',
            self::STATUS_NOT_FIXED => 'bg-gray-100 text-gray-800 dark:bg-gray-900 dark:text-gray-200',
            self::STATUS_PENDING_APPROVAL_REVIEW => 'bg-orange-100 text-orange-800 dark:bg-orange-900 dark:text-orange-200',
            default => 'bg-gray-100 text-gray-800 dark:bg-gray-900 dark:text-gray-200',
        };
    }

    /**
     * Get priority text
     */
    public function getPriorityText(): string
    {
        return self::getPriorityOptions()[$this->priority] ?? strtoupper($this->priority);
    }

    /**
     * Get status text
     */
    public function getStatusText(): string
    {
        return self::getStatusOptions()[$this->status] ?? ucfirst(str_replace('_', ' ', $this->status));
    }

    /**
     * Get issue type text
     */
    /**
     * Get all item names as a comma-separated string
     */
    // In MaintenanceRequest model
    public function getItemNamesAttribute()
    {
        return $this->items->pluck('item.name')->filter()->implode(', ');
    }


    /**
     * Get all issue type names as a comma-separated string
     */
    public function getIssueTypeNamesAttribute(): string
    {
        return $this->items
            ->map(fn($item) => $item->issueType?->name)
            ->filter()
            ->unique()
            ->implode(', ');
    }

    /**
     * Override the existing getIssueTypeText method
     */
    public function getIssueTypeText(): string
    {
        return $this->issue_type_names ?: 'N/A';
    }


    /**
     * Get formatted requested date
     */
    public function getRequestedDate(): string
    {
        return $this->requested_at->format('M d, Y');
    }

    /**
     * Get formatted requested time
     */
    public function getRequestedTime(): string
    {
        return $this->requested_at->format('h:i A');
    }

    /**
     * Calculate response time in hours
     */
    public function getResponseTime(): ?float
    {
        if ($this->assigned_at) {
            return round($this->requested_at->diffInHours($this->assigned_at, true), 1);
        }
        return null;
    }

    /**
     * Calculate resolution time in hours
     */
    public function getResolutionTime(): ?float
    {
        if ($this->completed_at) {
            $start = $this->started_at ?? $this->assigned_at ?? $this->requested_at;
            return round($start->diffInHours($this->completed_at, true), 1);
        }
        return null;
    }

    /**
     * Relationships
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    // public function item(): BelongsTo
    // {
    //     return $this->belongsTo(Item::class, 'item_id');
    // }
    public function items()
    {
        return $this->hasMany(MaintenanceRequestItem::class);
    }
    public function requestItems(): HasMany
    {
        return $this->hasMany(MaintenanceRequestItem::class);
    }

    // Optional: Helper to get items through the pivot
    public function getItemsListAttribute()
    {
        return $this->requestItems->map(function ($item) {
            return [
                'item' => $item->item,
                'issue_type' => $item->issueType,
                'description' => $item->description
            ];
        });
    }
    public function assignedTechnician(): BelongsTo
    {
        return $this->belongsTo(User::class, 'assigned_to');
    }

    public function files(): HasMany
    {
        return $this->hasMany(MaintenanceRequestFile::class);
    }

    public function statusHistories(): HasMany
    {
        return $this->hasMany(StatusHistory::class)->orderBy('created_at', 'desc');
    }

    /**
     * Scope for user's requests
     */
    public function scopeForUser($query, $userId)
    {
        return $query->where('user_id', $userId);
    }
    // Scopes
    public function scopeNeedsApproval($query)
    {
        return $query->whereHas('items.issueType', function ($q) {
            $q->where('is_need_approval', true);
        });
    }
    public function scopeWaitingApproval($query)
    {
        return $query->where('status', self::STATUS_WAITING_APPROVAL);
    }
    /**
     * Scope for assigned technician
     */
    public function scopeAssignedTo($query, $technicianId)
    {
        return $query->where('assigned_to', $technicianId);
    }

    /**
     * Scope by status
     */
    public function scopeByStatus($query, $status)
    {
        return $query->where('status', $status);
    }

    /**
     * Scope by priority
     */
    public function scopeByPriority($query, $priority)
    {
        return $query->where('priority', $priority);
    }

    /**
     * Scope by issue type
     */
    public function scopeByIssueType($query, $issueType)
    {
        return $query->where('issue_type', $issueType);
    }

    /**
     * Scope for open requests
     */
    public function scopeOpen($query)
    {
        return $query->whereIn('status', [self::STATUS_PENDING, self::STATUS_ASSIGNED, self::STATUS_IN_PROGRESS]);
    }
    public function approvalRequest()
    {
        return $this->hasOne(ApprovalRequest::class)->where('status', 'pending');
    }

    public function approvalRequests()
    {
        return $this->hasMany(ApprovalRequest::class);
    }
    /**
     * Scope for closed requests
     */
    public function scopeClosed($query)
    {
        return $query->whereIn('status', [self::STATUS_COMPLETED, self::STATUS_REJECTED, self::STATUS_NOT_FIXED]);
    }

    /**
     * Check if request is open
     */
    public function isOpen(): bool
    {
        return in_array($this->status, [self::STATUS_PENDING, self::STATUS_ASSIGNED, self::STATUS_IN_PROGRESS]);
    }

    /**
     * Check if request is assigned
     */
    public function isAssigned(): bool
    {
        return $this->status === self::STATUS_ASSIGNED || $this->status === self::STATUS_IN_PROGRESS;
    }
    public function approvedByUser(): BelongsTo
    {
        return $this->belongsTo(User::class, 'approved_by');
    }

    public function rejectedByUser(): BelongsTo
    {
        return $this->belongsTo(User::class, 'rejected_by');
    }

    /**
     * Check if request is completed
     */
    public function isCompleted(): bool
    {
        return $this->status === self::STATUS_COMPLETED;
    }
    /**
     * Reject the maintenance request
     */
    public function reject(User $rejector, string $reason): bool
    {
        return $this->update([
            'status' => self::STATUS_REJECTED,
            'rejected_at' => now(),
            'rejected_by' => $rejector->id,
            'rejection_reason' => $reason,
        ]);
    }
    // In MaintenanceRequest model
    public function notifyCompletion(string $status)
    {
        if (!$this->user) {
            \Log::warning('Cannot notify requester: user not found', ['request_id' => $this->id]);
            return;
        }

        try {
            $this->user->notify(
                new \App\Notifications\MaintenanceRequestCompleted($this, $status)
            );
        } catch (\Exception $e) {
            \Log::error('Failed to send completion notification: ' . $e->getMessage(), [
                'request_id' => $this->id,
                'error' => $e->getTraceAsString()
            ]);
        }
    }

    public function notifyEscalation()
    {
        try {
            $ictDirectors = $this->getGeneralIctDirectors();
            foreach ($ictDirectors as $director) {
                $director->notify(
                    new \App\Notifications\MaintenanceRequestEscalated($this)
                );
            }

            \Log::info('Escalation notification sent to ICT directors', [
                'request_id' => $this->id,
                'director_count' => count($ictDirectors)
            ]);
        } catch (\Exception $e) {
            \Log::error('Failed to send escalation notification: ' . $e->getMessage(), [
                'request_id' => $this->id,
                'error' => $e->getTraceAsString()
            ]);
        }
    }
    // In MaintenanceRequest model
    public function notifyConfirmation()
    {
        try {
            // Notify technician
            if ($this->assignedTechnician) {
                $this->assignedTechnician->notify(
                    new \App\Notifications\MaintenanceRequestConfirmed($this)
                );
            }

            // Notify ICT directors (excluding the technician if they're also a director)
            $ictDirectors = $this->getGeneralIctDirectors();
            foreach ($ictDirectors as $director) {
                if ($this->assignedTechnician && $director->id === $this->assignedTechnician->id) {
                    continue;
                }

                $director->notify(
                    new \App\Notifications\MaintenanceRequestConfirmedToIct($this)
                );
            }

            \Log::info('Confirmation notifications sent', [
                'request_id' => $this->id,
                'technician_notified' => (bool) $this->assignedTechnician,
                'directors_notified' => count($ictDirectors)
            ]);
        } catch (\Exception $e) {
            \Log::error('Failed to send confirmation notifications: ' . $e->getMessage(), [
                'request_id' => $this->id,
                'error' => $e->getTraceAsString()
            ]);
        }
    }
    // In MaintenanceRequest model
    public function notifyWorkLogRejected(WorkLog $workLog, string $rejectionReason)
    {
        try {
            // Notify technician
            if ($workLog->technician) {
                $workLog->technician->notify(
                    new \App\Notifications\WorkLogRejected($this, $workLog, $rejectionReason)
                );

                \Log::info('Work log rejection notification sent', [
                    'request_id' => $this->id,
                    'work_log_id' => $workLog->id,
                    'technician_id' => $workLog->technician->id
                ]);
            }
        } catch (\Exception $e) {
            \Log::error('Failed to send work log rejection notification: ' . $e->getMessage(), [
                'request_id' => $this->id,
                'work_log_id' => $workLog->id,
                'error' => $e->getTraceAsString()
            ]);
        }
    }
    public function uploadAttachment($file, $type = 'general'): MaintenanceRequestFile
    {
        $originalName = $file->getClientOriginalName();
        $extension = $file->getClientOriginalExtension();
        $mimeType = $file->getMimeType();
        $size = $file->getSize();

        // Generate unique filename
        $filename = Str::uuid() . '.' . $extension;

        // Store the file
        $path = $file->storeAs('maintenance-request-attachments', $filename, 'public');

        // Create database record
        return MaintenanceRequestFile::create([
            'maintenance_request_id' => $this->id,
            'file_name' => $filename,
            'original_name' => $originalName,
            'mime_type' => $mimeType,
            'size' => $size,
            'path' => $path,
            'type' => $type,
            'uploaded_by' => auth()->id(),
        ]);
    }
    public function handleStatusChangeNotifications()
    {
        // Check if status changed to approved
        if ($this->isDirty('status') && $this->status === self::STATUS_APPROVED && $this->isApproved()) {
            $this->user->notify(new MaintenanceRequestApproval($this, 'approved'));

            $ictDirectors = $this->getGeneralIctDirectors();
            foreach ($ictDirectors as $director) {
                $director->notify(new MaintenanceRequestForwardedToIctDirector($this));
            }
        }


        // Check if status changed to rejected
        if ($this->isDirty('status') && $this->status === self::STATUS_REJECTED) {
            $this->user->notify(new MaintenanceRequestApproval($this, 'rejected'));
        }
    }

    public function handleNotifications()
    {
        // Make sure items are loaded
        if (!$this->relationLoaded('items')) {
            $this->load('items.issueType');
        }

        \Log::info('handleNotifications called', [
            'request_id' => $this->id,
            'user_id' => $this->user_id,
            'items_count' => $this->items->count(),
            'needs_approval' => $this->needsApproval()
        ]);

        if (!$this->user) {
            \Log::error('User not found for maintenance request', ['request_id' => $this->id]);
            return;
        }

        try {
            // 1️⃣ Handle approval flow

            if ($this->needsApproval()) {
                \Log::info('Request needs approval', [
                    'request_id' => $this->id,
                    'items' => $this->items->map(fn($item) => [
                        'item_id' => $item->item_id,
                        'issue_type' => $item->issueType?->name,
                        'needs_approval' => $item->issueType?->is_need_approval
                    ])->toArray()
                ]);

                $this->update(['status' => self::STATUS_WAITING_APPROVAL]);

                // Find next approver
                $approver = $this->getNextApprover();

                if ($approver) {
                    \Log::info('Found approver', ['approver_id' => $approver->id, 'approver_name' => $approver->full_name]);
                    $approver->notify(new \App\Notifications\MaintenanceRequestApproval($this, 'approval_needed'));
                } else {
                    \Log::warning('No approver found, notifying ICT directors directly');
                    $this->notifyIctDirectors();
                }
            } else {
                \Log::info('Request does NOT need approval, notifying ICT directors directly');
                $this->notifyIctDirectors();
            }

            // Notify requester that request is created (optional)
            // $this->user->notify(new \App\Notifications\MaintenanceRequestCreated($this));

            \Log::info('Notifications handled successfully', ['request_id' => $this->id]);
        } catch (\Exception $e) {
            \Log::error('Error in handleNotifications: ' . $e->getMessage(), [
                'request_id' => $this->id,
                'trace' => $e->getTraceAsString()
            ]);
        }
    }
    // In MaintenanceRequest model
    public function workLogs(): HasMany
    {
        return $this->hasMany(WorkLog::class, 'request_id');
    }

    public function latestWorkLog(): HasOne
    {
        return $this->hasOne(WorkLog::class, 'request_id')->latest();
    }

    public function totalWorkTimeMinutes(): int
    {
        return $this->workLogs()->sum('time_spent_minutes');
    }

    public function getTotalWorkTimeFormatted(): string
    {
        $totalMinutes = $this->totalWorkTimeMinutes();
        $hours = floor($totalMinutes / 60);
        $minutes = $totalMinutes % 60;

        if ($hours > 0 && $minutes > 0) {
            return "{$hours}h {$minutes}m";
        } elseif ($hours > 0) {
            return "{$hours}h";
        } else {
            return "{$minutes}m";
        }
    }
}
