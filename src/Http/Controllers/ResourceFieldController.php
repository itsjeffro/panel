<?php

namespace Itsjeffro\Panel\Http\Controllers;

use Illuminate\Routing\Controller;
use Itsjeffro\Panel\Services\ResourceModel;

class ResourceFieldController extends Controller
{
    /**
     * Get single model resource's fields.
     *
     * @throws \Exception
     * @return \Illuminate\Http\JsonResponse
     */
    public function show(string $resourceName)
    {
        $resourceModel = new ResourceModel($resourceName);

        return response()->json([
            'name' => $resourceModel->getResourceName(),
            'fields' => $resourceModel->getFields(ResourceModel::SHOW_ON_CREATE),
            'relationships' => $resourceModel->getRelationships(ResourceModel::SHOW_ON_CREATE),
        ]);
    }
}
