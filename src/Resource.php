<?php

namespace Itsjeffro\Panel;

abstract class Resource
{
    /**
     * Return resource's fields.
     */
    abstract public function fields(): array;
}
