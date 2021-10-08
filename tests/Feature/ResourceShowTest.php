<?php

namespace Itsjeffro\Panel\Tests\Feature;

use Itsjeffro\Panel\Tests\TestCase;
use Itsjeffro\Panel\Tests\Models\User;

class ResourceShowTest extends TestCase
{
    public function test_404_returned_when_resource_model_does_not_exist()
    {
        $response = $this->json('GET', route('panel.resources.show', [
            'resource' => 'users',
            'id' => '1',
        ]));

        $response->assertStatus(404);
    }

    public function test_user_can_see_resource()
    {
        $user = new User();
        $user->name = 'Demo';
        $user->email = 'demo@demo.com';
        $user->password = bcrypt('demo123');
        $user->save();

        $response = $this->json('GET', route('panel.resources.show', [
            'resource' => 'users',
            'id' => $user->getKey(),
        ]));

        $response
            ->assertStatus(200)
            ->assertJson([
                'groups' => [
                    'general' => [
                        'name' => 'User Details',
                        'resourceFields' => [
                            [
                                'component' => 'Text',
                                'field' => [
                                    'attribute' => 'id',
                                    'name' => 'ID',
                                    'value' => $user->getKey(),
                                ],
                                'resource' => 'users',
                                'resourceId' => $user->getKey(),
                                'resourceName' => $user->getKey(),
                                'relationship' => null,
                            ]
                        ],
                    ],
                    'timestamps' => [
                        'name' => 'Timestamps',
                        'resourceFields' => [],
                    ],
                ]
            ]);
    }
}
