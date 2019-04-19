<?php

namespace Itsjeffro\Panel\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
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

        return response()->json([
            'name' => [
                'singular' => $name,
                'plural' => Str::plural($name),
            ],
            'fields' => $resourceManager->getFields(ResourceManager::SHOW_ON_INDEX),
            'model_data' => $model::orderBy('id', 'desc')->paginate(),
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

        return response()->json([
            'name' => [
                'singular' => $name,
                'plural' => Str::plural($name),
            ],
            'fields' => $resourceManager->getFields(ResourceManager::SHOW_ON_CREATE),
            'model_data' => $model::find($id),
            'relationships' => $resourceManager->getRelationships(),
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

        $allowedFields = array_filter($resourceManager->getFields(ResourceManager::SHOW_ON_UPDATE), function ($field) {
            return $field->showOnUpdate;
        });

        $fields = array_map(function ($field) {
            return $field->isRelationshipField ? $field->foreignKey : $field->column;
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
     * @throws \Exception
     */
    public function store(Request $request, string $resource)
    {
        $resourceManager = new ResourceManager($resource);
        $resourceModel = $resourceManager->resolveModel();
        $validationRules = $resourceManager->getValidationRules();
        $allowedFields = $resourceManager->getFields(ResourceManager::SHOW_ON_CREATE);

        if ($validationRules) {
            $request->validate($validationRules);
        }

        $fields = array_map(function ($field) {
            return $field->isRelationshipField ? $field->foreignKey : $field->column;
        }, $allowedFields);

        $model = new $resourceModel;

        foreach ($fields as $field) {
            $model->{$field} = $request->input($field);
        }

        $model->save();

        return response()->json($model, 201);
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
