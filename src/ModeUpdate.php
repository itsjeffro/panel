<?php

namespace Itsjeffro\Panel;

use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Http\Request;

class ModeUpdate
{
    private $resourceManager;

    private $request;

    private $id;

    /**
     * ModeUpdate constructor.
     *
     * @param ResourceManager $resourceManager
     * @param Request $request
     * @param string $id
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
     * Get updateable fields.
     *
     * @return array
     */
    public function fields(): array
    {
        return array_filter($this->resourceManager->getFields(), function ($field) {
            return $field->showOnUpdate;
        });
    }

    /**
     * Get validation rules for updateable fields.
     *
     * @param array $fields
     * @return array
     */
    public function validationRules(array $fields): array
    {
        return array_reduce($fields, function ($carry, $field) {
            $column = $field->isRelationshipField ? $field->foreignKey : $field->column;
            $field->rules = $field->rules + $field->rulesOnUpdate;

            if ($field->rules) {
                $carry[$column] = implode('|', $field->rules);
            }

            return $carry;
        }, []);
    }
}