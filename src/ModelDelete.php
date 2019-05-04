<?php

namespace Itsjeffro\Panel;

use Illuminate\Database\Eloquent\ModelNotFoundException;

class ModelDelete
{
    /**
     * @var ResourceManager
     */
    private $resourceManager;

    /**
     * @var string
     */
    private $id;

    /**
     * ModelDelete constructor.
     *
     * @param ResourceManager $resourceManager
     * @param string $id
     */
    public function __construct(ResourceManager $resourceManager, string $id)
    {
        $this->resourceManager = $resourceManager;
        $this->id = $id;
    }

    /**
     * @throws \Illuminate\Database\Eloquent\ModelNotFoundException
     * @throws \Exception
     */
    public function delete()
    {
        $resourceModel = $this->resourceManager->resolveModel();
        $model = $resourceModel::find($this->id);

        if (!is_object($model)) {
            throw new ModelNotFoundException();
        }

        $model->delete();
    }
}
