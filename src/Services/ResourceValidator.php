<?php

namespace Itsjeffro\Panel\Services;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Collection;
use Itsjeffro\Panel\Fields\BelongsTo;
use Itsjeffro\Panel\Fields\HasMany;
use Itsjeffro\Panel\Fields\MorphToMany;

class ResourceValidator
{
    /**
     * Get validation rules for updatable fields.
     */
    public function getValidationRules(Model $model, Collection $fields): array
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
