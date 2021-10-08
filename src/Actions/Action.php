<?php

namespace Itsjeffro\Panel\Actions;

abstract class Action
{
    /**
     * Action constructor.
     */
    public function __construct()
    {
        //
    }

    /**
     * Create instance of Action.
     */
    public static function make(): self
    {
        return new static();
    }
}
