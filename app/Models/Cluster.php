<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Concerns\HasUuids;
class Cluster extends Model
{
    use HasUuids;
    
    protected $fillable = ['name', 'organization_id', 'cluster_chairman'];
    
    public function organization()
    {
        return $this->belongsTo(Organization::class);
    }
    
    public function divisions()
    {
        return $this->hasMany(Division::class);
    }
    
    public function chairman()
    {
        return $this->belongsTo(User::class, 'cluster_chairman');
    }
}
