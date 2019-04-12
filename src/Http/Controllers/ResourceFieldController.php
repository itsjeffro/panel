<?php

namespace Itsjeffro\Panel\Http\Controllers;

use Illuminate\Routing\Controller;
use Illuminate\Support\Str;
use Itsjeffro\Panel\ResourceManager;

class ResourceFieldController extends Controller
{
    /**
     * Get single model resource's fields.
     *
     * @param string $resource
     * @throws \Exception
     * @return Illuminate\Http\JsonResponse
     */
    public function show(string $resource)
    {
        $resourceManager = new ResourceManager($resource);
        $name = $resourceManager->getName();

        return response()->json([
            'name' => [
                'singular' => $name,
                'plural' => Str::plural($name),
            ],
            'fields' => $resourceManager->getFields(),
        ]);
    }
}
