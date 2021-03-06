<?php

namespace Itsjeffro\Panel;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;

class ResourceManager
{
    /**
     * @var string
     */
    const SHOW_ON_CREATE = 'showOnCreate';

    /**
     * @var string
     */
    const SHOW_ON_UPDATE = 'showOnUpdate';

    /**
     * @var string
     */
    const SHOW_ON_INDEX = 'showOnIndex';

    /**
     * @var string
     */
    public $resourceClass;

    /**
     * ResourceManager constructor.
     *
     * @param string $resource
     */
    public function __construct(string $resource)
    {
        $this->resourceClass = $this->classNameFromResource(Panel::getResources(), $resource);
    }

    /**
     * Return resource class.

     * @throw InvalidArgumentException
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
     * Return namespace where application resources are located.
     */
    public function getResourceNamespace(): string
    {
        $classSegments =  explode('\\', $this->resourceClass);

        array_pop($classSegments);

        return implode('\\', $classSegments);
    }

    /**
     * Return name of resource from class.
     */
    public function getResourceName(): array
    {
        $model = explode('\\', $this->getResourceClass()->model);
        $name = end($model);

        return [
            'singular' => $name,
            'plural' => Str::plural($name),
        ];
    }

    /**
     * Return resource's full class name.
     *
     * @return mixed
     */
    public function getResourceClass()
    {
        return (new $this->resourceClass);
    }

    /**
     * Return resource's fields along with indexes.
     */
    public function getFields(string $showOn = ''): array
    {
        $fields = $this->getResourceClass()->fields();

        if ($showOn) {
            $fields = array_filter($this->getResourceClass()->fields(), function ($field) use ($showOn) {
                return $field->{$showOn};
            });
        }

        return array_map(function ($field) use ($showOn) {
            $relationshipResource = $this->getRelationshipResource($field->relation);

            if ($field->isRelationshipField) {
                try {
                    $relationMethod = $field->column;
                    $relationshipModel = $this->resolveModel()->$relationMethod();

                    $field->foreignKey = $relationshipModel->getForeignKeyName();
                    $field->relation = new $relationshipResource;
                } catch (\BadMethodCallException $e) {
                    throw new \Exception("Call to undefined relationship [$relationMethod] from model");
                }
            }

            return $field;
        }, $fields, []);
    }

    /**
     * Return the model relationships that should be eager loaded via the with() method.
     */
    public function getWith(): array
    {
        $relationshipFields = array_filter($this->getFields(), function ($field) {
            return $field->isRelationshipField;
        });

        return array_map(function ($field) {
            return $field->column;
        }, $relationshipFields, []);
    }

    /**
     * Return list of models based on the relationships from the main resource.
     */
    public function getRelationships(string $showOn = ''): array
    {
        $relationshipFields = array_filter($this->getFields($showOn), function ($field) {
            return $field->isRelationshipField;
        });

        return array_reduce($relationshipFields, function ($carry, $field) {
            $relation = $field->relation;
            $columns = ['id'];

            if (!in_array($relation->title, $columns)) {
                $columns[] = $relation->title;
            }

            $carry[$field->column] = $relation->model::select($columns)->get();

            return $carry;
        }, []);
    }

    /**
     * Return relationship resource class.
     */
    public function getRelationshipResource(string $relation): string
    {
        if (strpos($relation, '\\') !== false) {
            return $relation;
        }

        return $this->getResourceNamespace().'\\'.$relation;
    }

    /**
     * Resolved associated model class from resource.
     *
     * @return mixed
     * @throws \Exception
     */
    public function resolveModel()
    {
        $resource = $this->getResourceClass();
        $model = app()->make($resource->model);

        if (!$model instanceof Model) {
            throw new \Exception('Class is not an instance of Model');
        }

        return $model;
    }
}
