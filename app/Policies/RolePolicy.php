<?php

namespace App\Policies;

use App\Models\User;
use App\Models\Role;
use Illuminate\Auth\Access\HandlesAuthorization;

class RolePolicy
{
    use HandlesAuthorization;

    /**
     * Determine whether the user can view any models.
     */
    public function viewAny(User $user): bool
    {
        return $user->hasPermissionTo('view-any-role');
    }

    /**
     * Determine whether the user can view the model.
     */
    public function view(User $user, Role $role): bool
    {
        return $user->hasPermissionTo('view-role');
    }

    /**
     * Determine whether the user can create models.
     */
    public function create(User $user): bool
    {
        return $user->hasPermissionTo('create-role');
    }

    /**
     * Determine whether the user can update the model.
     */
    public function update(User $user, Role $role): bool
    {
        if (!$role->is_editable) {
            return false;
        }
        
        return $user->hasPermissionTo('update-role');
    }

    /**
     * Determine whether the user can delete the model.
     */
    public function delete(User $user, Role $role): bool
    {
        if (!$role->is_deletable) {
            return false;
        }
        
        return $user->hasPermissionTo('delete-role');
    }

    /**
     * Determine whether the user can assign permissions.
     */
    public function assignPermissions(User $user, Role $role): bool
    {
        return $user->hasPermissionTo('assign-role-permissions');
    }

    /**
     * Determine whether the user can view role users.
     */
    public function viewUsers(User $user, Role $role): bool
    {
        return $user->hasPermissionTo('view-role-users');
    }
}