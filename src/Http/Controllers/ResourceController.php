<?php

namespace Itsjeffro\Panel\Http\Controllers;

use Illuminate\Database\QueryException;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Itsjeffro\Panel\ResourceManager;

class ResourceController extends Controller
{
    /**
     * Retrieve paginated models from a given resource.
     *
     * @param Request $request
     * @param string $resource
     * @return \Illuminate\Http\JsonResponse
     * @throws \Exception
     */
    public function index(Request $request, string $resource)
    {
        $resourceManager = new ResourceManager($resource);
        $resource = $resourceManager->getClass();
        $model = $resourceManager->resolveModel();
        $with = $resourceManager->getWith();

        $models = $model::with($with)->orderBy('id', 'desc');

        if ($request->exists('search')) {
            foreach ($resource->search as $column) {
                $models->orWhere($column, 'LIKE', $request->input('search').'%');
            }
        }

        return response()->json([
            'name' => $resourceManager->getName(),
            'fields' => $resourceManager->getFields(ResourceManager::SHOW_ON_INDEX),
            'model_data' => $models->paginate(),
        ]);
    }

    /**
     * Retrieve a single model from a given resource.
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
        $with = $resourceManager->getWith();

        return response()->json([
            'name' => $resourceManager->getName(),
            'fields' => $resourceManager->getFields(ResourceManager::SHOW_ON_UPDATE),
            'model_data' => $model::with($with)->find($id),
            'relationships' => $resourceManager->getRelationships(),
        ]);
    }

    /**
     * Update a single model from a given resource.
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
        $validationRules = $resourceManager->getValidationRules(ResourceManager::SHOW_ON_UPDATE);
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

        try {
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
        } catch (QueryException $e) {
            return response()->json(['message' => $e->getMessage()], 500);
        }
    }


    /**
     * Create a new model for a given resource.
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
        $validationRules = $resourceManager->getValidationRules(ResourceManager::SHOW_ON_CREATE);
        $fields = $resourceManager->getFields(ResourceManager::SHOW_ON_CREATE);

        if ($validationRules) {
            $request->validate($validationRules);
        }

        try {
            $model = new $resourceModel;

            foreach ($fields as $field) {
                $column = $field->isRelationshipField ? $field->foreignKey : $field->column;

                $field->fillAttributeFromRequest($request, $model, $column);
            }

            $model->save();

            return response()->json($model, 201);
        } catch (QueryException $e) {
            return response()->json(['message' => $e->getMessage()], 500);
        }
    }

    /**
     * Delete a single model from a given resource.
     *
     * @param string $resource
     * @param string $id
     * @return \Illuminate\Http\JsonResponse
     * @throws \Exception
     */
    public function destroy(string $resource, string $id)
    {
        $resourceManager = new ResourceManager($resource);
        $resourceModel = $resourceManager->resolveModel();
        $model = $resourceModel::find($id);

        if (!is_object($model)) {
            return response()->json(['Model not found.'], 404);
        }

        $model->delete();

        return response()->json(null, 204);
    }
}
