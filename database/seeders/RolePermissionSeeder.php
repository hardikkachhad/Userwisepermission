<?php

namespace Database\Seeders;

use App\Models\User;
use App\Models\Role;
use App\Models\Permission;
use App\Models\Product;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class RolePermissionSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // 1. Create Permissions
        $permissions = [
            'view-admin-dashboard' => 'View Admin Dashboard',
            'view-user-dashboard'  => 'View User Dashboard',
            'manage-users'         => 'Manage Users',
            'manage-roles'         => 'Manage Roles and Permissions',
            'view-product'         => 'View Products List and Details',
            'create-product'       => 'Create New Product',
            'edit-product'         => 'Edit Existing Product',
            'delete-product'       => 'Delete Product',
        ];

        $createdPermissions = [];
        foreach ($permissions as $slug => $name) {
            $createdPermissions[$slug] = Permission::firstOrCreate(
                ['slug' => $slug],
                ['name' => $name]
            );
        }

        // 2. Create Roles
        $adminRole = Role::firstOrCreate(
            ['slug' => 'admin'],
            [
                'name' => 'Administrator',
                'description' => 'Super Administrator with full privileges across the platform.',
            ]
        );

        $userRole = Role::firstOrCreate(
            ['slug' => 'user'],
            [
                'name' => 'Standard User',
                'description' => 'Regular user with viewing capabilities.',
            ]
        );

        // 3. Assign Permissions to Roles
        // Admin gets all permissions
        $adminRole->permissions()->sync(
            collect($createdPermissions)->pluck('id')->all()
        );

        // Standard User gets view permissions
        $userRole->permissions()->sync([
            $createdPermissions['view-user-dashboard']->id,
            $createdPermissions['view-product']->id,
        ]);

        // 4. Create Users and Assign Roles / Direct Permissions

        // A. Super Admin User
        $admin = User::firstOrCreate(
            ['email' => 'admin@example.com'],
            [
                'name'     => 'System Admin',
                'password' => Hash::make('password123'),
            ]
        );
        $admin->roles()->sync([$adminRole->id]);

        // B. Standard User (Only Role Permissions)
        $normalUser = User::firstOrCreate(
            ['email' => 'user@example.com'],
            [
                'name'     => 'Regular User',
                'password' => Hash::make('password123'),
            ]
        );
        $normalUser->roles()->sync([$userRole->id]);

        // C. Custom User-wise Permission Demo (Role is 'user', but directly granted 'create-product' & 'edit-product')
        $customUser = User::firstOrCreate(
            ['email' => 'manager@example.com'],
            [
                'name'     => 'Custom Permission User',
                'password' => Hash::make('password123'),
            ]
        );
        $customUser->roles()->sync([$userRole->id]);
        $customUser->permissions()->sync([
            $createdPermissions['create-product']->id,
            $createdPermissions['edit-product']->id,
        ]);

        // 5. Seed Sample Products
        Product::firstOrCreate(
            ['sku' => 'PROD-LAPTOP-01'],
            [
                'user_id'     => $admin->id,
                'name'        => 'Apple MacBook Pro M3',
                'price'       => 1999.99,
                'stock'       => 15,
                'description' => 'High performance laptop with 16GB RAM, 512GB SSD.',
            ]
        );

        Product::firstOrCreate(
            ['sku' => 'PROD-PHONE-02'],
            [
                'user_id'     => $admin->id,
                'name'        => 'Samsung Galaxy S24 Ultra',
                'price'       => 1199.50,
                'stock'       => 28,
                'description' => 'Flagship smartphone with 200MP camera and AI features.',
            ]
        );

        Product::firstOrCreate(
            ['sku' => 'PROD-HEADPHONE-03'],
            [
                'user_id'     => $customUser->id,
                'name'        => 'Sony WH-1000XM5 Headphones',
                'price'       => 349.00,
                'stock'       => 45,
                'description' => 'Industry-leading wireless noise-canceling headphones.',
            ]
        );
    }
}
