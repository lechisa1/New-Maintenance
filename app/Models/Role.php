<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Support\Str;
use Spatie\Permission\Models\Role as SpatieRole;

class Role extends SpatieRole
{
    use HasUuids;

    public $incrementing = false;
    protected $keyType = 'string';

    protected $fillable = [
        'name',
        'guard_name',
        'dashboard_route',
        'description',
        'is_system_role'
    ];

    protected $casts = [
        'is_system_role' => 'boolean',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];

    protected static function booted()
    {
        static::creating(function ($model) {
            if (! $model->id) {
                $model->id = (string) Str::uuid();
            }

            if (empty($model->guard_name)) {
                $model->guard_name = config('auth.defaults.guard', 'web');
            }
        });
    }

    // Your custom scopes are OK
    public function scopeSystem($query)
    {
        return $query->where('is_system_role', true);
    }

    public function scopeNonSystem($query)
    {
        return $query->where('is_system_role', false);
    }
}