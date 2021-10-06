<?php

namespace Itsjeffro\Panel\Actions;

abstract class Action
{
    public function __construct()
    {
        //
    }

    public static function make()
    {
        return new static();
    }
}
