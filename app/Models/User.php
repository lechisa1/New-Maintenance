<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Spatie\Permission\Traits\HasRoles;
use Illuminate\Database\Eloquent\SoftDeletes;

class User extends Authenticatable
{
    use HasFactory, Notifiable, HasUuids, HasRoles, SoftDeletes;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'full_name',
        'email',
        'phone',
        'password',
        'division_id',
        'cluster_id',
        'email_verified_at',
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
        ];
    }

    /**
     * Get the route key for the model.
     */
    public function getRouteKeyName()
    {
        return 'id';
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

    /**
     * Check if user is active
     */
    public function isActive()
    {
        return !is_null($this->email_verified_at);
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