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

    public function test_user_can_list_resources()
    {
        Panel::resources([
            User::class,
        ]);

        $response = $this->json('GET', route('panel.resources.index', ['resource' => 'users']));

        $response
            ->assertStatus(200)
            ->assertJsonStructure([
                'data',
                'meta' => [
                    'actions',
                    'fields',
                    'name',
                ],
                'links',
            ]);
    }
}
