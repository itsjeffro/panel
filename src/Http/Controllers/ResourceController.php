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
        $with = $resourceManager->getWith();

        return response()->json([
            'name' => [
                'singular' => $name,
                'plural' => Str::plural($name),
            ],
            'fields' => $resourceManager->getFields(ResourceManager::SHOW_ON_INDEX),
            'model_data' => $model::with($with)->orderBy('id', 'desc')->paginate(),
        ]);
    }

    /**
     * Retrieve a single model resource.
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
        $with = $resourceManager->getWith();

        return response()->json([
            'name' => [
                'singular' => $name,
                'plural' => Str::plural($name),
            ],
            'fields' => $resourceManager->getFields(ResourceManager::SHOW_ON_CREATE),
            'model_data' => $model::with($with)->find($id),
            'relationships' => $resourceManager->getRelationships(),
        ]);
    }

    /**
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
        $fields = $resourceManager->getFields(ResourceManager::SHOW_ON_UPDATE);

        if ($validationRules) {
            $request->validate($validationRules);
        }

        $fields = array_filter($fields, function ($field) use ($validationRules) {
            $column = $field->isRelationshipField ? $field->foreignKey : $field->column;
            $fieldValidation = $validationRules[$column] ?? '';

            // Exclude the field from being processed
            if ($fieldValidation && strpos($fieldValidation, 'nullable') !== false) {
                return false;
            }

            return $field->showOnUpdate;
        });

        $model = $resourceModel::find($id);

        if (!is_object($model)) {
            return response()->json(['Model not found.'], 404);
        }

        foreach ($fields as $field) {
            $column = $field->isRelationshipField ? $field->foreignKey : $field->column;

            $field->fillAttributeFromRequest($request, $model, $column);
        }

        $model->save();

        return response()->json($model);
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
        $fields = $resourceManager->getFields(ResourceManager::SHOW_ON_CREATE);

        if ($validationRules) {
            $request->validate($validationRules);
        }

        $model = new $resourceModel;

        foreach ($fields as $field) {
            $column = $field->isRelationshipField ? $field->foreignKey : $field->column;

            $field->fillAttributeFromRequest($request, $model, $column);
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
