<?php

namespace Itsjeffro\Panel\Tests\Feature;

use Itsjeffro\Panel\Tests\TestCase;
use Itsjeffro\Panel\Tests\Models\User;

class ResourceEditTest extends TestCase
{
    public function test_404_returned_when_resource_model_does_not_exist()
    {
        $response = $this->json(
            'GET',
            route('panel.resources.edit', ['resource' => 'users', 'id' => '1'])
        );

        $response->assertStatus(404);
    }

    public function test_user_can_see_editable_resource_fields_and_data()
    {
        $user = factory(User::class)->create();

        $response = $this->json(
            'GET',
            route('panel.resources.edit', ['resource' => 'users', 'id' => $user->getKey()]),
        );

        $response->assertStatus(200);
    }
}
