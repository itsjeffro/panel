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
     * @return string
     */
    public function getName(): string
    {
        $resource = $this->getClass();
        $model = explode('\\', $resource->model);

        return end($model);
    }

    /**
     * @return mixed
     */
    public function getClass()
    {
        return (new $this->recourceClass);
    }

    /**
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
