<?php

namespace Itsjeffro\Panel;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;

class ResourceManager
{
    /**
     * @var string
     */
    public $resourceClass;

    /**
     * ResourceManager constructor.
     *
     * @param string $path
     * @param string $resource
     */
    public function __construct(string $path, string $resource)
    {
        $this->recourceClass = $this->classNameFromResource($path, $resource);
    }

    /**
     * Return resource class.
     *
     * @param string $path
     * @param string $resource
     * @return string
     */
    public function classNameFromResource(string $path, string $resource): string
    {
        $resourceSlug = ucfirst(Str::singular($resource));

        return '\\'.str_replace(DIRECTORY_SEPARATOR, '\\', ucfirst($path)).'\\'.$resourceSlug;
    }

    /**
     * Return name of resource from class.
     *
     * @return string
     */
    public function getName(): string
    {
        $model = explode('\\', $this->getClass()->model);

        return end($model);
    }

    /**
     * Return fill class name.
     *
     * @return mixed
     */
    public function getClass()
    {
        return (new $this->recourceClass);
    }

    /**
     * Return resource's fields along with indexes.
     *
     * @return array
     */
    public function getFields(): array
    {
        $fields = [];
        foreach ($this->getClass()->fields() as $field) {
            $fields[] = $field;
        }
        return $fields;
    }

    /**
     * Resolved associated model class from resource.
     *
     * @return mixed
     * @throws \Exception
     */
    public function resolveModel()
    {
        $resource = $this->getClass();
        $model = app()->make($resource->model);

        if (!$model instanceof Model) {
            throw new \Exception('Class is not an instance of Model');
        }

        return $model;
    }
}
