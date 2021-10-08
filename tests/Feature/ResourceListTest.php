<?php

namespace Itsjeffro\Panel\Tests\Feature;

use Itsjeffro\Panel\Panel;
use Itsjeffro\Panel\Tests\TestCase;
use Itsjeffro\Panel\Tests\Resources\User;

class ResourceListTest extends TestCase
{
    public function test_throws_when_resource_is_not_registered()
    {
        $response = $this->json('GET', route('panel.resources.index', ['resource' => 'UNREGISTERED']));

        $response->assertStatus(500);
    }

    public function test_user_can_list_resource_models()
    {
        Panel::resources([
            User::class,
        ]);

        $response = $this->json('GET', route('panel.resources.index', ['resource' => 'users']));

        $response
            ->assertStatus(200)
            ->assertJson([
                'data' => [],
                'meta' => [
                    'actions' => [],
                    'fields' => [
                        ['name' => 'ID', 'attribute' => 'id'],
                        ['name' => 'Name', 'attribute' => 'name'],
                        ['name' => 'Email', 'attribute' => 'email'],
                        ['name' => 'Created At', 'attribute' => 'created_at'],
                        ['name' => 'Updated At', 'attribute' => 'updated_at'],
                    ],
                    'name' => [
                        'plural' => 'Users',
                        'singular' => 'User',
                    ],
                ],
            ]);
    }
}
