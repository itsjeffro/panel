<?php

namespace Itsjeffro\Panel\Tests\Feature;

use Itsjeffro\Panel\Tests\TestCase;
use Itsjeffro\Panel\Tests\Models\User;

class ResourceUpdateTest extends TestCase
{
    public function test_404_returned_when_resource_model_does_not_exist()
    {
        $response = $this->json(
            'PUT',
            route('panel.resources.update', ['resource' => 'users', 'id' => '1'])
        );

        $response->assertStatus(404);
    }

    public function test_user_can_update_resource()
    {
        $user = factory(User::class)->create();

        $response = $this->json(
            'PUT',
            route('panel.resources.update', ['resource' => 'users', 'id' => $user->getKey()]),
            [
                'name' => 'Jeffro',
                'email' => 'hello@itsjeffro.com',
            ]
        );

        $response->assertStatus(200);
    }
}
