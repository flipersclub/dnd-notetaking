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
        $this->createRoles();

        $this->createPermissions();

        $this->doAdminRole();

        $this->doOtherRoles();
    }

    private function createRoles(): void
    {
        $roles = ['admin', 'writer', 'gameMaster', 'player'];

        foreach ($roles as $role) {
            Role::findOrCreate($role);
        }
    }

    private function createPermissions(): void
    {
        $objectTypes = ['systems', 'locations', 'compendia', 'campaigns', 'characters', 'monsters', 'languages', 'maps', 'items', 'tags', 'sessions', 'notebooks', 'permissions'];
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
    }

    private function doAdminRole(): void
    {
        // Get all permissions
        $permissions = Permission::pluck('id')->toArray();

        // Create admin role
        $role = Role::findByName('admin');

        // Assign the admin role to the admin user
        $role->permissions()->sync($permissions);
    }

    private function doOtherRoles(): void
    {
        $roles = [
            'writer' => ['compendia.create', 'tags.create', 'notebooks.create'],
            'gameMaster' => ['campaigns.create', 'tags.create', 'notebooks.create'],
            'player' => ['notebooks.create'],
        ];

        foreach ($roles as $roleName => $permissions) {
            // Create writer role
            $role = Role::findByName($roleName);

            $role->givePermissionTo(
                ...$permissions
            );
        }

    }

}
