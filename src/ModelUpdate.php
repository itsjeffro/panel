<?php

namespace Itsjeffro\Panel;

use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Itsjeffro\Panel\Services\ResourceFields;
use Itsjeffro\Panel\Services\ResourceValidator;

class ModelUpdate
{
    private $resourceManager;

    private $request;

    /**
     * ModeUpdate constructor.
     */
    public function __construct(ResourceManager $resourceManager, Request $request)
    {
        $this->resourceManager = $resourceManager;
        $this->request = $request;
    }

    /**
     * Validate and update model.
     *
     * @return JsonResponse
     * @throws ModelNotFoundException
     * @throws \Exception
     */
    public function update(string $id)
    {
        $resourceModel = $this->resourceManager->resolveModel();
        $model = $resourceModel::find($id);
        $resourceValidator = new ResourceValidator();

        if (!$model) {
            throw new ModelNotFoundException();
        }

        $fields = $this->fields();
        $validationRules = $resourceValidator->getValidationRules($resourceModel, $fields);

        if ($validationRules) {
            $this->request->validate($validationRules);
        }

        foreach ($fields as $field) {
            $column = $field->column;

            if ($field->isRelationshipField) {
                $column = $resourceModel->{$column}()->getForeignKeyName();
            }

            $field->fillAttributeFromRequest($this->request, $model, $column);
        }

        $model->save();

        return $model;
    }

    /**
     * Get updatable fields.
     *
     * @throws \Exception
     */
    public function fields(): array
    {
        $fields = array_filter($this->resourceManager->getFields(), function ($field) {
            return $field->showOnUpdate;
        });

        return array_map(function ($field) {
            $field->rules = $field->rules + $field->rulesOnUpdate;
            return $field;
        }, $fields, []);
    }
}
