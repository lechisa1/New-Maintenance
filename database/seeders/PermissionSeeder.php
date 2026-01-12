<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;
use Illuminate\Support\Facades\DB;

class PermissionSeeder extends Seeder
{
    public function run(): void
    {
        // Clear cached permissions
        app()[\Spatie\Permission\PermissionRegistrar::class]->forgetCachedPermissions();

        DB::transaction(function () {

            /*
            |--------------------------------------------------------------------------
            | RESOURCES & ACTIONS
            |--------------------------------------------------------------------------
            */
            $resources = [
                'users',
                'roles',
                'organizations',
                'maintenance_requests',
                'departments',
                'divisions',
                'reports',
            ];

            $actions = [
                'view',
                'create',
                'update',
                'delete',
                'approve',
                'assign',
            ];

            /*
            |--------------------------------------------------------------------------
            | CREATE PERMISSIONS
            |--------------------------------------------------------------------------
            */
            foreach ($resources as $resource) {
                foreach ($actions as $action) {
                    Permission::firstOrCreate([
                        'name' => "{$resource}.{$action}",
                        'guard_name' => 'web',
                    ]);
                }
            }

            /*
            |--------------------------------------------------------------------------
            | EXTRA SYSTEM PERMISSIONS
            |--------------------------------------------------------------------------
            */
            $extraPermissions = [
                'dashboard.view',
                'settings.manage',
                'profile.update',
            ];

            foreach ($extraPermissions as $permission) {
                Permission::firstOrCreate([
                    'name' => $permission,
                    'guard_name' => 'web',
                ]);
            }

            /*
            |--------------------------------------------------------------------------
            | ROLES
            |--------------------------------------------------------------------------
            */
            $superAdmin = Role::firstOrCreate(['name' => 'superadmin']);
            $admin      = Role::firstOrCreate(['name' => 'admin']);
            $manager    = Role::firstOrCreate(['name' => 'manager']);
            $employee   = Role::firstOrCreate(['name' => 'employee']);

            /*
            |--------------------------------------------------------------------------
            | ROLE → PERMISSION ASSIGNMENT
            |--------------------------------------------------------------------------
            */

            // Super Admin → ALL permissions
            $superAdmin->syncPermissions(Permission::all());

            // Admin
            $admin->syncPermissions([
                'dashboard.view',
                'users.view',
                'users.create',
                'users.update',
                'roles.view',
                'roles.create',
                'roles.update',
                'organizations.view',
                'organizations.create',
                'organizations.update',
                'maintenance_requests.view',
                'maintenance_requests.assign',
                'maintenance_requests.approve',
                'reports.view',
            ]);

            // Manager
            $manager->syncPermissions([
                'dashboard.view',
                'maintenance_requests.view',
                'maintenance_requests.assign',
                'maintenance_requests.approve',
                'reports.view',
            ]);

            // Employee
            $employee->syncPermissions([
                'dashboard.view',
                'maintenance_requests.view',
                'maintenance_requests.create',
                'maintenance_requests.update',
                'profile.update',
            ]);
        });
    }
}
