<?php

namespace Itsjeffro\Panel;

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
     *
     * @return string
     */
    public function getResourcesNamespace(): string
    {
        $classSegments =  explode('\\', $this->resourceClass);

        array_pop($classSegments);

        return implode('\\', $classSegments);
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

        return array_map(function ($field) {
            $relationshipResource = $this->getRelationshipResource($field->relation);

            if ($field->isRelationshipField) {
                $field->relation = new $relationshipResource;
            }

            return $field;
        }, $this->getClass()->fields());
    }

    /**
     * @return array
     */
    public function getRelationships(): array
    {
        $relationshipFields = array_filter($this->getFields(), function ($field) {
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
        });
    }

    /**
     * Return relationship resource class.
     *
     * @param string $relation
     * @return string
     */
    public function getRelationshipResource(string $relation): string
    {
        if (strpos($relation, '\\') !== false) {
            return $relation;
        }

        return $this->getResourcesNamespace().'\\'.$relation;
    }

    /**
     * Return fields with their associated validation rules.
     *
     * @return array
     */
    public function getValidationRules(): array
    {
        return array_reduce($this->getClass()->fields(), function ($carry, $field) {
            if ($field->rules) {
                $carry[$field->column] = implode('|', $field->rules);
            }
            return $carry;
        });
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
