<?php

namespace Itsjeffro\Panel\Fields;

class Text extends Field
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
    public $component = 'Text';
}
