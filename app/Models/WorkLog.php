<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class WorkLog extends Model
{
    use HasUuids;

    protected $keyType = 'string';
    public $incrementing = false;

    protected $fillable = [
        'request_id',
        'technician_id',
        'work_done',
        'materials_used',
        'time_spent_minutes',
        'completion_notes',
        'log_date',
        'status',
        'rejected_at',
        'rejected_by',
        'rejection_reason',
        'rejection_notes',
        'item_ids', // JSON array of item IDs
        'issue_type_ids', // JSON array of issue type IDs
        'item_notes', // JSON object with notes per item
    ];

    protected $casts = [
        'log_date' => 'datetime',
        'materials_used' => 'array', // If storing as JSON
        'time_spent_minutes' => 'integer',
        'rejected_at' => 'datetime',
        'item_ids' => 'array',
        'issue_type_ids' => 'array',
        'item_notes' => 'array',
    ];

    /**
     * Relationships
     */
    const STATUS_PENDING  = 'pending';
    const STATUS_ACCEPTED = 'accepted';
    const STATUS_REJECTED = 'rejected';
    public function maintenanceRequest(): BelongsTo
    {
        return $this->belongsTo(MaintenanceRequest::class, 'request_id');
    }

    public function technician(): BelongsTo
    {
        return $this->belongsTo(User::class, 'technician_id');
    }

    /**
     * Get formatted time spent
     */
    public function getTimeSpentFormatted(): string
    {
        $hours = floor($this->time_spent_minutes / 60);
        $minutes = $this->time_spent_minutes % 60;

        if ($hours > 0 && $minutes > 0) {
            return "{$hours}h {$minutes}m";
        } elseif ($hours > 0) {
            return "{$hours}h";
        } else {
            return "{$minutes}m";
        }
    }

    /**
     * Get log date formatted
     */
    public function getLogDateFormatted(): string
    {
        return $this->log_date->format('M d, Y');
    }
    public function rejectedBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'rejected_by');
    }

    public function isRejected(): bool
    {
        return $this->status === self::STATUS_REJECTED;
    }

    public function isAccepted(): bool
    {
        return $this->status === self::STATUS_ACCEPTED;
    }
    public function getItems()
    {
        if (empty($this->item_ids)) {
            return collect([]);
        }

        return Item::withTrashed()->whereIn('id', $this->item_ids)->get();
    }
    public function getIssueTypes()
    {
        if (empty($this->issue_type_ids)) {
            return collect([]);
        }

        return IssueType::whereIn('id', $this->issue_type_ids)->get();
    }

    public function getItemNote($itemId)
    {
        return $this->item_notes[$itemId] ?? null;
    }

    // Add method to get rejection badge class:
    public function getRejectionBadgeClass(): string
    {
        if ($this->isRejected()) {
            return 'bg-red-100 text-red-800 dark:bg-red-900 dark:text-red-200';
        }
        return 'bg-green-100 text-green-800 dark:bg-green-900 dark:text-green-200';
    }

    // Add method to get rejection status text:
    public function getRejectionStatusText(): string
    {
        return $this->isRejected() ? 'Rejected' : 'Accepted';
    }
    /**
     * Get log time formatted
     */
    public function getLogTimeFormatted(): string
    {
        return $this->log_date->format('h:i A');
    }

    /**
     * Scope for technician's logs
     */
    public function scopeForTechnician($query, $technicianId)
    {
        return $query->where('technician_id', $technicianId);
    }

    /**
     * Scope for request's logs
     */
    public function scopeForRequest($query, $requestId)
    {
        return $query->where('request_id', $requestId);
    }

    /**
     * Scope for recent logs
     */
    public function scopeRecent($query, $days = 7)
    {
        return $query->where('log_date', '>=', now()->subDays($days));
    }
    public function getStatusBadgeText(): string
    {
        return match ($this->status) {
            self::STATUS_ACCEPTED => 'Accepted',
            self::STATUS_REJECTED => 'Rejected',
            default => 'Pending',
        };
    }
    public function getStatusBadgeClass(): string
    {
        return match ($this->status) {
            self::STATUS_ACCEPTED => 'bg-green-100 text-green-800 dark:bg-green-900 dark:text-green-200',
            self::STATUS_REJECTED => 'bg-red-100 text-red-800 dark:bg-red-900 dark:text-red-200',
            default => 'bg-yellow-100 text-yellow-800 dark:bg-yellow-900 dark:text-yellow-200',
        };
    }
}
