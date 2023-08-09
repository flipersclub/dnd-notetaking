<?php

namespace Tests;

use App\Models\User;
use Illuminate\Foundation\Testing\TestCase as BaseTestCase;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;

abstract class TestCase extends BaseTestCase
{
    use CreatesApplication;

    protected $seed = true;

    public function signedIn()
    {
        $user = User::factory()->create();
        return $this->actingAs($user);
    }

    public function asAdmin()
    {
        $user = User::find(1);
        return $this->actingAs($user);
    }

    public function userWithPermission(string $permission)
    {
        $permission = Permission::create(['name' => $permission]);
        $user = User::factory()->create();
        $user->givePermissionTo($permission);
        return $user;
    }

    public function userWithRole(string $permission, string $role)
    {
        $permission = Permission::create(['name' => $permission]);
        $role = Role::create(['name' => $role]);
        $role->givePermissionTo($permission);
        $user = User::factory()->create();
        $user->assignRole($role);
        return $user;
    }
}
