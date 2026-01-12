<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Support\Str;

class MaintenanceRequest extends Model
{
    use HasFactory, SoftDeletes, HasUuids;

    protected $keyType = 'string';
    public $incrementing = false;

    protected $fillable = [
        'user_id',
        'item_id',
    
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
        'rejected_at'
    ];

    protected $casts = [
        'requested_at' => 'datetime',
        'assigned_at' => 'datetime',
        'started_at' => 'datetime',
        'completed_at' => 'datetime',
        'rejected_at' => 'datetime',
    ];

    // Constants for priority
    const PRIORITY_LOW = 'low';
    const PRIORITY_MEDIUM = 'medium';
    const PRIORITY_HIGH = 'high';
    const PRIORITY_EMERGENCY = 'emergency';

    // Constants for status
    const STATUS_PENDING = 'pending';
    const STATUS_ASSIGNED = 'assigned';
    const STATUS_IN_PROGRESS = 'in_progress';
    const STATUS_COMPLETED = 'completed';
    const STATUS_REJECTED = 'rejected';
    const STATUS_NOT_FIXED = 'not_fixed';

    // Constants for issue types
    // const ISSUE_HARDWARE = 'hardware';
    // const ISSUE_SOFTWARE = 'software';
    // const ISSUE_NETWORK = 'network';
    // const ISSUE_PERFORMANCE = 'performance';
    // const ISSUE_SETUP = 'setup';
    // const ISSUE_UPGRADE = 'upgrade';
    // const ISSUE_OTHER = 'other';

    protected static function boot()
    {
        parent::boot();

        static::creating(function ($model) {
            $model->id = Str::uuid();
            $model->ticket_number = $model->generateTicketNumber();
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
            self::STATUS_REJECTED => 'Rejected',
            self::STATUS_NOT_FIXED => 'Not Fixed',
        ];
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

    /**
     * Check if request is completed
     */
    public function isCompleted(): bool
    {
        return $this->status === self::STATUS_COMPLETED;
    }
}