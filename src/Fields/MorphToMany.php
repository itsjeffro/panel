<?php

namespace Itsjeffro\Panel\Fields;

use Illuminate\Http\Request;

class MorphToMany extends Field
{
    /**
     * {@inheritdoc}
     */
    public $isRelationshipField = true;

    /**
     * {@inheritdoc}
     */
    public $component = 'MorphToMany';

    /**
     * {@inheritdoc}
     */
    public $visibility = [
      self::SHOW_ON_UPDATE,
      self::SHOW_ON_CREATE,
      self::SHOW_ON_DETAIL,
      self::SHOW_ON_INDEX,
    ];

    /**
     * {@inheritDoc}
     */
    public function fillAttributeFromRequest(Request $request, $model, $field)
    {
        if ($request->exists($field)) {
            $model->{$field}()->sync($request->input($field));
        }
    }
}
