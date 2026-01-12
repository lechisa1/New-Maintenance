<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Concerns\HasUuids;

class Organization extends Model
{
    use HasUuids;
    
    protected $fillable = ['name'];
    
    public function clusters()
    {
        return $this->hasMany(Cluster::class);
    }
    
    public function clusters_count()
    {
        return $this->clusters()->count();
    }
}
