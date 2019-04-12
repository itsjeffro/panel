<?php

namespace Itsjeffro\Panel;

use http\Exception\InvalidArgumentException;
use Illuminate\Database\Eloquent\Model;

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
    public function __construct(string $resource)
    {
        $this->resourceClass = $this->classNameFromResource(Panel::getResources(), $resource);
    }

    /**
     * Return resource class.
     *
     * @param array $registeredResources
     * @param string $resource
     * @return string
     */
    public function classNameFromResource(array $registeredResources, string $resource): string
    {
        foreach ($registeredResources as $registeredResource) {
            if (strtolower($registeredResource['slug']) === strtolower($resource)) {
                return $registeredResource['path'];
            }
        }

        throw new \InvalidArgumentException(sprintf("Resource [%s] is not registered.", $resource));
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
        return (new $this->resourceClass);
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
