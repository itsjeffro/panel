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
        $validationRules = $this->validationRules($resourceModel, $fields);

        if ($validationRules) {
            $this->request->validate($validationRules);
        }

        $model = new $resourceModel;

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
}