<?php

namespace Tests\Feature\Compendium;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Str;
use Spatie\Permission\Models\Permission;
use Tests\TestCase;

class CompendiumCreateTest extends TestCase
{
    use RefreshDatabase;

    public function test_it_returns_unauthorized_if_user_not_logged_in(): void
    {
        $response = $this->postJson('/api/compendia');

        $response->assertUnauthorized();
    }

    /** @dataProvider forbiddenRoles */
    public function test_it_returns_forbidden_if_user_role_not_allowed_to_create($role): void
    {
        $user = User::factory()->create();
        if ($role) {
            $user->assignRole($role);
        }

        $response = $this->actingAs($user)
                         ->postJson('/api/compendia');

        $response->assertForbidden();
    }

    public static function forbiddenRoles() {
        return [
            [null], ['gameMaster'], ['player']
        ];
    }

    /** @dataProvider validationDataProvider */
    public function test_it_returns_unprocessable_if_validation_failed($payload, $errors): void
    {
        $response = $this->asAdmin()
                         ->postJson('/api/compendia', $payload);

        $response->assertUnprocessable();

        $response->assertInvalid($errors);

        $this->assertDatabaseEmpty('compendia');

    }

    public static function validationDataProvider()
    {
        return [
            'name not present' => [[], ['name' => 'The name field is required.']],
            'name empty' => [['name' => ''], ['name' => 'The name field is required.']],
            'name not a string' => [['name' => ['an', 'array']], ['name' => 'The name field must be a string.']],
            'name longer than 255 characters' => [['name' => Str::random(256)], ['name' => 'The name field must not be greater than 255 characters.']],
            'content not a string' => [['content' => ['an', 'array']], ['content' => 'The content field must be a string.']],
            'content longer than 255 characters' => [['content' => Str::random(65536)], ['content' => 'The content field must not be greater than 65535 characters.']],
        ];
    }

    public function test_it_returns_compendia_if_successful(): void
    {
        $user = User::factory()->create();
        $user->givePermissionTo('compendia.create');

        $response = $this->actingAs($user)
                         ->postJson('/api/compendia?include=creator', [
                             'name' => 'D&D',
                             'content' => ($content = Str::random(65535)),
                         ]);

        $response->assertSuccessful();

        $response->assertJson([
            'data' => [
                'name' => 'D&D',
                'content' => $content,
                'creator' => [
                    'id' => $user->id,
                    'name' => $user->name,
                    'email' => $user->email
                ]
            ]
        ]);

        $this->assertDatabaseHas('compendia', [
            'name' => 'D&D',
            'creator_id' => $user->getKey(),
            'content' => $content,
        ]);

    }

    /** @dataProvider allowedRoles */
    public function test_it_returns_successful_if_user_role_allowed_to_create($role): void
    {
        $user = User::factory()->create();
        $user->assignRole($role);

        $response = $this->actingAs($user)
                         ->postJson('/api/compendia', [
                             'name' => 'D&D',
                             'content' => Str::random(65535),
                         ]);

        $response->assertSuccessful();
    }

    public static function allowedRoles()
    {
        return [['admin', 'writer']];
    }

    public function test_it_creates_permissions(): void
    {
        $response = $this->asAdmin()
            ->postJson('/api/compendia', [
                'name' => 'D&D',
                'content' => Str::random(65535),
            ]);

        $response->assertSuccessful();

        $this->assertNotNull(Permission::findByName("compendia.view.{$response->json('data.id')}"));
        $this->assertNotNull(Permission::findByName("compendia.update.{$response->json('data.id')}"));
        $this->assertNotNull(Permission::findByName("compendia.delete.{$response->json('data.id')}"));
    }
}
