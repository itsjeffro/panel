<?php

namespace Itsjeffro\Panel\Http\Controllers;

use Exception;
use Illuminate\Http\JsonResponse;
use Illuminate\Routing\Controller;
use Itsjeffro\Panel\Fields\Field;
use Itsjeffro\Panel\Panel;
use Itsjeffro\Panel\Services\ResourceModel;

class ResourceFieldController extends Controller
{
    /**
     * Get single model resource's fields.
     *
     * @throws Exception
     */
    public function show(string $resourceName): JsonResponse
    {
        $resource = Panel::resolveResourceByName($resourceName);
        $resourceModel = new ResourceModel($resource);
        $model = $resource->resolveModel();

        return response()->json([
            'data' => $resourceModel->getGroupedFields($model, Field::SHOW_ON_CREATE),
            'meta' => [
                'name' => [
                    'singular' => $resource->modelName(),
                    'plural' => $resource->modelPluralName(),
                ],
            ]
        ]);
    }
}
