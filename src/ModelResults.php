<?php

namespace Itsjeffro\Panel;

use Illuminate\Http\Request;

class ModelResults
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
     * ModelResults constructor.
     */
    public function __construct(ResourceManager $resourceManager, Request $request)
    {
        $this->resourceManager = $resourceManager;
        $this->request = $request;
    }

    /**
     * Return built data response.
     *
     * @throws \Exception
     */
    public function get(): array
    {
        $resource = $this->resourceManager->getResourceClass();
        $model = $this->resourceManager->resolveModel();
        $with = $this->resourceManager->getWith();

        $models = $model::with($with)->orderBy('id', 'desc');

        if ($this->request->exists('search')) {
            foreach ($resource->search as $column) {
                $models->orWhere($column, 'LIKE', $this->request->input('search').'%');
            }
        }

        return [
            'name' => $this->resourceManager->getResourceName(),
            'fields' => $this->resourceManager->getFields(ResourceManager::SHOW_ON_INDEX),
            'model_data' => $models->paginate(),
        ];
    }
}
