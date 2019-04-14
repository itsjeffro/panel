<?php

namespace Itsjeffro\Panel\Fields;

class Textarea extends Field
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
     * @var string
     */
    public $component = 'Textarea';
}
