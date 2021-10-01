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
use Itsjeffro\Panel\Fields\MorphToMany;
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
     * Returns a flat list of the resource's defined fields.
     */
    public function getFields(string $visibility = ''): Collection
    {
        $resource = $this->getResourceClass();
        $fields = new Collection([]);

        foreach ($resource->fields() as $resourceField) {
            if ($resourceField instanceof Block) {
                foreach ($resourceField->fields() as $blockField) {
                    $fields->add($blockField);
                }
            } else {
                $fields->add($resourceField);
            }
        }

        if ($visibility) {
            $fields = $fields->filter(function ($field) use ($visibility) {
                return in_array($visibility, $field->visibility);
            });
        }

        return $fields->values();
    }

    /**
     * Return resource's grouped fields along.
     *
     * @throws Exception
     */
    public function getResourceDetailFields($model): array
    {
        $resource = $this->getResourceClass();
        $fields = $this->getResourceFields($model);

        $groups = [
            'general' => [
                'name' => $resource->modelName() . ' Details',
                'resourceFields' => [],
            ],
        ];

        foreach ($fields as $field) {
            if ($field instanceof Block) {
                $groupKey = strtolower($field->getName());

                if (!isset($groups[$groupKey]['name'])) {
                    $groups[$groupKey]['name'] = $field->getName();
                }

                $blockFields = $this->filterFieldsByVisibility(Field::SHOW_ON_DETAIL, $field->fields());

                $groups[$groupKey]['resourceFields'] = $blockFields->map(function ($blockField) use ($model) {
                    return [
                        'component' => $blockField->component,
                        'field' => $blockField,
                        'resourceId' => $model->getKey(),
                        'resourceName' => $model->getTable(),
                    ];
                });
            } elseif ($field instanceof HasMany) {
                $groupKey = strtolower($field->getName());

                if (!isset($groups[$groupKey]['name'])) {
                    $groups[$groupKey]['name'] = $field->getName();
                }

                $groups[$groupKey] = [
                    'component' => $field->component,
                    'resourceName' => $field->column,
                ];
            } else {
                $groupKey = 'general';

                $field->setValue($this->resourceValue($model, $field));

                $groups[$groupKey]['resourceFields'][] = [
                    'component' => $field->component,
                    'field' => [
                        'attribute' => $field->column,
                        'name' => $field->name,
                        'value' => $field->value,
                    ],
                    'resourceId' => $model->getKey(),
                    'resourceName' => $model->getTable(),
                ];
            }
        }

        return $groups;
    }

    /**
     * Returns only the resource's index fields.
     */
    public function getResourceIndexFields($model): Collection
    {
        $resourceFields = $this->getResourceFields($model)
            ->filter(function ($field) {
                return $field->hasVisibility(Field::SHOW_ON_INDEX);
            })
            ->values();

        $fields = collect([]);

        foreach ($resourceFields as $resourceField) {
            $resourceField->setValue(
                $this->resourceValue($model, $resourceField)
            );

            $fields->add([
                'attribute' => $resourceField->column,
                'name' => $resourceField->name,
                'value' => $resourceField->value,
            ]);
        }

        return $fields;
    }

    /**
     * Return all resource's fields.
     */
    public function getResourceFields($model): Collection
    {
        $resource = $this->getResourceClass();
        $resourceFields = new Collection([]);

        foreach ($resource->fields() as $resourceField) {
            if ($resourceField instanceof Block) {
                $resourceFields->push(...$resourceField->fields());
            } else {
                $resourceFields->add($resourceField);
            }
        }

        return $resourceFields;
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
     * Returns resource's relationship.
     *
     * @return mixed
     */
    protected function resourceValue($model, Field $field)
    {
        $fieldColumn = $field->column;

        if ($field instanceof MorphToMany) {
            $relationshipResource = new $field->resourceNamespace;
            $resourceTitle = $relationshipResource->title;
            $items = collect($model->{$fieldColumn});

            return $items->map(function ($item) use ($resourceTitle) {
                return $item->{$resourceTitle};
            });
        }

        if ($field instanceof BelongsTo) {
            $relationshipResource = new $field->resourceNamespace;
            $resourceTitle = $relationshipResource->title;

            return $model->{$fieldColumn}->{$resourceTitle};
        }

        return $model->{$fieldColumn};
    }

    /**
     * Returns fields based on its specified visibility.
     */
    protected function filterFieldsByVisibility(string $visibility, array $fields): Collection
    {
        $fields = collect($fields);

        if (!$visibility) {
            return $fields;
        }

        return $fields->filter(function ($field) use ($visibility) {
            return $field instanceof Block || ($field instanceof Field && $field->hasVisibility($visibility));
        });
    }
}
