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
     * @return \Illuminate\Http\JsonResponse
     * @throws \Illuminate\Database\Eloquent\ModelNotFoundException
     * @throws \Exception
     */
    public function update()
    {
        $resourceModel = $this->resourceManager->resolveModel();
        $validationRules = $this->resourceManager->getValidationRules(ResourceManager::SHOW_ON_UPDATE);
        $fields = $this->resourceManager->getFields(ResourceManager::SHOW_ON_UPDATE);

        if ($validationRules) {
            $this->request->validate($validationRules);
        }

        $fields = array_filter($fields, function ($field) use ($validationRules) {
            $column = $field->isRelationshipField ? $field->foreignKey : $field->column;
            $fieldValidation = $validationRules[$column] ?? '';

            // Exclude the field from being processed
            if ($fieldValidation && strpos($fieldValidation, 'nullable') !== false) {
                return false;
            }

            return $field->showOnUpdate;
        });

        $model = $resourceModel::find($this->id);

        if (!is_object($model)) {
            throw new ModelNotFoundException();
        }

        foreach ($fields as $field) {
            $column = $field->isRelationshipField ? $field->foreignKey : $field->column;

            $field->fillAttributeFromRequest($this->request, $model, $column);
        }

        $model->save();

        return $model;
    }
}
