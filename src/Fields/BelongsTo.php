<?php

namespace Itsjeffro\Panel\Fields;

class BelongsTo extends Field
{
    /**
     * @var bool
     */
    public $isRelationshipField = true;

    /**
     * @var bool
     */
    public $showOnCreate = true;

    /**
     * @var bool
     */
    public $showOnUpdate = true;

    /**
     * @var bool
     */
    public $showOnDetail = true;

    /**
     * @var bool
     */
    public $showOnIndex = true;

    /**
     * @var string
     */
    public $component = 'BelongsTo';
}
