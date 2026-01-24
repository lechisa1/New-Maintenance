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
    ];

    protected $casts = [
        'log_date' => 'datetime',
        'materials_used' => 'array', // If storing as JSON
        'time_spent_minutes' => 'integer',
    ];

    /**
     * Relationships
     */
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
}