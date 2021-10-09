<?php

namespace Itsjeffro\Panel\Services;

use Illuminate\Support\Collection;
use Itsjeffro\Panel\Block;
use Itsjeffro\Panel\Contracts\ResourceInterface;
use Itsjeffro\Panel\Fields\BelongsTo;
use Itsjeffro\Panel\Fields\Field;
use Itsjeffro\Panel\Fields\HasMany;
use Itsjeffro\Panel\Fields\MorphToMany;

class ResourceField
{
    /**
     * Resolve model field column.
     */
    public function column($model, Field $field): string
    {
        $column = $field->column;

        if ($field instanceof MorphToMany) {
            return $field->column;
        }

        if ($field instanceof HasMany) {
            return  $model->{$column};
        }

        if ($field instanceof BelongsTo) {
            return $model->{$column}()->getForeignKeyName();
        }

        return $column;
    }
}
