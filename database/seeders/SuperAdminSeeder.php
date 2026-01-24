<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use Spatie\Permission\Models\Role;

class SuperAdminSeeder extends Seeder
{
    /**
     * Run the database seeds.
     * Creates SuperAdmin user using Eloquent models.
     */
    public function run(): void
    {
        // Check if SuperAdmin already exists
        $existingAdmin = User::where('email', 'admin@gmail.com')->first();

        if ($existingAdmin) {
            $this->command->info('SuperAdmin already exists!');
            return;
        }

        // Create SuperAdmin user using Eloquent
        $superAdmin = User::create([
            'name' => 'Super Admin',
            'email' => 'admin@gmail.com',
            'password' => Hash::make('password'),
            'email_verified_at' => now(),
            'company_id' => null,
            'is_active' => true,
        ]);

        // Get SuperAdmin role
        $role = Role::where('name', 'SuperAdmin')->first();

        // Assign SuperAdmin role using Spatie's method
        $superAdmin->assignRole($role);

        $this->command->info('SuperAdmin created successfully!');
        $this->command->info('Email: superadmin@urlshortener.com');
        $this->command->info('Password: password');
    }
}
