<?php

namespace Itsjeffro\Panel\Http\Controllers;

use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Database\QueryException;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Itsjeffro\Panel\ModelCreate;
use Itsjeffro\Panel\ModelDelete;
use Itsjeffro\Panel\ModelResults;
use Itsjeffro\Panel\ModeUpdate;
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
        $resourceModel = new ModelResults($resourceManager, $request);

        return response()->json($resourceModel->get());
    }

    /**
     * Retrieve a single model from a given resource.
     *
     * @param string $resource
     * @param string $id
     * @return \Illuminate\Http\JsonResponse
     */
    public function show(string $resource, string $id)
    {
        try {
            $resourceManager = new ResourceManager($resource);
            $model = $resourceManager->resolveModel();
            $with = $resourceManager->getWith();

            return response()->json([
                'name' => $resourceManager->getName(),
                'fields' => $resourceManager->getFields(ResourceManager::SHOW_ON_UPDATE),
                'model_data' => $model::with($with)->find($id),
                'relationships' => $resourceManager->getRelationships(),
            ]);
        } catch (\Exception $e) {
            return response()->json($e->getMessage(), 500);
        }
    }

    /**
     * Update a single model from a given resource.
     *
     * @param \Illuminate\Http\Request
     * @param string $resource
     * @param string $id
     * @return \Illuminate\Http\JsonResponse
     */
    public function update(Request $request, string $resource, string $id)
    {
        try {
            $resourceManager = new ResourceManager($resource);
            $resourceModel = new ModeUpdate($resourceManager, $request, $id);

            $model = $resourceModel->update();

            return response()->json($model);
        } catch (ModelNotFoundException $e) {
            return response()->json(['message' => sprintf('Resource [%s] not found', $resource)], 404);
        } catch (QueryException $e) {
            return response()->json(['message' => $e->getMessage()], 500);
        } catch (\Exception $e) {
            return response()->json($e->getMessage(), 500);
        }
    }


    /**
     * Create a new model for a given resource.
     *
     * @param Request $request
     * @param string $resource
     * @return \Illuminate\Http\JsonResponse
     */
    public function store(Request $request, string $resource)
    {
        try {
            $resourceManager = new ResourceManager($resource);
            $resourceModel = new ModelCreate($resourceManager, $request);

            $model = $resourceModel->create();

            return response()->json($model, 201);
        } catch (QueryException $e) {
            return response()->json(['message' => $e->getMessage()], 500);
        } catch (\Exception $e) {
            return response()->json($e->getMessage(), 500);
        }
    }

    /**
     * Delete a single model from a given resource.
     *
     * @param string $resource
     * @param string $id
     * @return \Illuminate\Http\JsonResponse
     */
    public function destroy(string $resource, string $id)
    {
        try {
            $resourceManager = new ResourceManager($resource);
            $resourceModel = new ModelDelete($resourceManager, $id);

            $resourceModel->delete();

            return response()->json(null, 204);
        } catch (ModelNotFoundException $e) {
            return response()->json(['message' => sprintf('Resource [%s] not found', $resource)], 404);
        } catch (\Exception $e) {
            return response()->json($e->getMessage(), 500);
        }
    }
}
