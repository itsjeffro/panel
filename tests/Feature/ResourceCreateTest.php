<?php

namespace Itsjeffro\Panel\Tests\Feature;

use Itsjeffro\Panel\Tests\TestCase;

class ResourceCreateTest extends TestCase
{
    public function test_500_query_exception()
    {
        $response = $this->json(
            'POST',
            route('panel.resources.store', ['resource' => 'users']),
            [
                'email' => 'hello@itsjeffro.com',
            ]
        );

        $response
            ->assertStatus(500)
            ->assertJsonStructure([
                'message',
            ]);
    }

    public function test_user_can_create_resource()
    {
        $response = $this->json(
            'POST',
            route('panel.resources.store', ['resource' => 'users']),
            [
                'name' => 'Jeffro',
                'email' => 'hello@itsjeffro.com',
                'password' => bcrypt('demo123')
            ]
        );

        $response->assertStatus(201);
    }
}
