<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Spatie\Permission\Guard;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;

class PermissionSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {

        $objectTypes = ['systems', 'locations', 'compendia', 'campaigns', 'characters', 'monsters', 'languages', 'maps', 'items', 'tags', 'sessions', 'permissions'];
        $actions = ['create', 'view', 'update', 'delete'];

        $permissions = [];
        foreach ($objectTypes as $objectType) {
            foreach ($actions as $action) {
                $permissions[] = ['name' => "{$objectType}.{$action}", 'guard_name' => Guard::getDefaultName(Permission::class)];
            }
        }

        // Upsert the permission using the name as the unique identifier
        Permission::upsert(
            $permissions,
            ['name']
        );

        // Get all permissions
        $permissions = Permission::pluck('id')->toArray();

        // Create admin role
        $adminRole = Role::findOrCreate('admin');

        // Assign the admin role to the admin user
        $adminRole->permissions()->sync($permissions);
    }
}
