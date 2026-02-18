<?php

namespace Database\Seeders;

use App\Enums\RoleEnum;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;

class SuperAdminSeeder extends Seeder
{
    /**
     * Run the database seeds.
     * Creates SuperAdmin user using raw SQL.
     */
    public function run(): void
    {
        $email = 'superadmin@gmail.com';

        $superAdmin = User::firstOrCreate(
            ['email' => $email],
            [
                'name' => 'Super Admin',
                'password' => Hash::make('password'),
                'email_verified_at' => now(),
                'company_id' => null,
                'is_active' => true,
            ]
        );

        if (!$superAdmin->wasRecentlyCreated) {
            $this->command->info('SuperAdmin already exists!');
            return;
        }

        $now = now();

        $role = DB::selectOne('SELECT id FROM roles WHERE name = ? AND guard_name = ? LIMIT 1', [RoleEnum::SUPER_ADMIN->value, 'web']);

        if (!$role) {
            DB::insert(
                'INSERT INTO roles (name, guard_name, created_at, updated_at) VALUES (?, ?, ?, ?)',
                [RoleEnum::SUPER_ADMIN->value, 'web', $now, $now]
            );
            $role = DB::selectOne('SELECT id FROM roles WHERE name = ? AND guard_name = ? LIMIT 1', [RoleEnum::SUPER_ADMIN->value, 'web']);
        }

        $alreadyAssigned = DB::selectOne(
            'SELECT 1 FROM model_has_roles WHERE role_id = ? AND model_type = ? AND model_id = ? LIMIT 1',
            [$role->id, User::class, $superAdmin->id]
        );

        if (!$alreadyAssigned) {
            DB::insert(
                'INSERT INTO model_has_roles (role_id, model_type, model_id) VALUES (?, ?, ?)',
                [$role->id, User::class, $superAdmin->id]
            );
        }

        $this->command->info('SuperAdmin created successfully!');
        $this->command->info('Email: superadmin@gmail.com');
        $this->command->info('Password: password');
    }
}
