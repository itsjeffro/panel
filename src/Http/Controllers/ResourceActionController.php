<?php

namespace Itsjeffro\Panel\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Illuminate\Support\Str;
use Itsjeffro\Panel\Services\ResourceModel;

class ResourceActionController extends Controller
{
    /**
     * Handle action for the given resource.
     */
    public function handle(Request $request, string $resource, string $action)
    {
        $resourceModel = new ResourceModel($resource);
        $resource = $resourceModel->resolveResource();
        $models = $resource->resolveModel()->find($request->get('model_ids'));

        $fields = [];

        foreach ($resource->actions($request) as $resourceAction) {
            $class = explode('\\', get_class($resourceAction));
            $className = Str::kebab(end($class));

            if ($className === $action) {
                $resourceAction->handle($fields, $models);
                break;
            }
        }

        return response()->json([
            'message' => 'Action successfully called.',
        ]);
    }
}
