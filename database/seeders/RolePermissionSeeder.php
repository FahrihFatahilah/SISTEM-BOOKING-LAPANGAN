<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;

class RolePermissionSeeder extends Seeder
{
    public function run(): void
    {
        // Reset cached roles and permissions
        app()[\Spatie\Permission\PermissionRegistrar::class]->forgetCachedPermissions();

        // Create permissions
        $permissions = [
            // Branch permissions
            'view branches',
            'create branches',
            'edit branches',
            'delete branches',
            
            // Field permissions
            'view fields',
            'create fields',
            'edit fields',
            'delete fields',
            
            // Booking permissions
            'view bookings',
            'create bookings',
            'edit bookings',
            'delete bookings',
            
            // User permissions
            'view users',
            'create users',
            'edit users',
            'delete users',
            
            // Report permissions
            'view reports',
            'export reports',
            
            // Live booking permissions
            'view live bookings',
        ];

        foreach ($permissions as $permission) {
            Permission::create(['name' => $permission]);
        }

        // Create roles and assign permissions
        
        // Owner role - full access
        $ownerRole = Role::create(['name' => 'owner']);
        $ownerRole->givePermissionTo(Permission::all());

        // Admin role - manage branch operations
        $adminRole = Role::create(['name' => 'admin']);
        $adminRole->givePermissionTo([
            'view branches',
            'view fields',
            'create fields',
            'edit fields',
            'view bookings',
            'create bookings',
            'edit bookings',
            'view users',
            'create users',
            'edit users',
            'view reports',
            'export reports',
            'view live bookings',
        ]);

        // Staff role - limited access
        $staffRole = Role::create(['name' => 'staff']);
        $staffRole->givePermissionTo([
            'view fields',
            'view bookings',
            'create bookings',
            'view live bookings',
        ]);
    }
}