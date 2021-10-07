<?php

namespace Itsjeffro\Panel\Http\Controllers;

use Exception;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Database\QueryException;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Itsjeffro\Panel\Fields\Field;
use Itsjeffro\Panel\Services\ResourceHandler;
use Itsjeffro\Panel\Services\ResourceModel;

class ResourceController extends Controller
{
    /**
     * Retrieve paginated models from a given resource.
     *
     * @throws \Exception
     */
    public function index(Request $request, string $resourceName): JsonResponse
    {
        $resourceModel = new ResourceModel($resourceName);
        $handler = new ResourceHandler($resourceModel);
        $models = $handler->index($resourceName, $request);

        return response()->json($models);
    }

    /**
     * Retrieve a single model from a given resource.
     */
    public function show(string $resourceName, string $id): JsonResponse
    {
        try {
            $resourceModel = new ResourceModel($resourceName);
            $resource = $resourceModel->resolveResource();

            $with = $resourceModel
                ->getWith()
                ->toArray();

            $model = $resource->resolveModel()
                ->with($with)
                ->find($id);

            return response()->json([
                'groups' => $resourceModel->getGroupedFields($model, Field::SHOW_ON_DETAIL)
            ]);
        } catch (\Exception $e) {
            return response()->json($e->getMessage(), 500);
        }
    }

    /**
     * Retrieve a single model to edit.
     */
    public function edit(string $resourceName, string $id): JsonResponse
    {
        try {
            $resourceModel = new ResourceModel($resourceName);
            $resource = $resourceModel->resolveResource();

            $with = $resourceModel
                ->getWith()
                ->toArray();

            $model = $resource->resolveModel()
                ->with($with)
                ->find($id);

            return response()->json([
                'name' => [
                    'singular' => $resource->modelName(),
                    'plural' => $resource->modelPluralName(),
                ],
                'groups' => $resourceModel->getGroupedFields($model, Field::SHOW_ON_UPDATE),
            ]);
        } catch (\Exception $e) {
            return response()->json($e->getMessage(), 500);
        }
    }

    /**
     * Update a single model from a given resource.
     *
     * @throws \Exception
     */
    public function update(Request $request, string $resourceName, string $id): JsonResponse
    {
        try {
            $resourceModel = new ResourceModel($resourceName);
            $handler = new ResourceHandler($resourceModel);
            $model = $handler->update($request, $id);

            return response()->json($model);
        } catch (ModelNotFoundException $e) {
            return response()->json(['message' => "Resource [{$resourceName}] not found"], 404);
        } catch (QueryException $e) {
            return response()->json(['message' => $e->getMessage()], 500);
        }
    }

    /**
     * Create a new model for a given resource.
     *
     * @throws Exception
     */
    public function store(Request $request, string $resourceName): JsonResponse
    {
        try {
            $resourceModel = new ResourceModel($resourceName);
            $handler = new ResourceHandler($resourceModel);
            $model = $handler->store($request);

            return response()->json($model, 201);
        } catch (QueryException $e) {
            return response()->json(['message' => $e->getMessage()], 500);
        }
    }

    /**
     * Delete a single model from a given resource.
     *
     * @throws Exception
     */
    public function destroy(string $resourceName, string $id): JsonResponse
    {
        try {
            $resourceModel = new ResourceModel($resourceName);
            $resource = $resourceModel->resolveResource();
            $model = $resource->resolveModel()->find($id);

            if (!$model) {
                throw new ModelNotFoundException();
            }

            $model->delete();

            return response()->json(null, 204);
        } catch (ModelNotFoundException $e) {
            return response()->json([
                'message' => "Resource [{$resourceName}] not found",
            ], 404);
        }
    }
}
