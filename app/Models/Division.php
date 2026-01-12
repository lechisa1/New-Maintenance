<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Concerns\HasUuids;
class Division extends Model
{
    use HasUuids;
    
    protected $fillable = ['name', 'cluster_id', 'division_chairman'];
    
    public function cluster()
    {
        return $this->belongsTo(Cluster::class);
    }
    
    public function chairman()
    {
        return $this->belongsTo(User::class, 'division_chairman');
    }
}
