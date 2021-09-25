<?php

namespace Itsjeffro\Panel\Services;

use Exception;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Collection;
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
        $model = $resource->resolveModel();
        $fields = $resource->fields();

        $groups = [
            'general' => [
                'name' => $resource->modelName() . ' Details',
                'fields' => [],
            ],
        ];

        foreach ($fields as $field) {
            if (property_exists($field, 'isRelationshipField') && $field->isRelationshipField) {
                $relationshipResource = new $field->resourceNamespace;
                $relationshipModel = new $relationshipResource->model;
                $relationshipName = $field->column;

                $field->relation = [
                    'type' => lcfirst($field->component),
                    'table' => $relationshipModel->getTable(),
                    'title' => $relationshipResource->title,
                    'foreign_key' => $model->{$relationshipName}()->getForeignKeyName(),
                ];
            }

            if ($field instanceof Block) {
                $groupKey = strtolower($field->getName());

                if (!isset($groups[$groupKey]['name'])) {
                    $groups[$groupKey]['name'] = $field->getName();
                }

                $groups[$groupKey]['fields'] = $field->fields();
            } elseif ($field instanceof HasMany) {
                $groupKey = strtolower($field->getName());

                if (!isset($groups[$groupKey]['name'])) {
                    $groups[$groupKey]['name'] = $field->getName();
                }

                $groups[$groupKey]['fields'][] = $field;
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
    public function getFields(string $visibility = ''): Collection
    {
        $resource = $this->getResourceClass();
        $model = $resource->resolveModel();
        $fields = new Collection();

        foreach ($resource->fields() as $resourceField) {
            if (property_exists($resourceField, 'isRelationshipField') && $resourceField->isRelationshipField) {
                $relationshipResource = new $resourceField->resourceNamespace;
                $relationshipModel = new $relationshipResource->model;
                $relationshipColumn = $resourceField->column;

                $resourceField->relation = [
                    'type' => lcfirst($resourceField->component),
                    'table' => $relationshipModel->getTable(),
                    'title' => $relationshipResource->title,
                    'foreign_key' => $model->{$relationshipColumn}()->getForeignKeyName(),
                ];
            }

            if ($resourceField instanceof Block) {
                foreach ($resourceField->fields() as $blockField) {
                    $fields->add($blockField);
                }
            } else {
                $fields->add($resourceField);
            }
        }

        if (!$visibility) {
            return $fields;
        }

        return $fields->filter(function ($field) use ($visibility) {
            return in_array($visibility, $field->visibility);
        });
    }

    /**
     * Return the model relationships that should be eager loaded via the with() method. Exclude
     * hasMany fields as they will be passed with the relationships property in the controllers.
     */
    public function getWith(): Collection
    {
        $relationshipFields = $this->getFields()
            ->filter(function ($field) {
                return $field instanceof Field && $field->isRelationshipField && !$field instanceof HasMany;
            });

        return $relationshipFields->map(function ($field) {
            return $field->column;
        });
    }

    /**
     * Returns the resource model's relationships.
     */
    public function getRelationships(string $showOn = '', string $id = ''): array
    {
        $fields = $this->getFields($showOn)
            ->filter(function ($field) {
                return $field instanceof Field &&in_array(get_class($field), self::RELATIONSHIPS_TYPES);
            });

        $relationshipFields = [];

        foreach ($fields as $field) {
            $component = Str::camel($field->component);
            $table = is_array($field->relation) ? $field->relation['table'] : '';

            $relationshipFields[$component][$table] = [
                'name' => $field->name,
                'table' => $table,
            ];
        }

        return $relationshipFields;
    }
}
