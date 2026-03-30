<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\SoftDeletes;

class MaintenanceRequestItem extends Model
{
    //
    use HasFactory, SoftDeletes, HasUuids;
    protected $keyType = 'string';
    public $incrementing = false;
    protected $fillable = [
        'maintenance_request_id',
        'item_id',
        'issue_type_id',
        'description',
    ];

    public function maintenanceRequest()
    {
        return $this->belongsTo(MaintenanceRequest::class);
    }

    public function item()
    {
        return $this->belongsTo(Item::class)->withTrashed();
    }

    public function issueType()
    {
        return $this->belongsTo(IssueType::class);
    }
}
