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
    const STATUS_IN_PROGRESS = 'in_progress';
    const STATUS_COMPLETED = 'completed';
    const STATUS_REJECTED = 'rejected';
    const STATUS_NOT_FIXED = 'not_fixed';
    const STATUS_WAITING_APPROVAL = 'waiting_approval';
 

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
            self::STATUS_COMPLETED => 'Completed',
            self::STATUS_WAITING_APPROVAL => 'Waiting Approval',
            self::STATUS_REJECTED => 'Rejected',
            self::STATUS_NOT_FIXED => 'Not Fixed',
            self::STATUS_APPROVED => 'Approved',
        ];
    }
public function approver(): BelongsTo
{
    return $this->belongsTo(User::class, 'approved_by');
}
public function needsApproval(): bool
{
    return $this->issueType?->is_need_approval === true;
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
    // public function getNextApprover()
    // {
    //     $user = $this->user;
        
    //     if (!$user) return null;
        
    //     // First, check division chairman
    //     if ($user->division && $user->division->chairman) {
    //         return $user->division->chairman;
    //     }
        
    //     // If no division chairman, check cluster chairman
    //     if ($user->cluster && $user->cluster->chairman) {
    //         return $user->cluster->chairman;
    //     }
        
    //     return null;
    // }
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

    // Otherwise, no approver
    return null;
}
// public function isDivisionChairman(): bool
// {
//     return (string) $this->division?->division_chairman === (string) $this->id;
// }
// public function isClusterChairman(): bool
// {
//     return (string) $this->cluster?->cluster_chairman === (string) $this->id;
// }
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
        $q->whereHas('issueType', function ($sub) {
            $sub->where('is_need_approval', false);
        })
        ->orWhere('is_approved', true);
    });
}

    /**
     * Get issue type options
     */
    // public static function getIssueTypeOptions(): array
    // {
    //     return [
    //         self::ISSUE_HARDWARE => 'Hardware Issue',
    //         self::ISSUE_SOFTWARE => 'Software Issue',
    //         self::ISSUE_NETWORK => 'Network/Connectivity',
    //         self::ISSUE_PERFORMANCE => 'Performance Problem',
    //         self::ISSUE_SETUP => 'New Setup/Installation',
    //         self::ISSUE_UPGRADE => 'Upgrade Request',
    //         self::ISSUE_OTHER => 'Other',
    //     ];
    // }
public function issueType(): BelongsTo
{
    return $this->belongsTo(IssueType::class);
}

    /**
     * Get priority badge class
     */
    public function getPriorityBadgeClass(): string
    {
        return match($this->priority) {
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
        return match($this->status) {
            self::STATUS_PENDING => 'bg-yellow-100 text-yellow-800 dark:bg-yellow-900 dark:text-yellow-200',
            self::STATUS_ASSIGNED => 'bg-blue-100 text-blue-800 dark:bg-blue-900 dark:text-blue-200',
            self::STATUS_IN_PROGRESS => 'bg-indigo-100 text-indigo-800 dark:bg-indigo-900 dark:text-indigo-200',
            self::STATUS_COMPLETED => 'bg-green-100 text-green-800 dark:bg-green-900 dark:text-green-200',
            self::STATUS_REJECTED => 'bg-red-100 text-red-800 dark:bg-red-900 dark:text-red-200',
            self::STATUS_NOT_FIXED => 'bg-gray-100 text-gray-800 dark:bg-gray-900 dark:text-gray-200',
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
public function getIssueTypeText(): string
{
    return $this->issueType?->name ?? 'N/A';
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

    public function item(): BelongsTo
    {
        return $this->belongsTo(Item::class, 'item_id');
    }

    public function assignedTechnician(): BelongsTo
    {
        return $this->belongsTo(User::class, 'assigned_to');
    }

    public function files(): HasMany
    {
        return $this->hasMany(MaintenanceRequestFile::class);
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
        return $query->whereHas('issueType', function ($q) {
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
            'approved_by' => $rejector->id,
            'rejection_reason' => $reason,
        ]);
    }
// In your MaintenanceRequest model, update the approve method:
// public function approve(User $approver, array $data = [], $files = null): bool
// {
//     return \DB::transaction(function () use ($approver, $data, $files) {
//         $updateData = [
//             'status' => self::STATUS_ASSIGNED,
//             'approved_at' => now(),
//             'approved_by' => $approver->id,
//             'forwarded_to_ict_director_at' => now(),
//         ];
        
//         if (!empty($data['approval_notes'])) {
//             $updateData['approval_notes'] = $data['approval_notes'];
//         }
        
//         $updated = $this->update($updateData);
        
//         // Handle file uploads - use passed files instead of request()
//         if ($updated && $files && count($files) > 0) {
//             foreach ($files as $file) {
//                 $this->uploadAttachment($file, 'approval');
//             }
//         }
        
//         return $updated;
//     });
// }

/**
 * Upload attachment for the maintenance request
 */
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
  // In MaintenanceRequest model
// public function handleNotifications()
// {
//     \Log::info('handleNotifications called', [
//         'request_id' => $this->id,
//         'user_id' => $this->user_id,
//         'needs_approval' => $this->needsApproval()
//     ]);
    
//     // Check if user exists
//     if (!$this->user) {
//         \Log::error('User not found for maintenance request', ['request_id' => $this->id]);
//         return;
//     }
    
//     try {
//         // Notify requester
//         \Log::info('Notifying requester', ['user_id' => $this->user_id]);
//         $this->user->notify(new \App\Notifications\MaintenanceRequestCreated($this));
        
//         if ($this->needsApproval()) {
//             \Log::info('Request needs approval', ['request_id' => $this->id]);
//             // Set status to waiting approval
//             $this->update(['status' => self::STATUS_WAITING_APPROVAL]);
            
//             // Find and notify the appropriate approver
//             $approver = $this->getNextApprover();
            
//             if ($approver) {
//                 \Log::info('Found approver', ['approver_id' => $approver->id]);
//                 $approver->notify(new \App\Notifications\MaintenanceRequestApproval($this, 'approval_needed'));
//             } else {
//                 \Log::warning('No approver found, notifying ICT directors directly');
//                 // If no approver found, notify ICT directors directly
//                 $ictDirectors = $this->getGeneralIctDirectors();
//                 foreach ($ictDirectors as $director) {
//                     $director->notify(new \App\Notifications\MaintenanceRequestForwardedToIctDirector($this));
//                 }
//                 $this->update([
//                     'status' => self::STATUS_ASSIGNED,
//                     'forwarded_to_ict_director_at' => now(),
//                 ]);
//             }
//         } else {
//             \Log::info('Request does not need approval, notifying ICT directors directly');
//             // No approval needed, notify ICT directors directly
//             $ictDirectors = $this->getGeneralIctDirectors();
//             foreach ($ictDirectors as $director) {
//                 $director->notify(new \App\Notifications\MaintenanceRequestForwardedToIctDirector($this));
//             }
//             $this->update([
//                 'approved_at' => now(),
//                 'forwarded_to_ict_director_at' => now(),
//             ]);
//         }
        
//         \Log::info('Notifications handled successfully', ['request_id' => $this->id]);
        
//     } catch (\Exception $e) {
//         \Log::error('Error in handleNotifications: ' . $e->getMessage(), [
//             'request_id' => $this->id,
//             'trace' => $e->getTraceAsString()
//         ]);
//     }
// }
public function handleNotifications()
{
    \Log::info('handleNotifications called', [
        'request_id' => $this->id,
        'user_id' => $this->user_id,
        'needs_approval' => $this->needsApproval()
    ]);

    if (!$this->user) {
        \Log::error('User not found for maintenance request', ['request_id' => $this->id]);
        return;
    }

    try {
        // 1️⃣ Notify requester that request is created
        // $this->user->notify(new \App\Notifications\MaintenanceRequestCreated($this));

        // 2️⃣ Handle approval flow
        if ($this->needsApproval()) {

            \Log::info('Request needs approval', ['request_id' => $this->id]);
            $this->update(['status' => self::STATUS_WAITING_APPROVAL]);

            // Find next approver
            $approver = $this->getNextApprover();

            if ($approver) {
                \Log::info('Found approver', ['approver_id' => $approver->id]);
                $approver->notify(new \App\Notifications\MaintenanceRequestApproval($this, 'approval_needed'));
            } else {
                \Log::warning('No approver found, notifying ICT directors directly');
                $this->notifyIctDirectors();
            }

        } else {
            \Log::info('Request does NOT need approval, notifying ICT directors directly');
            $this->notifyIctDirectors();
        }

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