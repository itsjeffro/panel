<?php

namespace Itsjeffro\Panel\Tests\Feature;

use Itsjeffro\Panel\Tests\TestCase;

class ResourceFieldShowTest extends TestCase
{
    public function test_user_can_see_resource_fields()
    {
        $response = $this->json(
            'GET',
            route('panel.resources.fields.show', ['resource' => 'users',])
        );

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
                                    'attribute' => 'name',
                                    'name' => 'Name',
                                    'value' => null,
                                ],
                                'resource' => 'users',
                                'resourceId' => null,
                                'resourceName' => null,
                                'relationship' => null,
                            ]
                        ],
                    ],
                    'timestamps' => [
                        'name' => 'Timestamps',
                        'resourceFields' => [],
                    ],
                ],
                'name' => [
                    'plural' => 'Users',
                    'singular' => 'User',
                ]
            ]);
    }
}
