<?php

namespace Itsjeffro\Panel\Services;

use Itsjeffro\Panel\Fields\BelongsTo;
use Itsjeffro\Panel\Fields\HasMany;
use Itsjeffro\Panel\Fields\MorphToMany;

class ResourceValidator
{
    /**
     * Get validation rules for updatable fields.
     */
    public function getValidationRules($model, array $fields): array
    {
        $rules = [];

        foreach ($fields as $field) {
            $column = $field->column;

            if ($field instanceof MorphToMany) {
                $column = $model->{$column}();
            }

            if ($field instanceof HasMany) {
                $column = $model->{$column}();
            }

            if ($field instanceof BelongsTo) {
                $column = $model->{$column}()->getForeignKeyName();
            }

            if ($field->rules) {
                $rules[$column] = implode('|', $field->rules);
            }
        }

        return $rules;
    }
}
