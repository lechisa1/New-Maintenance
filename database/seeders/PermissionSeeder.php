<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use App\Models\Permission;
use App\Models\Role;
use Spatie\Permission\PermissionRegistrar;
use App\Models\User;
use Illuminate\Support\Facades\Hash;

class PermissionSeeder extends Seeder
{
    public function run(): void
    {
        // Clear cached permissions
        app(PermissionRegistrar::class)->forgetCachedPermissions();

        DB::transaction(function () {

            $guard = 'web';

            /*
            |----------------------------------------------------------------------
            | RESOURCES & ACTIONS
            |----------------------------------------------------------------------
            */
            $resources = [
                // User & Role Management
                'users' => ['view', 'create', 'update', 'delete'],
                'roles' => ['view', 'assign', 'create', 'update', 'delete'],

                // Organization Units
                'organization_units' => ['view', 'create', 'update', 'delete', 'assign_head'],

                // Inventory & Equipment
                'equipment' => ['view', 'create', 'update', 'delete'],
                'issue_types' => ['view', 'create', 'update', 'delete'],

                // Maintenance System
                'maintenance_requests' => [
                    'view_unit',
                    'view_all',
                    'view_own',
                    'view_assigned',
                    'create',
                    'update',
                    'assign',
                    'reassign',
                    'approve',
                    'reject',
                    'resolve',
                    'confirm',
                    'reopen'
                ],

                // Reports
                'reports' => ['view', 'generate'],

                // System
                'dashboard' => ['view'],
                'profile' => ['view', 'update', 'password'],
                'settings' => ['view', 'update'],
            ];

            /*
            |----------------------------------------------------------------------
            | CREATE PERMISSIONS
            |----------------------------------------------------------------------
            */
            foreach ($resources as $resource => $actions) {
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
            |----------------------------------------------------------------------
            | ROLES
            |----------------------------------------------------------------------
            */
            $roles = [
                'superadmin',
                'admin',
                'chairman',
                'user',
                'ict_director',
                'technician'
            ];

            $roleModels = [];
            foreach ($roles as $role) {
                $roleModels[$role] = Role::firstOrCreate([
                    'name'       => $role,
                    'guard_name' => $guard,
                ]);
            }

            /*
            |----------------------------------------------------------------------
            | ASSIGN PERMISSIONS TO ROLES
            |----------------------------------------------------------------------
            */

            // Super Admin → Everything
            $roleModels['superadmin']->syncPermissions(Permission::all());

            // Admin → User, Role, Org, Maintenance & Reports
            $roleModels['admin']->syncPermissions(Permission::whereIn('name', [
                'dashboard.view',
                'users.view',
                'users.create',
                'users.update',
                'users.delete',
                'roles.view',
                'roles.assign',
                'roles.create',
                'roles.update',
                'roles.delete',
                'organization_units.view',
                'organization_units.create',
                'organization_units.update',
                'organization_units.assign_head',
                'equipment.view',
                'equipment.create',
                'equipment.update',
                'equipment.delete',
                'issue_types.view',
                'issue_types.create',
                'issue_types.update',
                'issue_types.delete',
                'maintenance_requests.view_all',
                'maintenance_requests.assign',
                'maintenance_requests.reassign',
                'maintenance_requests.approve',
                'maintenance_requests.reject',
                'reports.view',
                'reports.generate',
                'settings.view',
                'settings.update',
            ])->get());
            /*
            |--------------------------------------------------------------------------
            | CREATE DEFAULT USERS
            |--------------------------------------------------------------------------
            */
            $superAdmin = User::firstOrCreate(
                ['email' => 'superAdmin@example.com'],
                [
                    'full_name' => 'Super Admin',
                    'phone'     => '2345678901',
                    'password'  => Hash::make('password123'),
                ]
            );

            $superAdmin->syncRoles([$roleModels['superadmin']]);

            $admin = User::firstOrCreate(
                ['email' => 'admin@example.com'],
                [
                    'full_name' => 'Admin User',
                    'phone'     => '2345678902',
                    'password'  => Hash::make('password123'),
                ]
            );

            $admin->syncRoles([$roleModels['admin']]);
            // Admin → User, Role, Org, Maintenance & Reports
            $roleModels['ict_director']->syncPermissions(Permission::whereIn('name', [
                'dashboard.view',
                'users.view',
                'users.create',
                'users.update',
                'users.delete',
                'roles.view',
                'roles.assign',
                'roles.create',
                'roles.update',
                'roles.delete',
                'organization_units.view',
                'organization_units.create',
                'organization_units.update',
                'organization_units.assign_head',
                'equipment.view',
                'equipment.create',
                'equipment.update',
                'equipment.delete',
                'issue_types.view',
                'issue_types.create',
                'issue_types.update',
                'issue_types.delete',
                'maintenance_requests.view_all',
                'maintenance_requests.assign',
                'maintenance_requests.reassign',
                'maintenance_requests.approve',
                'maintenance_requests.reject',
                'reports.view',
                'reports.generate',
                'settings.view',
                'settings.update',
            ])->get());

            // Manager → Maintenance & Reports
            $roleModels['chairman']->syncPermissions(Permission::whereIn('name', [
                'dashboard.view',
                'maintenance_requests.view_unit',
                'maintenance_requests.approve',
                'maintenance_requests.reject',
                'reports.view',
            ])->get());

            // Employee → Own Maintenance Requests & Profile
            $roleModels['user']->syncPermissions(Permission::whereIn('name', [
                'dashboard.view',
                'maintenance_requests.view_own',
                'maintenance_requests.create',
                'maintenance_requests.update',
                'profile.view',
                'profile.update',
            ])->get());

            $roleModels['technician']->syncPermissions(Permission::whereIn('name', [
                'dashboard.view',
                'maintenance_requests.view_own',
                'maintenance_requests.create',
                'maintenance_requests.update',
                'profile.view',
                'profile.update',
            ])->get());
        });



        // Clear cached permissions again
        app(PermissionRegistrar::class)->forgetCachedPermissions();

        $this->command->info('✅ Permissions & roles seeded successfully.');
    }
}
