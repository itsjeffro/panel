<?php

namespace Itsjeffro\Panel\Http\Controllers;

use Illuminate\Routing\Controller;
use Illuminate\Support\Str;
use Itsjeffro\Panel\ResourceManager;

class ResourceFieldController extends Controller
{
    /**
     * Path to resources.
     *
     * @var string
     */
    public $resourcesPath = 'app/Panel';

    /**
     * Get single model resource's fields.
     *
     * @param string $resource
     * @throws \Exception
     * @return Illuminate\Http\JsonResponse
     */
    public function show(string $resource)
    {
        $resourceManager = new ResourceManager($this->resourcesPath, $resource);
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
