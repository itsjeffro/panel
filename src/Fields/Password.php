<?php

namespace Itsjeffro\Panel\Fields;

use Illuminate\Http\Request;

class Password extends Field
{
    /**
     * @var string
     */
    public $component = 'Password';

    /**
     * {@inheritdoc}
     */
    public $visibility = [
        self::SHOW_ON_CREATE,
        self::SHOW_ON_UPDATE,
    ];

    /**
     * Fill attribute from request.
     *
     * @param Request $request
     * @param $model
     * @param $field
     */
    public function fillAttributeFromRequest(Request $request, $model, $field)
    {
        if ($request->exists($field)) {
            $model->{$field} = bcrypt($request->input($field));
        }
    }
}
