<?php

namespace Itsjeffro\Panel\Services;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Collection;

class ResourceValidator
{
    /**
     * Get validation rules for updatable fields.
     */
    public function getValidationRules(Model $model, Collection $fields): array
    {
        $rules = [];
        $resourceField = new ResourceField();

        foreach ($fields as $field) {
            $column = $resourceField->column($model, $field);

            if ($field->rules) {
                $rules[$column] = implode('|', $field->rules);
            }
        }

        return $rules;
    }
}
