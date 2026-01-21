<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Role;
use App\Models\Permission;
use Illuminate\Support\Str;

class RolePermissionSeeder extends Seeder
{
    /**
     * Default permissions structure
     */
    protected array $permissions = [
        'user' => [
            'view-any-user',
            'view-user',
            'create-user',
            'update-user',
            'delete-user',
            'restore-user',
            'force-delete-user',
        ],
        'role' => [
            'view-any-role',
            'view-role',
            'create-role',
            'update-role',
            'delete-role',
            'assign-role-permissions',
            'view-role-users',
        ],
        'permission' => [
            'view-any-permission',
            'view-permission',
            'create-permission',
            'update-permission',
            'delete-permission',
        ],
        'settings' => [
            'view-settings',
            'update-settings',
        ],
    ];

    /**
     * Default roles structure
     */
    protected array $roles = [
        [
            'name' => 'super-admin',
            'guard_name' => 'web',
            'dashboard_route' => 'admin.dashboard',
            'description' => 'Super Administrator with full system access',
            'is_system_role' => true,
            'permissions' => '*', // All permissions
        ],
        [
            'name' => 'admin',
            'guard_name' => 'web',
            'dashboard_route' => 'admin.dashboard',
            'description' => 'System Administrator',
            'is_system_role' => true,
            'permissions' => [
                'user' => ['view-any-user', 'view-user', 'create-user', 'update-user'],
                'role' => ['view-any-role', 'view-role'],
                'permission' => ['view-any-permission', 'view-permission'],
                'settings' => ['view-settings'],
            ],
        ],
        [
            'name' => 'manager',
            'guard_name' => 'web',
            'dashboard_route' => 'manager.dashboard',
            'description' => 'Content Manager',
            'is_system_role' => false,
            'permissions' => [
                'user' => ['view-any-user', 'view-user'],
                'settings' => ['view-settings'],
            ],
        ],
    ];

    public function run(): void
    {
        // Reset cached roles and permissions
        app()[\Spatie\Permission\PermissionRegistrar::class]->forgetCachedPermissions();

        $this->createPermissions();
        $this->createRoles();
    }

    /**
     * Create permissions from structure
     */
    protected function createPermissions(): void
    {
        foreach ($this->permissions as $group => $permissionNames) {
            foreach ($permissionNames as $permissionName) {
                Permission::firstOrCreate([
                    'name' => $permissionName,
                    'guard_name' => 'web',
                ], [
                    'group' => $group,
                    'description' => $this->getPermissionDescription($permissionName),
                    'is_system_permission' => true,
                ]);
            }
        }
    }

    /**
     * Create roles and assign permissions
     */
    protected function createRoles(): void
    {
        foreach ($this->roles as $roleData) {
            $permissions = $roleData['permissions'];
            unset($roleData['permissions']);

            $role = Role::firstOrCreate(
                ['name' => $roleData['name']],
                $roleData
            );

            if ($permissions === '*') {
                $role->syncPermissions(Permission::all());
            } else {
                $permissionNames = collect($permissions)
                    ->flatten()
                    ->unique()
                    ->toArray();
                
                $permissions = Permission::whereIn('name', $permissionNames)->get();
                $role->syncPermissions($permissions);
            }
        }
    }

    /**
     * Get permission description from name
     */
    protected function getPermissionDescription(string $permissionName): string
    {
        $descriptions = [
            'view-any' => 'View any :resource',
            'view' => 'View :resource',
            'create' => 'Create :resource',
            'update' => 'Update :resource',
            'delete' => 'Delete :resource',
            'restore' => 'Restore deleted :resource',
            'force-delete' => 'Permanently delete :resource',
            'assign-role-permissions' => 'Assign permissions to roles',
            'view-role-users' => 'View users assigned to roles',
            'view-settings' => 'View system settings',
            'update-settings' => 'Update system settings',
        ];

        foreach ($descriptions as $key => $description) {
            if (Str::startsWith($permissionName, $key)) {
                $resource = Str::replaceFirst($key . '-', '', $permissionName);
                $resource = Str::singular($resource);
                return __($description, ['resource' => $resource]);
            }
        }

        return ucwords(str_replace('-', ' ', $permissionName));
    }
}