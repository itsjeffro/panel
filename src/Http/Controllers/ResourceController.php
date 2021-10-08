<?php

namespace Itsjeffro\Panel\Http\Controllers;

use Exception;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Database\QueryException;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Itsjeffro\Panel\Fields\Field;
use Itsjeffro\Panel\Panel;
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
        $resourceHandler = new ResourceHandler();

        $models = $resourceHandler->index($resourceName, $request);

        return response()->json($models);
    }

    /**
     * Retrieve a single model from a given resource.
     */
    public function show(string $resourceName, string $id): JsonResponse
    {
        try {
            $resource = Panel::resolveResourceByName($resourceName);

            $model = $resource->resolveModel()
                ->with($resource::$with)
                ->findOrFail($id);

            $resourceModel = new ResourceModel($resource);

            return response()->json([
                'groups' => $resourceModel->getGroupedFields($model, Field::SHOW_ON_DETAIL)
            ]);
        } catch (ModelNotFoundException $e) {
            return response()->json(['message' => "Resource [{$resourceName}] not found"], 404);
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
            $resource = Panel::resolveResourceByName($resourceName);
            $resourceModel = new ResourceModel($resource);

            $model = $resource->resolveModel()->with($resource::$with)->findOrFail($id);

            return response()->json([
                'name' => [
                    'singular' => $resource->modelName(),
                    'plural' => $resource->modelPluralName(),
                ],
                'groups' => $resourceModel->getGroupedFields($model, Field::SHOW_ON_UPDATE),
            ]);
        } catch (ModelNotFoundException $e) {
            return response()->json(['message' => "Resource [{$resourceName}] not found"], 404);
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
            $resourceHandler = new ResourceHandler();

            $model = $resourceHandler->update($resourceName, $request, $id);

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
            $resourceHandler = new ResourceHandler();

            $model = $resourceHandler->store($resourceName, $request);

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
            $resource = Panel::resolveResourceByName($resourceName);

            $model = $resource->resolveModel()->findOrFail($id);
            $model->delete();

            return response()->json(null, 204);
        } catch (ModelNotFoundException $e) {
            return response()->json(['message' => "Resource [{$resourceName}] not found"], 404);
        }
    }
}
