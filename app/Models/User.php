<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

use Spatie\Permission\Traits\HasRoles;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Casts\Attribute;
class User extends Authenticatable
{
    use HasFactory, Notifiable, HasUuids, HasRoles, SoftDeletes;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
      protected $primaryKey = 'id';
      protected $keyType = 'string';
     public $incrementing = false;
     protected $guard_name = 'web';

    protected $fillable = [
        'id', 
        'full_name',
        'email',
        'phone',
        'password',
        'division_id',
        'cluster_id',
        'email_verified_at',
        'last_login_at',
        'last_login_ip',
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var array<int, string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * Get the attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
            'last_login_at' => 'datetime',
        // 'is_active' => 'boolean',
        ];
    }
    protected $appends = [
        'avatar_url',
      
    ];
    /**
     * Get the route key for the model.
     */
    public function getRouteKeyName()
    {
        return 'id';
    }

/**
 * Get the unique identifier for the session.
 */
public function getAuthIdentifier()
{
    return (string) $this->id;
}

    /**
     * Relationship with Division
     */
    public function division()
    {
        return $this->belongsTo(Division::class, 'division_id');
    }

    /**
     * Relationship with Cluster
     */
    public function cluster()
    {
        return $this->belongsTo(Cluster::class, 'cluster_id');
    }

    /**
     * Relationship with Organization through Cluster
     */
    public function organization()
    {
        return $this->hasOneThrough(
            Organization::class,
            Cluster::class,
            'id', // Foreign key on clusters table
            'id', // Foreign key on organizations table
            'cluster_id', // Local key on users table
            'organization_id' // Local key on clusters table
        );
    }

    /**
     * Scope for filtering users
     */
    public function scopeFilter($query, array $filters)
    {
        $query->when($filters['search'] ?? null, function ($query, $search) {
            $query->where(function ($query) use ($search) {
                $query->where('full_name', 'like', '%' . $search . '%')
                    ->orWhere('email', 'like', '%' . $search . '%')
                    ->orWhere('phone', 'like', '%' . $search . '%');
            });
        });

        $query->when($filters['division_id'] ?? null, function ($query, $divisionId) {
            $query->where('division_id', $divisionId);
        });

        $query->when($filters['cluster_id'] ?? null, function ($query, $clusterId) {
            $query->where('cluster_id', $clusterId);
        });

        $query->when($filters['role'] ?? null, function ($query, $role) {
            $query->whereHas('roles', function ($query) use ($role) {
                $query->where('name', $role);
            });
        });

        $query->when($filters['status'] ?? null, function ($query, $status) {
            if ($status === 'active') {
                $query->whereNotNull('email_verified_at');
            } elseif ($status === 'inactive') {
                $query->whereNull('email_verified_at');
            }
        });
    }
public function isDivisionChairman(): bool
{
    return (string) $this->division?->division_chairman === (string) $this->id;
}

public function isClusterChairman(): bool
{
    return (string) $this->cluster?->cluster_chairman === (string) $this->id;
}


    /**
     * Get the user's full name.
     */
    // protected function fullName(): Attribute
    // {
    //     return Attribute::make(
    //         get: fn () => $this->full_name,
    //     );
    // }
    /**
     * Check if user is active
     */
    public function isActive()
    {
        return !is_null($this->email_verified_at);
    }
        public function isSuperAdmin(): bool
    {
        return $this->hasRole('super-admin');
    }
        public function isAdmin(): bool
    {
        return $this->hasRole('admin');
    }    /**
     * Check if user can access admin panel.
     */
    public function canAccessAdmin(): bool
    {
        return $this->hasAnyRole(['super-admin', 'admin']);
    }
        /**
     * Get the user's primary role.
     */
    public function getPrimaryRoleAttribute()
    {
        return $this->roles()->first();
    }
        /**
     * Get the user's dashboard route based on role.
     */
    public function getDashboardRouteAttribute()
    {
        $role = $this->primaryRole;
        return $role ? $role->dashboard_route : 'dashboard';
    }

        /**
     * Scope a query to only include admin users.
     */
    public function scopeAdmins($query)
    {
        return $query->whereHas('roles', function ($q) {
            $q->whereIn('name', ['super-admin', 'admin']);
        });
    }
        /**
     * Scope a query to only include active users.
     */
    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

        /**
     * Get the user's avatar URL.
     */
    protected function avatarUrl(): Attribute
    {
        return Attribute::make(
            get: fn () => 'https://ui-avatars.com/api/?name='.urlencode($this->full_name).'&background=random&color=fff',
        );
    }
    /**
     * Get user's hierarchical path
     */
    public function getHierarchyPath()
    {
        $path = [];
        
        if ($this->division) {
            $path[] = $this->division->name;
        }
        
        if ($this->cluster) {
            $path[] = $this->cluster->name;
        }
        
        if ($this->organization) {
            $path[] = $this->organization->name;
        }
        
        return implode(' → ', array_reverse($path));
    }
}