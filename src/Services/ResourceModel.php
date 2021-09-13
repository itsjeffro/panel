<?php

namespace Itsjeffro\Panel\Services;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;
use Itsjeffro\Panel\Fields\BelongsTo;
use Itsjeffro\Panel\Fields\HasMany;
use Itsjeffro\Panel\Panel;

class ResourceModel
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
     * Available relationship types.
     *
     * @var string[]
     */
    const RELATIONSHIPS_TYPES = [
      BelongsTo::class,
      HasMany::class,
    ];

    /**
     * @var string
     */
    public $resourceClass;

    /**
     * ResourceManager constructor.
     */
    public function __construct(string $resourceName)
    {
        $this->resourceClass = $this->classNameFromResource(
            Panel::getResources(),
            $resourceName
        );
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
     * Return singular and plural values for resource. Eg. "post" and "posts".
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
     *
     * @throws \Exception
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
                $modelClass = new $relationshipResource;
                $relationshipModel = new $modelClass->model;
                $relationshipName = $field->column;

                $field->relation = [
                    'type' => lcfirst($field->component),
                    'table' => $relationshipModel->getTable(),
                    'title' => $modelClass->title,
                    'foreign_key' => $this->resolveModel()->{$relationshipName}()->getForeignKeyName(),
                ];
            }

            return $field;
        }, $fields, []);
    }

    /**
     * Return the model relationships that should be eager loaded via the with() method.
     */
    public function getWith(): array
    {
        // Exclude hasMany fields as they will be passed with the relationships property in the controllers.
        $relationshipFields = array_filter($this->getFields(), function ($field) {
            return $field->isRelationshipField && !$field instanceof HasMany;
        });

        return array_map(function ($field) {
            return $field->column;
        }, $relationshipFields, []);
    }

    /**
     * Returns the resource model's relationships.
     */
    public function getRelationships(string $showOn = '', string $id = ''): array
    {
        $fields = array_filter($this->getFields($showOn), function ($field) {
            return in_array(get_class($field), self::RELATIONSHIPS_TYPES);
        });

        $relationshipFields = [];

        foreach ($fields as $field) {
            $component = Str::camel($field->component);
            $table = $field->relation['table'];

            $relationshipFields[$component][$table] = [
                'name' => $field->name,
                'table' => $table,
            ];
        }

        return $relationshipFields;
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
     * Resolves the model from the resource.
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
