<?php

namespace Itsjeffro\Panel\Http\Controllers;

use Illuminate\Routing\Controller;
use Illuminate\Support\Arr;
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
        $fields = $resourceManager->getFields();
        $columns = Arr::pluck($fields, 'column');

        return response()->json([
            'name' => [
                'singular' => $name,
                'plural' => Str::plural($name),
            ],
            'fields' => $fields,
            'model_data' => $model::select($columns)->paginate(),
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
        $fields = $resourceManager->getFields();
        $columns = Arr::pluck($fields, 'column');

        return response()->json([
            'name' => [
                'singular' => $name,
                'plural' => Str::plural($name),
            ],
            'fields' => $fields,
            'model_data' => $model::select($columns)->find($id),
        ]);
    }

    /**F
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


    /**
     * Create new model resource.
     *
     * @param string $resource
     * @param string $id
     * @return Illuminate\Http\JsonResponse
     */
    public function store(string $resource)
    {
        //
    }
}
