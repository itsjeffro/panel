<?php

namespace Itsjeffro\Panel;

use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Http\Request;

class ModelUpdate
{
    private $resourceManager;

    private $request;

    private $id;

    /**
     * ModeUpdate constructor.
     */
    public function __construct(ResourceManager $resourceManager, Request $request, string $id)
    {
        $this->resourceManager = $resourceManager;
        $this->request = $request;
        $this->id = $id;
    }

    /**
     * Validate and update model.
     *
     * @return \Illuminate\Http\JsonResponse
     * @throws \Illuminate\Database\Eloquent\ModelNotFoundException
     * @throws \Exception
     */
    public function update()
    {
        $resourceModel = $this->resourceManager->resolveModel();
        $model = $resourceModel::find($this->id);

        if (!is_object($model)) {
            throw new ModelNotFoundException();
        }

        $fields = $this->fields();
        $validationRules = $this->validationRules($fields);

        if ($validationRules) {
            $this->request->validate($validationRules);
        }

        foreach ($fields as $field) {
            $column = $field->isRelationshipField ? $field->foreignKey : $field->column;

            $field->fillAttributeFromRequest($this->request, $model, $column);
        }

        $model->save();

        return $model;
    }

    /**
     * Get updatable fields.
     */
    protected function fields(): array
    {
        $fields = array_filter($this->resourceManager->getFields(), function ($field) {
            return $field->showOnUpdate;
        });

        return array_map(function ($field) {
            $field->rules = $field->rules + $field->rulesOnUpdate;
            return $field;
        }, $fields, []);
    }

    /**
     * Get validation rules for updatable fields.
     */
    protected function validationRules(array $fields): array
    {
        return array_reduce($fields, function ($carry, $field) {
            $column = $field->isRelationshipField ? $field->foreignKey : $field->column;

            if ($field->rules) {
                $carry[$column] = implode('|', $field->rules);
            }

            return $carry;
        }, []);
    }
}
