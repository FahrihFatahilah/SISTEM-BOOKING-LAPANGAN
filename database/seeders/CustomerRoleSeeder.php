<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;

class CustomerRoleSeeder extends Seeder
{
    public function run(): void
    {
        // Create customer role if it doesn't exist
        if (!Role::where('name', 'customer')->exists()) {
            $customerRole = Role::create(['name' => 'customer']);
            
            // Give basic permissions to customer
            $permissions = ['view bookings'];
            foreach ($permissions as $permission) {
                if (Permission::where('name', $permission)->exists()) {
                    $customerRole->givePermissionTo($permission);
                }
            }
            
            $this->command->info('Customer role created successfully.');
        } else {
            $this->command->info('Customer role already exists.');
        }
    }
}