<?php

namespace Database\Seeders;

use App\Enums\RoleEnum;
use Illuminate\Database\Seeder;
use Spatie\Permission\PermissionRegistrar;
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
        app(PermissionRegistrar::class)->forgetCachedPermissions();

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
            Permission::firstOrCreate(['name' => $permission, 'guard_name' => 'web']);
        }

        // Define roles with their permissions
        $rolesWithPermissions = [
            RoleEnum::SUPER_ADMIN->value => [
                'manage-companies',
                'manage-users',
                'invite-users',
                'view-short-urls',
                'view-analytics',
            ],
            RoleEnum::ADMIN->value => [
                'create-short-urls',
                'view-short-urls',
                'invite-users',
                'manage-users',
                'view-analytics',
            ],
            RoleEnum::MEMBER->value => [
                'create-short-urls',
                'view-short-urls',
            ],
            RoleEnum::MANAGER->value => [
                'create-short-urls',
                'view-short-urls',
                'edit-short-urls',
                'delete-short-urls',
                'view-analytics',
            ],
        ];

        // Create roles and assign permissions
        foreach ($rolesWithPermissions as $roleName => $rolePermissions) {
            $role = Role::firstOrCreate(['name' => $roleName, 'guard_name' => 'web']);
            $role->syncPermissions($rolePermissions);
        }

        $this->command->info('Roles and permissions created successfully!');
        $this->command->info('Roles: ' . implode(', ', array_keys($rolesWithPermissions)));
    }
}
