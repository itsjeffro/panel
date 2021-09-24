<?php

namespace Itsjeffro\Panel\Services;

use Exception;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;
use Itsjeffro\Panel\Block;
use Itsjeffro\Panel\Contracts\ResourceInterface;
use Itsjeffro\Panel\Fields\BelongsTo;
use Itsjeffro\Panel\Fields\Field;
use Itsjeffro\Panel\Fields\HasMany;
use Itsjeffro\Panel\Panel;

class ResourceModel
{
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
     * Return resource's full class name.
     */
    public function getResourceClass(): ResourceInterface
    {
        return (new $this->resourceClass);
    }

    /**
     * Return resource's grouped fields along.
     *
     * @throws Exception
     */
    public function getGroupedFields(string $visibility = ''): array
    {
        $resource = $this->getResourceClass();
        $fields = $resource->fields();

        $groups = [
            'general' => [
                'name' => $resource->modelName() . ' Details',
                'fields' => [],
            ],
        ];

        foreach ($fields as $field) {
            if (property_exists($field, 'isRelationshipField') && $field->isRelationshipField) {
                $relationshipResource = $this->getRelationshipResource($field->relation);
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

            if ($field instanceof Block) {
                $groupKey = strtolower($field->getName());

                if (!isset($groups[$groupKey]['name'])) {
                    $groups[$groupKey]['name'] = $field->getName();
                }

                $groups[$groupKey]['fields'] = $field->getFields();
            } elseif ($field instanceof HasMany) {
                $groupKey = strtolower($field->getName());

                if (!isset($groups[$groupKey]['name'])) {
                    $groups[$groupKey]['name'] = $field->getName();
                }

                $groups[$groupKey]['fields'] = $field;
            } else {
                $groupKey = 'general';

                $groups[$groupKey]['fields'][] = $field;
            }
        }

        return $groups;
    }

    /**
     * Returns a flat list of the resource's defined fields.
     */
    public function getFields(string $visibility = ''): array
    {
        $resource = $this->getResourceClass();
        $fields = [];

        foreach ($resource->fields() as $resourceField) {
            if ($resourceField instanceof Block) {
                foreach ($resourceField->getFields() as $blockField) {
                    $fields[] = $blockField;
                }
            } else {
                $fields[] = $resourceField;
            }
        }

        $fields = array_filter($fields, function (Field $field) use ($visibility) {
            return in_array($visibility, $field->visibility);
        });

        return $fields;
    }

    /**
     * Return the model relationships that should be eager loaded via the with() method.
     */
    public function getWith(): array
    {
        // Exclude hasMany fields as they will be passed with the relationships property in the controllers.
        $relationshipFields = array_filter($this->getFields(), function ($field) {
            return $field instanceof Field &&
                $field->isRelationshipField &&
                !$field instanceof HasMany;
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
            return $field instanceof Field && in_array(get_class($field), self::RELATIONSHIPS_TYPES);
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
     * @throws Exception
     */
    public function resolveModel()
    {
        $resource = $this->getResourceClass();
        $model = app()->make($resource->model);

        if (!$model instanceof Model) {
            throw new Exception('Class is not an instance of Model');
        }

        return $model;
    }
}
