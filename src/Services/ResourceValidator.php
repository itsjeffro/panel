<?php

namespace Itsjeffro\Panel\Services;

class ResourceValidator
{
    /**
     * Get validation rules for updatable fields.
     */
    public function getValidationRules($resourceModel, array $fields): array
    {
        $rules = [];

        foreach ($fields as $field) {
            $column = $field->column;

            if ($field->isRelationshipField) {
                $column = $resourceModel->{$column}()->getForeignKeyName();
            }

            if ($field->rules) {
                $rules[$column] = implode('|', $field->rules);
            }
        }

        return $rules;
    }
}
