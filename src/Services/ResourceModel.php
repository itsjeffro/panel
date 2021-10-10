<?php

namespace Itsjeffro\Panel\Services;

use Exception;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Collection;
use Itsjeffro\Panel\Block;
use Itsjeffro\Panel\Fields\BelongsTo;
use Itsjeffro\Panel\Fields\Field;
use Itsjeffro\Panel\Fields\HasMany;
use Itsjeffro\Panel\Fields\MorphToMany;
use Itsjeffro\Panel\Resource;

class ResourceModel
{
    /**
     * The defined resource.
     *
     * @var Resource
     */
    public $resource;

    /**
     * ResourceManager constructor.
     */
    public function __construct(Resource $resource)
    {
        $this->resource = $resource;
    }

    /**
     * Return resource's grouped fields along.
     *
     * @throws Exception
     */
    public function getGroupedFields(Model $model, ?string $visibility = null): Collection
    {
        $fields =  $this->resource
            ->fieldsByVisibility($visibility)
            ->values();

        $resourceFields = collect([]);

        foreach ($fields as $field) {
            $resourceFields->add($this->prepareField($model, $field));
        }

        return $resourceFields;
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
            'block' => $field->blockName,
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
