<?php

namespace Itsjeffro\Panel\Http\Controllers;

use Illuminate\Routing\Controller;
use Illuminate\Support\Str;
use Itsjeffro\Panel\ResourceManager;

class ResourceController extends Controller
{
    /**
     * Path to resources.
     *
     * @var string
     */
    public $resourcesPath = 'app/Panel';

    /**
     * List resources from resource type.
     *
     * @param string $resource
     * @return \Illuminate\Http\JsonResponse
     * @throws \Exception
     */
    public function index(string $resource)
    {
        $resourceManager = new ResourceManager($this->resourcesPath, $resource);
        $model = $resourceManager->resolveModel();
        $name = $resourceManager->getName();

        $indexes = [];

        foreach ($resourceManager->getClass()->fields() as $field) {
            $indexes[] = $field;
        }

        return response()->json([
            'name' => [
                'singular' => $name,
                'plural' => Str::plural($name),
            ],
            'indexes' => $indexes,
            'model_data' => $model::paginate(),
        ]);
    }

    /**
     * Get single model resource.
     *
     * @param string $resource
     * @param string $id
     * @throws \Exception
     * @return Illuminate\Http\JsonResponse
     */
    public function show(string $resource, string $id)
    {
        $resourceManager = new ResourceManager($this->resourcesPath, $resource);
        $model = $resourceManager->resolveModel();
        $name = $resourceManager->getName();

        $fields = [];

        foreach ($resourceManager->getClass()->fields() as $field) {
            $fields[] = $field;
        }

        return response()->json([
            'name' => [
                'singular' => $name,
                'plural' => Str::plural($name),
            ],
            'fields' => $fields,
            'model_data' => $model::find($id),
        ]);
    }

    /**
     * Update single model resource.
     *
     * @param string $resource
     * @param string $id
     * @return Illuminate\Http\JsonResponse
     */
    public function update(string $resource, string $id)
    {
        //
    }
}
