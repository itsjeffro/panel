<?php

namespace Itsjeffro\Panel\Tests\Feature;

use Itsjeffro\Panel\Tests\TestCase;
use Itsjeffro\Panel\Tests\Models\User;

class ResourceCreateTest extends TestCase
{
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
