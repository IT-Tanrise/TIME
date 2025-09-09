<?php
// database/seeders/RolePermissionSeeder.php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;
use App\Models\User;

class RolePermissionSeeder extends Seeder
{
    public function run()
    {
        // Reset cached roles and permissions
        app()[\Spatie\Permission\PermissionRegistrar::class]->forgetCachedPermissions();

        // Create permissions
        $permissions = [
            // User permissions
            'users.index',
            'users.create',
            'users.show',
            'users.edit',
            'users.delete',
            
            // Role permissions
            'roles.index',
            'roles.create',
            'roles.show',
            'roles.edit',
            'roles.delete',
            
            // Permission permissions
            'permissions.index',
            'permissions.create',
            'permissions.show',
            'permissions.edit',
            'permissions.delete',
            
            // Dashboard permissions
            'dashboard.access',
            'admin.access',// parent access
        ];

        foreach ($permissions as $permission) {
            Permission::create(['name' => $permission]);
        }

        // Create roles
        $superAdminRole = Role::create(['name' => 'Super Admin']);
        $adminRole = Role::create(['name' => 'Admin']);
        $userRole = Role::create(['name' => 'User']);

        // Assign permissions to roles
        $superAdminRole->givePermissionTo(Permission::all());
        
        $adminRole->givePermissionTo([
            'users.index',
            'users.show',
            'users.edit',
            'dashboard.access',
            'admin.access',
        ]);

        $userRole->givePermissionTo([
            'dashboard.access',
        ]);

        // Create super admin user
        $superAdmin = User::create([
            'name' => 'Super Admin',
            'email' => 'superadmin@example.com',
            'password' => bcrypt('password'),
            'email_verified_at' => now(),
        ]);

        $superAdmin->assignRole('Super Admin');

        // Create admin user
        $admin = User::create([
            'name' => 'Admin User',
            'email' => 'admin@example.com',
            'password' => bcrypt('password'),
            'email_verified_at' => now(),
        ]);

        $admin->assignRole('Admin');

        // Create normal user
        $user = User::create([
            'name' => 'Normal User',
            'email' => 'user@example.com',
            'password' => bcrypt('password'),
            'email_verified_at' => now(),
        ]);

        $user->assignRole('User');
    }
}