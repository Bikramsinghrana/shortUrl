<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;

class RoleAndPermissionSeeder extends Seeder
{
    /**
     * Run the database seeds.
     * Creates roles and permissions with proper structure.
     */
    public function run(): void
    {
        // Reset cached roles and permissions
        app()[\Spatie\Permission\PermissionRegistrar::class]->forgetCachedPermissions();

        // Define all permissions
        $permissions = [
            'create-short-urls',
            'view-short-urls',
            'edit-short-urls',
            'delete-short-urls',
            'view-all-company-short-urls',
            'invite-users',
            'manage-users',
            'manage-companies',
            'view-analytics',
        ];

        // Create permissions (skip if already exists)
        foreach ($permissions as $permission) {
            Permission::firstOrCreate(['name' => $permission]);
        }

        // Define roles with their permissions
        $rolesWithPermissions = [
            'SuperAdmin' => [
                'manage-companies',
                'view-all-company-short-urls',
                'manage-users',
                'invite-users',
                'view-analytics',
            ],
            'Admin' => [
                'create-short-urls',
                'view-all-company-short-urls',
                'edit-short-urls',
                'delete-short-urls',
                'invite-users',
                'manage-users',
                'view-analytics',
            ],
            'Member' => [
                'create-short-urls',
                'view-short-urls',
                'edit-short-urls',
                'delete-short-urls',
            ],
        ];

        // Create roles and assign permissions
        foreach ($rolesWithPermissions as $roleName => $rolePermissions) {
            $role = Role::firstOrCreate(['name' => $roleName]);
            $role->syncPermissions($rolePermissions);
        }

        $this->command->info('Roles and permissions created successfully!');
        $this->command->info('Roles: ' . implode(', ', array_keys($rolesWithPermissions)));
    }
}
