<?php

namespace Itsjeffro\Panel\Fields;

class HasMany extends Field
{
    /**
     * @var bool
     */
    public $isRelationshipField = true;

    /**
     * @var bool
     */
    public $showOnCreate = false;

    /**
     * @var bool
     */
    public $showOnUpdate = false;

    /**
     * @var bool
     */
    public $showOnDetail = true;

    /**
     * @var bool
     */
    public $showOnIndex = false;

    /**
     * @var string
     */
    public $component = 'HasMany';
}
