<?php


namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\SoftDeletes;

class MaintenanceRequestTechnician extends Model
{
    protected $table = 'maintenance_request_technicians';
    use HasFactory, SoftDeletes, HasUuids;
    protected $keyType = 'string';
    public $incrementing = false;
    protected $fillable = [
        'maintenance_request_id',
        'user_id',
        'item_ids',
        'notes',
        'status',
        'assigned_at',
        'started_at',
        'completed_at',
    ];

    protected $casts = [
        'item_ids' => 'array',
        'assigned_at' => 'datetime',
        'started_at' => 'datetime',
        'completed_at' => 'datetime',
    ];

    public function maintenanceRequest(): BelongsTo
    {
        return $this->belongsTo(MaintenanceRequest::class);
    }

    public function technician(): BelongsTo
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    public function getItems()
    {
        if (empty($this->item_ids)) {
            return collect([]);
        }

        return Item::whereIn('id', $this->item_ids)->get();
    }
}
