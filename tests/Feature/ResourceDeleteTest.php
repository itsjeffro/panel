<?php

namespace Itsjeffro\Panel\Tests\Feature;

use Itsjeffro\Panel\Tests\Models\User;
use Itsjeffro\Panel\Tests\TestCase;

class ResourceDeleteTest extends TestCase
{
    public function test_404_returned_when_resource_model_does_not_exist()
    {
        $response = $this->json('DELETE', route('panel.resources.destroy', [
            'resource' => 'users',
            'id' => '1',
        ]));

        $response->assertStatus(404);
    }

    public function test_user_can_delete_resource()
    {
        $user = new User();
        $user->name = 'Demo';
        $user->email = 'demo@demo.com';
        $user->password = bcrypt('demo123');
        $user->save();

        $response = $this->json('DELETE', route('panel.resources.destroy', [
            'resource' => 'users',
            'id' => $user->getKey(),
        ]));

        $response->assertStatus(204);
    }
}
