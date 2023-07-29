<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;

class AdminUserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Check if the admin role and permissions have already been created
        if (!$adminUser = User::where('name', 'Filipe')->first()) {

            // Create admin user
            $adminUser = User::create([
                'name' => 'Filipe',
                'email' => 'admin@example.com',
                'password' => bcrypt('password'), // Replace 'password' with the desired password
            ]);
        }

        // Create admin role
        $adminRole = Role::findByName('admin');

        // Assign admin role to the admin user
        $adminUser->assignRole($adminRole);
    }
}
