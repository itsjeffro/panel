<?php

namespace Itsjeffro\Panel\Tests\Feature;

use Itsjeffro\Panel\Tests\Models\User;
use Itsjeffro\Panel\Tests\TestCase;

class ResourceListTest extends TestCase
{
    public function test_throws_when_resource_is_not_registered()
    {
        $response = $this->json(
            'GET',
            route('panel.resources.index', ['resource' => 'UNREGISTERED'])
        );

        $response->assertStatus(500);
    }

    public function test_user_can_see_listed_resources()
    {
        $user = factory(User::class)->create();

        $response = $this->json(
            'GET',
            route('panel.resources.index', ['resource' => 'users'])
        );

        $response
            ->assertStatus(200)
            ->assertJson([
                'data' => [
                    [
                        'resourceId' => $user->getKey(),
                        'resourceName' => $user->name,
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
                            ],
                            [
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
                            ]
                        ],
                    ],
                ],
                'meta' => [
                    'actions' => [
                        ['name' => 'Bulk Delete', 'slug' => 'bulk-delete'],
                    ],
                    'fields' => [
                        ['name' => 'ID', 'attribute' => 'id'],
                        ['name' => 'Name', 'attribute' => 'name'],
                        ['name' => 'Email', 'attribute' => 'email'],
                        ['name' => 'Created At', 'attribute' => 'created_at'],
                        ['name' => 'Updated At', 'attribute' => 'updated_at'],
                    ],
                    'name' => [
                        'plural' => 'Users',
                        'singular' => 'User',
                    ],
                ],
            ]);
    }

    public function test_user_can_search_listed_resources()
    {
        $user1 = factory(User::class)->create();
        $user2 = factory(User::class)->create();

        $response = $this->json(
            'GET',
            route('panel.resources.index', ['resource' => 'users', 'search' => $user1->getKey()])
        );

        $jsonData = $response
            ->assertStatus(200)
            ->json('data');

        $this->assertCount(1, $jsonData);
        $this->assertSame($user1->name, $jsonData[0]['resourceName']);
    }
}
