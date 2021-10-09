<?php

namespace Itsjeffro\Panel\Services;

use Exception;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Collection;
use Itsjeffro\Panel\Block;
use Itsjeffro\Panel\Contracts\ResourceInterface;
use Itsjeffro\Panel\Fields\BelongsTo;
use Itsjeffro\Panel\Fields\Field;
use Itsjeffro\Panel\Fields\HasMany;
use Itsjeffro\Panel\Fields\MorphToMany;

class ResourceModel
{
    /**
     * The defined resource.
     *
     * @var ResourceInterface
     */
    public $resource;

    /**
     * ResourceManager constructor.
     */
    public function __construct(ResourceInterface $resource)
    {
        $this->resource = $resource;
    }

    /**
     * Return resource's grouped fields along.
     *
     * @throws Exception
     */
    public function getGroupedFields(Model $model, ?string $visibility = null): array
    {
        $fields =  collect($this->resource->fields())
            ->filter(function ($field) use ($visibility) {
                return $field instanceof Block ||
                    ($field instanceof Field && $field->hasVisibility($visibility));
            });

        $groups = [
            'general' => [
                'name' => $this->resource->modelName() . ' Details',
                'resourceFields' => [],
            ],
        ];

        foreach ($fields as $field) {
            if ($field instanceof Block) {
                $groupKey = strtolower($field->getName());

                if (!isset($groups[$groupKey]['name'])) {
                    $groups[$groupKey]['name'] = $field->getName();
                }

                $blockFields = collect($field->fields())->filter(function ($field) use ($visibility) {
                    return $field->hasVisibility($visibility);
                });

                $groups[$groupKey]['resourceFields'] = $blockFields->map(function ($blockField) use ($model) {
                    return $this->prepareField($model, $blockField);
                });
            } elseif ($field instanceof HasMany) {
                $groupKey = strtolower($field->getName());

                if (!isset($groups[$groupKey]['name'])) {
                    $groups[$groupKey]['name'] = $field->getName();
                }

                $groups[$groupKey] = $this->prepareField($model, $field);
            } else {
                $groupKey = 'general';

                $groups[$groupKey]['resourceFields'][] = $this->prepareField($model, $field);
            }
        }

        return $groups;
    }

    /**
     * Returns only the resource's index fields.
     */
    public function getResourceIndexFields(Model $model): Collection
    {
        $resourceFields = $this->resource
            ->fieldsByVisibility(Field::SHOW_ON_INDEX)
            ->values();

        $fields = collect([]);

        foreach ($resourceFields as $resourceField) {
            $fields->add($this->prepareField($model, $resourceField));
        }

        return $fields;
    }

    /**
     * Returns field data.
     */
    protected function prepareField(Model $model, Field $field): array
    {
        $fieldColumn = $field->column;
        $fieldAttribute = $fieldColumn;

        $value = $model->{$fieldColumn};
        $resource = $this->resource->slug();
        $resourceId = $model->getKey();
        $resourceName = $value;
        $relationship = null;

        if ($field instanceof MorphToMany) {
            $relationshipResource = new $field->resourceNamespace;
            $resourceTitle = $relationshipResource->title;

            $items = collect($model->{$fieldColumn});
            $value = $items->map(function ($item) {
                return $item->getKey();
            });
            $resource = $relationshipResource->slug();
            $resourceId = $value;
            $resourceName = $items->map(function ($item) use ($resourceTitle) {
                return $item->{$resourceTitle};
            });
        }

        if ($field instanceof BelongsTo) {
            $relationshipResource = new $field->resourceNamespace;
            $fieldAttribute = $model->{$fieldColumn}()->getForeignKeyName();

            $value = optional($model->{$fieldColumn})->getKey();
            $resource = $relationshipResource->slug();
            $resourceId = optional($model->{$fieldColumn})->getKey();
            $resourceName = optional($model->{$fieldColumn})->{$relationshipResource->title};
        }

        if ($field instanceof HasMany) {
            $relationshipResource = new $field->resourceNamespace;

            $value = null;
            $resourceName = null;
            $relationship = $relationshipResource->slug();
        }

        return [
            'component' => $field->component,
            'field' => [
                'attribute' => $fieldAttribute,
                'name' => $field->name,
                'value' => $value,
            ],
            'resource' => $resource,
            'resourceId' => $resourceId,
            'resourceName' => $resourceName,
            'relationship' => $relationship,
        ];
    }
}
