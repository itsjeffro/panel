<?php

namespace Itsjeffro\Panel\Fields;

class HasMany extends Field
{
    /**
     * @var bool
     */
    public $isRelationshipField = true;

    /**
     * @var string
     */
    public $component = 'HasMany';

    /**
     * {@inheritdoc}
     */
    public $visibility = [
        self::SHOW_ON_DETAIL,
    ];
}
