<?php

namespace Itsjeffro\Panel;

use Illuminate\Http\Request;

class ModelCreate
{
    /**
     * @var ResourceManager
     */
    private $resourceManager;

    /**
     * @var Request
     */
    private $request;

    /**
     * ModelCreate constructor.
     *
     * @param ResourceManager $resourceManager
     * @param Request $request
     */
    public function __construct(ResourceManager $resourceManager, Request $request)
    {
        $this->resourceManager = $resourceManager;
        $this->request = $request;
    }

    /**
     * @return mixed
     * @throws \Exception
     */
    public function create()
    {
        $resourceModel = $this->resourceManager->resolveModel();
        $fields = $this->fields();
        $validationRules = $this->validationRules($fields);

        if ($validationRules) {
            $this->request->validate($validationRules);
        }

        $model = new $resourceModel;

        foreach ($fields as $field) {
            $column = $field->isRelationshipField ? $field->foreignKey : $field->column;

            $field->fillAttributeFromRequest($this->request, $model, $column);
        }

        $model->save();

        return $model;
    }

    /**
     * Get creation fields.
     *
     * @return array
     */
    public function fields()
    {
        $fields = array_filter($this->resourceManager->getFields(), function ($field) {
            return $field->showOnCreate;
        });
        
        return array_map(function ($field) {
            return $field->rules = $field->rules + $field->rulesOnCreate;
        }, $fields, []);
    }

    /**
     * Get validation rules for creation fields.
     *
     * @param array $fields
     * @return array
     */
    public function validationRules(array $fields): array
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