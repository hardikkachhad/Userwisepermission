<?php

namespace Database\Seeders;

use App\Models\Permission;
use App\Models\Role;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class AdminSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // 1. Ensure 'admin' role exists
        $adminRole = Role::firstOrCreate(
            ['slug' => 'admin'],
            [
                'name'        => 'Administrator',
                'description' => 'Super Administrator with full privileges across the platform.',
            ]
        );

        // 2. Attach all available permissions to the admin role
        $allPermissions = Permission::all();
        if ($allPermissions->isNotEmpty()) {
            $adminRole->permissions()->syncWithoutDetaching($allPermissions->pluck('id'));
        }

        // 3. Create or retrieve Admin user
        $admin = User::firstOrCreate(
            ['email' => 'admin@example.com'],
            [
                'name'              => 'System Admin',
                'email_verified_at' => now(),
                'password'          => Hash::make('password123'),
            ]
        );

        // 4. Assign the 'admin' role to the admin user
        $admin->assignRole($adminRole);
    }
}
