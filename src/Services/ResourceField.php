<?php

namespace Itsjeffro\Panel\Services;

use Itsjeffro\Panel\Fields\BelongsTo;
use Itsjeffro\Panel\Fields\Field;
use Itsjeffro\Panel\Fields\HasMany;
use Itsjeffro\Panel\Fields\MorphToMany;

class ResourceField
{
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
