<?php

namespace Itsjeffro\Panel\Fields;

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
}
