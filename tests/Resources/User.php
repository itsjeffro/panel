<?php

namespace Itsjeffro\Panel\Tests\Resources;

use Itsjeffro\Panel\Block;
use Itsjeffro\Panel\Fields\DateTime;
use Itsjeffro\Panel\Fields\ID;
use Itsjeffro\Panel\Fields\Password;
use Itsjeffro\Panel\Fields\Text;
use Itsjeffro\Panel\Resource;

class User extends Resource
{
    /**
     * The model that the resource corresponds with.
     *
     * @var string
     */
    public $model = 'Itsjeffro\Panel\Tests\Models\User';

    /**
     * The single value that is used to represent the resource when it is being displayed.
     *
     * @var string
     */
    public $title = 'name';

    /**
     * The columns that should be search from the resource.
     *
     * @return array
     */
    public $search = [
        'id',
        'name',
        'email',
    ];

    /**
     * Get the fields displayed by the resource.
     *
     * @return array
     */
    public function fields(): array
    {
        return [
            ID::make(),
            Text::make('Name'),
            Text::make('Email')->rules(['required']),
            Password::make('Password'),

            new Block('Timestamps', [
                DateTime::make('Created At')->hideFromCreate()->hideFromUpdate(),
                DateTime::make('Updated At')->hideFromCreate()->hideFromUpdate(),
            ]),
        ];
    }
}
