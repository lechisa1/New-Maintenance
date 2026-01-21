<?php

namespace App\Models;

use Illuminate\Support\Str;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Spatie\Permission\Models\Permission as SpatiePermission;
class Permission extends SpatiePermission
{
    use HasUuids;

    public $incrementing = false;
    protected $keyType = 'string';

    protected $fillable = [
        'name',
        'guard_name',
        'resource',
        'description',
        'group',
        'is_system_permission'
    ];

    protected $casts = [
        'is_system_permission' => 'boolean',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
        'is_active' => 'boolean',
    ];

    protected static function booted()
    {
        static::creating(function ($model) {
            if (! $model->id) {
                $model->id = (string) Str::uuid();
            }
            
            if (empty($model->guard_name)) {
                $model->guard_name = 'web';
            }
        });
    }

    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    /**
     * Find a permission by its name.
     * This is the missing method that's causing the error.
     */


    /**
     * Find or create a permission by its name.
     */
    public static function findOrCreate(string $name, ?string $guardName = null): self
    {
        $guardName = $guardName ?? config('auth.defaults.guard', 'web');
        
        $permission = self::findByName($name, $guardName);
        
        if (!$permission) {
            $permission = self::create([
                'name' => $name,
                'guard_name' => $guardName,
            ]);
        }
        
        return $permission;
    }

    /**
     * Get the roles that have this permission.
     */
    public function roles(): BelongsToMany
    {
        return $this->belongsToMany(
            Role::class,
            'role_has_permissions',
            'permission_id',
            'role_id'
        )->withTimestamps();
    }

    /**
     * Scope a query to filter by resource.
     */
    public function scopeResource($query, $resource)
    {
        return $query->where('resource', $resource);
    }

    /**
     * Scope a query to filter by group.
     */
    public function scopeGroup($query, $group)
    {
        return $query->where('group', $group);
    }

    /**
     * Scope a query to filter by guard.
     */
    public function scopeGuard($query, $guard)
    {
        return $query->where('guard_name', $guard);
    }

    /**
     * Get the display name attribute.
     */
    public function getDisplayNameAttribute(): string
    {
        return ucwords(str_replace(['-', '_'], ' ', $this->name));
    }

    /**
     * Get the roles count attribute.
     */
    public function getRolesCountAttribute(): int
    {
        return $this->roles()->count();
    }

    /**
     * Check if permission is assignable.
     */
    public function getIsAssignableAttribute(): bool
    {
        return !$this->is_system_permission || $this->name !== 'super-admin-access';
    }
}