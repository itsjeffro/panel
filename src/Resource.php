<?php

namespace Itsjeffro\Panel;

use Exception;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;
use Itsjeffro\Panel\Contracts\ResourceInterface;

abstract class Resource implements ResourceInterface
{
    /**
     * Return resource's defined fields.
     */
    abstract public function fields(): array;

    /**
     * Returns resource's model name.
     */
    public function modelName(): string
    {
        $model = explode('\\', $this->model);

        return end($model);
    }

    /**
     * Returns resource's model plural name.
     */
    public function modelPluralName(): string
    {
        $model = explode('\\', $this->model);

        return Str::plural(end($model));
    }

    /**
     * Resolves the model from the resource.
     *
     * @throws Exception
     */
    public function resolveModel(): Model
    {
        $model = app()->make($this->model);

        if (!$model instanceof Model) {
            throw new Exception('Class is not an instance of Model');
        }

        return $model;
    }
}
