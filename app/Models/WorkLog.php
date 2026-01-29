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
        'rejected_at',           // Add these
        'rejected_by',           // Add these
        'rejection_reason',      // Add these
        'rejection_notes',    // Add these
    ];

    protected $casts = [
        'log_date' => 'datetime',
        'materials_used' => 'array', // If storing as JSON
        'time_spent_minutes' => 'integer',
        'rejected_at' => 'datetime',
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