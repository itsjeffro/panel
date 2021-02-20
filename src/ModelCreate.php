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
     */
    public function __construct(ResourceManager $resourceManager, Request $request)
    {
        $this->resourceManager = $resourceManager;
        $this->request = $request;
    }

    /**
     * Create resource.
     *
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
     */
    protected function fields(): array
    {
        $fields = array_filter($this->resourceManager->getFields(), function ($field) {
            return $field->showOnCreate;
        });
        
        return array_map(function ($field) {
            $field->rules = $field->rules + $field->rulesOnCreate;
            return $field;
        }, $fields, []);
    }

    /**
     * Get validation rules for creation fields.
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