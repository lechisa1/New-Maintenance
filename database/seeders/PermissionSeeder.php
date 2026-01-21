<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use App\Models\Permission;
use App\Models\Role;
use Spatie\Permission\PermissionRegistrar;

class PermissionSeeder extends Seeder
{
    public function run(): void
    {
        // Clear cached permissions
        app(PermissionRegistrar::class)->forgetCachedPermissions();

        DB::transaction(function () {

            $guard = 'web';

            /*
            |--------------------------------------------------------------------------
            | RESOURCES & ACTIONS
            |--------------------------------------------------------------------------
            */
            $resources = [
                'users',
                'roles',
                'organizations',
                'departments',
                'divisions',
                'maintenance_requests',
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
                        'name'       => "{$resource}.{$action}",
                        'guard_name' => $guard,
                    ], [
                        'resource' => $resource,
                    ]);
                }
            }

            /*
            |--------------------------------------------------------------------------
            | EXTRA SYSTEM PERMISSIONS
            |--------------------------------------------------------------------------
            */
            $extras = [
                'dashboard.view' => 'dashboard',
                'profile.update' => 'profile',
                'settings.manage' => 'settings',
            ];

            foreach ($extras as $permission => $resource) {
                Permission::firstOrCreate([
                    'name'       => $permission,
                    'guard_name' => $guard,
                ], [
                    'resource' => $resource,
                ]);
            }

            /*
            |--------------------------------------------------------------------------
            | ROLES
            |--------------------------------------------------------------------------
            */
            $roles = [
                'superadmin',
                'admin',
                'manager',
                'employee',
            ];

            $roleModels = [];
            foreach ($roles as $role) {
                $roleModels[$role] = Role::firstOrCreate([
                    'name'       => $role,
                    'guard_name' => $guard,
                ]);
            }

            /*
            |--------------------------------------------------------------------------
            | ASSIGN PERMISSIONS TO ROLES
            |--------------------------------------------------------------------------
            */

            // Super Admin → Everything
            $roleModels['superadmin']->syncPermissions(Permission::all());

            // Admin → Limited permissions
            $roleModels['admin']->syncPermissions(Permission::whereIn('name', [
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
            ])->get());

            // Manager → Limited permissions
            $roleModels['manager']->syncPermissions(Permission::whereIn('name', [
                'dashboard.view',
                'maintenance_requests.view',
                'maintenance_requests.assign',
                'maintenance_requests.approve',
                'reports.view',
            ])->get());

            // Employee → Limited permissions
            $roleModels['employee']->syncPermissions(Permission::whereIn('name', [
                'dashboard.view',
                'maintenance_requests.view',
                'maintenance_requests.create',
                'maintenance_requests.update',
                'profile.update',
            ])->get());
        });

    }
}