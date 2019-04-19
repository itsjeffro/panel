<?php

namespace Itsjeffro\Panel\Fields;

use Illuminate\Http\Request;

class Password extends Field
{
    /**
     * @var bool
     */
    public $showOnCreate = true;

    /**
     * @var bool
     */
    public $showOnUpdate = true;

    /**
     * @var string
     */
    public $component = 'Password';

    /**
     * Fill attribute from request.
     *
     * @param Request $request
     * @param $model
     * @param $field
     */
    public function fillAttributeFromRequest(Request $request, $model, $field)
    {
        $model->{$field} = bcrypt($request->input($field));
    }
}
