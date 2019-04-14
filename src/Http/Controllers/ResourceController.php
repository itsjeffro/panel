<?php

namespace Itsjeffro\Panel\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Illuminate\Support\Arr;
use Illuminate\Support\Str;
use Itsjeffro\Panel\ResourceManager;

class ResourceController extends Controller
{
    /**
     * List resources from resource type.
     *
     * @param string $resource
     * @return \Illuminate\Http\JsonResponse
     * @throws \Exception
     */
    public function index(string $resource)
    {
        $resourceManager = new ResourceManager($resource);
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
     * @return \Illuminate\Http\JsonResponse
     */
    public function show(string $resource, string $id)
    {
        $resourceManager = new ResourceManager($resource);
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
     * @param \Illuminate\Http\Request
     * @param string $resource
     * @param string $id
     * @return \Illuminate\Http\JsonResponse
     * @throws \Exception
     */
    public function update(Request $request, string $resource, string $id)
    {
        $resourceManager = new ResourceManager($resource);
        $resourceModel = $resourceManager->resolveModel();
        $validationRules = $resourceManager->getValidationRules();

        if ($validationRules) {
            $request->validate($validationRules);
        }

        $allowedFields = array_filter($resourceManager->getFields(), function ($field) {
            return $field->showOnUpdate;
        });

        $fields = array_map(function ($field) {
            return $field->column;
         }, $allowedFields);

        $affectedRows = $resourceModel::where('id', $id)
            ->limit(1)
            ->update($request->only($fields));

        if ($affectedRows > 0) {
            $model = $resourceModel::select($fields)->find($id);

            return response()->json($model);
        }

        return response()->json(['Model not found.'], 404);
    }


    /**
     * Create new model resource.
     *
     * @param Request $request
     * @param string $resource
     * @return \Illuminate\Http\JsonResponse
     */
    public function store(Request $request, string $resource)
    {
        $resourceManager = new ResourceManager($resource);
        $validationRules = $resourceManager->getValidationRules();

        if ($validationRules) {
            $request->validate($validationRules);
        }

        return response()->json([], 201);
    }

    /**
     * Delete resource.
     *
     * @param Request $request
     * @param string $resource
     * @param string $id
     * @return \Illuminate\Http\JsonResponse
     */
    public function destroy(Request $request, string $resource, string $id)
    {
        return response()->json([], 200);
    }
}
