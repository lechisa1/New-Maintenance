<?php
// app/Models/ApprovalRequest.php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\SoftDeletes;

class ApprovalRequest extends Model
{
    use HasFactory, SoftDeletes, HasUuids;
    protected $keyType = 'string';
    public $incrementing = false;

    // Constants for status
    const STATUS_PENDING = 'pending';
    const STATUS_APPROVED = 'approved';
    const STATUS_REJECTED = 'rejected';
    const STATUS_FORWARDED = 'forwarded';

    protected $table = 'approval_requests';


    protected $fillable = [
        'maintenance_request_id',
        'technician_id',
        'issue_type_id',
        'item_id',
        'notes',
        'status',
        'forwarded_at',
        'rejected_at',
        'reviewed_by',
    ];

    protected $casts = [
        'forwarded_at' => 'datetime',
        'rejected_at' => 'datetime',
    ];

    public function maintenanceRequest(): BelongsTo
    {
        return $this->belongsTo(MaintenanceRequest::class);
    }

    public function technician(): BelongsTo
    {
        return $this->belongsTo(User::class, 'technician_id');
    }

    public function issueType(): BelongsTo
    {
        return $this->belongsTo(IssueType::class);
    }

    public function item(): BelongsTo
    {
        return $this->belongsTo(Item::class);
    }

    public function reviewer(): BelongsTo
    {
        return $this->belongsTo(User::class, 'reviewed_by');
    }
}
