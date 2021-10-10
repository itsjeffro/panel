<?php

namespace Itsjeffro\Panel\Tests\Feature;

use Itsjeffro\Panel\Tests\TestCase;
use Itsjeffro\Panel\Tests\Models\User;

class ResourceShowTest extends TestCase
{
    public function test_404_returned_when_resource_model_does_not_exist()
    {
        $response = $this->json(
            'GET',
            route('panel.resources.show', ['resource' => 'users', 'id' => '1'])
        );

        $response->assertStatus(404);
    }

    public function test_user_can_see_resource()
    {
        $user = factory(User::class)->create();

        $response = $this->json(
            'GET',
            route('panel.resources.show', ['resource' => 'users', 'id' => $user->getKey()])
        );

        $response
            ->assertStatus(200)
            ->assertJson([
                'data' => [
                    [
                        'block' => null,
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
                    ],
                    [
                        'block' => null,
                        'component' => 'Text',
                        'field' => [
                            'attribute' => 'name',
                            'name' => 'Name',
                            'value' => $user->name,
                        ],
                        'resource' => 'users',
                        'resourceId' => $user->getKey(),
                        'resourceName' => $user->name,
                        'relationship' => null,
                    ],
                    [
                        'block' => null,
                        'component' => 'Text',
                        'field' => [
                            'attribute' => 'email',
                            'name' => 'Email',
                            'value' => $user->email,
                        ],
                        'resource' => 'users',
                        'resourceId' => $user->getKey(),
                        'resourceName' => $user->email,
                        'relationship' => null,
                    ],
                    [
                        'block' => 'Timestamps',
                        'component' => 'DateTime',
                        'field' => [
                            'attribute' => 'created_at',
                            'name' => 'Created At',
                        ],
                        'resource' => 'users',
                    ],
                    [
                        'block' => 'Timestamps',
                        'component' => 'DateTime',
                        'field' => [
                            'attribute' => 'updated_at',
                            'name' => 'Updated At',
                        ],
                        'resource' => 'users',
                    ]
                ]
            ]);
    }
}
