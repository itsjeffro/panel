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
        $validationRules = $this->resourceManager->getValidationRules(ResourceManager::SHOW_ON_CREATE);
        $fields = $this->resourceManager->getFields(ResourceManager::SHOW_ON_CREATE);

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
}
