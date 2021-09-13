<?php

namespace Itsjeffro\Panel\Http\Controllers;

use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Database\QueryException;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Itsjeffro\Panel\Services\ResourceHandler;
use Itsjeffro\Panel\ResourceManager;

class ResourceController extends Controller
{
    /**
     * Retrieve paginated models from a given resource.
     *
     * @throws \Exception
     */
    public function index(Request $request, string $resource): JsonResponse
    {
        $resourceManager = new ResourceManager($resource);
        $handler = new ResourceHandler($resourceManager);
        $models = $handler->index($request);

        return response()->json($models);
    }

    /**
     * Retrieve a single model from a given resource.
     */
    public function show(string $resource, string $id): JsonResponse
    {
        try {
            $resourceManager = new ResourceManager($resource);
            $model = $resourceManager->resolveModel();
            $with = $resourceManager->getWith();

            return response()->json([
                'name' => $resourceManager->getResourceName(),
                'fields' => $resourceManager->getFields(),
                'model_data' => $model::with($with)->find($id),
                'relationships' => $resourceManager->getRelationships('', $id),
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
    public function update(Request $request, string $resource, string $id): JsonResponse
    {
        try {
            $resourceManager = new ResourceManager($resource);
            $handler = new ResourceHandler($resourceManager);
            $model = $handler->update($request, $id);

            return response()->json($model);
        } catch (ModelNotFoundException $e) {
            return response()->json(['message' => sprintf('Resource [%s] not found', $resource)], 404);
        } catch (QueryException $e) {
            return response()->json(['message' => $e->getMessage()], 500);
        }
    }


    /**
     * Create a new model for a given resource.
     *
     * @throws \Exception
     */
    public function store(Request $request, string $resource): JsonResponse
    {
        try {
            $resourceManager = new ResourceManager($resource);

            $handler = new ResourceHandler($resourceManager);
            $model = $handler->create($request);

            return response()->json($model, 201);
        } catch (QueryException $e) {
            return response()->json(['message' => $e->getMessage()], 500);
        }
    }

    /**
     * Delete a single model from a given resource.
     *
     * @throws \Exception
     */
    public function destroy(string $resource, string $id): JsonResponse
    {
        try {
            $resourceManager = new ResourceManager($resource);
            $handler = new ResourceHandler($resourceManager);
            $handler->delete($id);

            return response()->json(null, 204);
        } catch (ModelNotFoundException $e) {
            return response()->json([
                'message' => sprintf('Resource [%s] not found', $resource)
            ], 404);
        }
    }
}
