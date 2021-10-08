<?php

namespace Itsjeffro\Panel;

use Exception;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Itsjeffro\Panel\Contracts\ResourceInterface;

abstract class Resource implements ResourceInterface
{
    /**
     * Determines if the resource should appear in the menu.
     *
     * @var bool
     */
    public static $displayInNavigation = true;

    /**
     * The relationships that should be eager loaded for the resource's model.
     *
     * @var array
     */
    public static $with = [];

    /**
     * Return resource's defined fields.
     */
    abstract public function fields(): array;

    /**
     * Get the actions available for the resource.
     */
    public function actions(Request $request): array
    {
        return [];
    }

    /**
     * Returns resource's name.
     */
    public function name(): string
    {
        $className = static::class;
        $resource = explode('\\', $className);

        return Str::plural(end($resource));
    }

    /**
     * Returns resource's slug.
     */
    public function slug(): string
    {
        return Str::kebab($this->name());
    }

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
