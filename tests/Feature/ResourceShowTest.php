<?php

namespace Itsjeffro\Panel\Tests\Feature;

use Itsjeffro\Panel\Panel;
use Itsjeffro\Panel\Tests\TestCase;
use Itsjeffro\Panel\Tests\Resources\User;

class ResourceShowTest extends TestCase
{
    public function test_not_found_returned_when_resource_model_does_not_exist()
    {
        Panel::resources([
            User::class,
        ]);

        $response = $this->json('GET', route('panel.resources.show', [
            'resource' => 'users',
            'id' => '1',
        ]));

        $response->assertStatus(404);
    }

    public function test_resource_model_returned_successfully()
    {
        $user = new \Itsjeffro\Panel\Tests\Models\User();
        $user->name = 'Demo';
        $user->email = 'demo@demo.com';
        $user->password = bcrypt('demo123');
        $user->save();

        Panel::resources([
            User::class,
        ]);

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
