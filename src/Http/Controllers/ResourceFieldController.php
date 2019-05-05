<?php

namespace Itsjeffro\Panel\Http\Controllers;

use Illuminate\Routing\Controller;
use Itsjeffro\Panel\ResourceManager;

class ResourceFieldController extends Controller
{
    /**
     * Get single model resource's fields.
     *
     * @param string $resource
     * @throws \Exception
     * @return \Illuminate\Http\JsonResponse
     */
    public function show(string $resource)
    {
        $resourceManager = new ResourceManager($resource);

        return response()->json([
            'name' => $resourceManager->getResourceName(),
            'fields' => $resourceManager->getFields(ResourceManager::SHOW_ON_CREATE),
            'relationships' => $resourceManager->getRelationships(ResourceManager::SHOW_ON_CREATE),
        ]);
    }
}
