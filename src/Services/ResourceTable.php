<?php

namespace Itsjeffro\Panel\Services;

use Illuminate\Support\Collection;
use Itsjeffro\Panel\Fields\Field;
use Itsjeffro\Panel\Resource;

class ResourceTable
{
    /**
     * Returns fields to be used for table headings.
     */
    public function tableHeaders(Resource $resource): Collection
    {
        return $resource->fieldsByVisibility(Field::SHOW_ON_INDEX)
            ->map(function ($field) {
                return [
                    'attribute' => $field->column,
                    'name' => $field->name,
                ];
            })
            ->values();
    }
}
