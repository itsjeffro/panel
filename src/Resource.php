<?php

namespace Itsjeffro\Panel;

use Illuminate\Support\Str;
use Itsjeffro\Panel\Contracts\ResourceInterface;

abstract class Resource implements ResourceInterface
{
    /**
     * Return resource's defined fields.
     */
    abstract public function fields(): array;

    public function modelName(): string
    {
        $model = explode('\\', $this->model);

        return end($model);
    }

    public function modelPluralName(): string
    {
        $model = explode('\\', $this->model);

        return Str::plural(end($model));
    }
}
