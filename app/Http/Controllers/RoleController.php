<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Spatie\Permission\Models\Role;
// use Spatie\Permission\Models\Permission;

class RoleController extends Controller
{
public function create()
{
    return view('admin.roles');
}

    public function edit(Role $role)
    {
        $permissions = Permission::all()->map(function($permission) {
            return [
                'id' => $permission->id,
                'name' => $permission->name,
                'description' => $this->getPermissionDescription($permission->name),
            ];
        });

        $roles = Role::withCount(['users', 'permissions'])->get()->map(function($roleItem) {
            return [
                'id' => $roleItem->id,
                'name' => $roleItem->name,
                'description' => $roleItem->description,
                'users_count' => $roleItem->users_count,
                'permissions_count' => $roleItem->permissions_count,
            ];
        });

        return view('roles.create', compact('role', 'permissions', 'roles'));
    }

    private function getPermissionDescription($permissionName)
    {
        $descriptions = [
            'view_users' => 'View user profiles and lists',
            'create_users' => 'Create new user accounts',
            'edit_users' => 'Modify existing user information',
            'delete_users' => 'Remove user accounts',
            'view_roles' => 'View role lists and details',
            'create_roles' => 'Create new roles',
            'edit_roles' => 'Modify role permissions',
            'delete_roles' => 'Remove roles',
            'view_settings' => 'View system settings',
            'edit_settings' => 'Modify system settings',
            'view_reports' => 'Access and view reports',
            'export_data' => 'Export system data',
        ];

        return $descriptions[$permissionName] ?? 'No description available';
    }
}