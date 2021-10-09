<?php

namespace Itsjeffro\Panel\Tests\Feature;

use Itsjeffro\Panel\Tests\Models\User;
use Itsjeffro\Panel\Tests\TestCase;

class ResourceActionTest extends TestCase
{
    public function test_user_can_use_resource_action()
    {
        $users = factory(User::class, 2)->create();

        $response = $this->json(
            'POST',
            route('panel.resources.actions.handle', ['resource' => 'users', 'action' => 'bulk-delete']),
            [
                'model_ids' => [
                    $users->first()->getKey(),
                ],
            ]
        );

        $response->assertStatus(200);

        $this->assertDatabaseCount('users', 1);
    }
}
