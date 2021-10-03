<?php

namespace Itsjeffro\Panel\Http\Controllers;

use Exception;
use Illuminate\Http\JsonResponse;
use Illuminate\Routing\Controller;
use Itsjeffro\Panel\Fields\Field;
use Itsjeffro\Panel\Services\ResourceModel;

class ResourceFieldController extends Controller
{
    /**
     * Get single model resource's fields.
     *
     * @throws Exception
     * @return JsonResponse
     */
    public function show(string $resourceName)
    {
        $resourceModel = new ResourceModel($resourceName);
        $resource = $resourceModel->getResourceClass();
        $model = $resource->resolveModel();

        return response()->json([
            'name' => [
                'singular' => $resource->modelName(),
                'plural' => $resource->modelPluralName(),
            ],
            'groups' => $resourceModel->getGroupedFields($model, Field::SHOW_ON_CREATE),
        ]);
    }
}
