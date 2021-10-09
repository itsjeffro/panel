<?php

namespace Itsjeffro\Panel\Services;

use Illuminate\Support\Collection;
use Itsjeffro\Panel\Contracts\ResourceInterface;
use Itsjeffro\Panel\Fields\Field;

class ResourceTable
{
    /**
     * Returns fields to be used for table headings.
     */
    public function tableHeaders(ResourceInterface $resource): Collection
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
