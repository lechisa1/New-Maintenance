<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;

class RolesAndPermissionsSeeder extends Seeder
{
    public function run(): void
    {
        app()[\Spatie\Permission\PermissionRegistrar::class]->forgetCachedPermissions();

        // ===============================
        // USER MANAGEMENT
        // ===============================
        Permission::create(['name' => 'user.view']);
        Permission::create(['name' => 'user.create']);
        Permission::create(['name' => 'user.edit']);
        Permission::create(['name' => 'user.delete']);

        // ===============================
        // ROLE MANAGEMENT
        // ===============================
        Permission::create(['name' => 'role.view']);
        Permission::create(['name' => 'role.create']);
        Permission::create(['name' => 'role.edit']);
        Permission::create(['name' => 'role.delete']);

        // ===============================
        // ORGANIZATION MANAGEMENT
        // ===============================
        Permission::create(['name' => 'organization.view']);
        Permission::create(['name' => 'organization.create']);
        Permission::create(['name' => 'organization.edit']);
        Permission::create(['name' => 'organization.delete']);

        // ===============================
        // MAINTENANCE REQUEST
        // ===============================
        Permission::create(['name' => 'maintenance.create']);
        Permission::create(['name' => 'maintenance.view']);
        Permission::create(['name' => 'maintenance.assign']);
        Permission::create(['name' => 'maintenance.approve']);
        Permission::create(['name' => 'maintenance.reject']);
        Permission::create(['name' => 'maintenance.complete']);

        // ===============================
        // ROLES
        // ===============================
        $ictDirector = Role::create(['name' => 'ict_director']);
        $technician = Role::create(['name' => 'technician']);
        $employee = Role::create(['name' => 'employee']);

        // ===============================
        // ROLE → PERMISSIONS
        // ===============================
        $ictDirector->givePermissionTo(Permission::all());

        $technician->givePermissionTo([
            'maintenance.view',
            'maintenance.assign',
            'maintenance.complete',
        ]);

        $employee->givePermissionTo([
            'maintenance.create',
            'maintenance.view',
        ]);
    }
}
