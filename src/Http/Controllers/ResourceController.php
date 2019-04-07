<?php

namespace Itsjeffro\Panel\Http\Controllers;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Routing\Controller;
use Illuminate\Support\Str;
use Itsjeffro\Panel\Resource;

class ResourceController extends Controller
{
    /**
     * Path to resources.
     *
     * @var string
     */
    public $resourcesPath = 'app/Panel';

    /**
     * List resources from resource type.
     *
     * @param string $resource
     * @return \Illuminate\Http\JsonResponse
     * @throws \Exception
     */
    public function index(string $resource)
    {
        $resource = $this->resourceFromResourceSlug($resource);
        $model = $this->modelFromResource($resource);
        $resourceName = explode('\\', $resource->model);
        $name = end($resourceName);

        $indexes = [];

        foreach ($resource->fields() as $field) {
            $indexes[] = $field;
        }

        return response()->json([
            'name' => [
                'singular' => $name,
                'plural' => Str::plural($name),
            ],
            'indexes' => $indexes,
            'model_data' => $model::paginate(),
        ]);
    }

    /**
     * Get single model resource.
     *
     * @param string $resource
     * @param string $id
     * @throws \Exception
     * @return Illuminate\Http\JsonResponse
     */
    public function show(string $resource, string $id)
    {
        $resource = $this->resourceFromResourceSlug($resource);
        $model = $this->modelFromResource($resource);
        $resourceName = explode('\\', $resource->model);
        $name = end($resourceName);

        $fields = [];

        foreach ($resource->fields() as $field) {
            $fields[] = $field;
        }

        return response()->json([
            'name' => [
                'singular' => $name,
                'plural' => Str::plural($name),
            ],
            'fields' => $fields,
            'model_data' => $model::find($id),
        ]);
    }

    /**
     * Update single model resource.
     *
     * @param string $resource
     * @param string $id
     * @return Illuminate\Http\JsonResponse
     */
    public function update(string $resource, string $id)
    {
        //
    }

    /**
     * Return resource class.
     *
     * @param string $resourceSlug
     * @return mixed
     */
    public function resourceFromResourceSlug(string $resourceSlug): Resource
    {
        $resourceSlug = ucfirst(Str::singular($resourceSlug));
        $class = '\\'.str_replace(DIRECTORY_SEPARATOR, '\\', ucfirst($this->resourcesPath)).'\\'.$resourceSlug;

        return (new $class);
    }

    /**
     * Return model instance from resource.
     *
     * @param \Itsjeffro\Panel\Resource $resource
     * @return \Illuminate\Database\Eloquent\Model
     * @throws \Exception
     */
    public function modelFromResource(Resource $resource): Model
    {
        $model = app()->make($resource->model);

        if (!$model instanceof Model) {
            throw new \Exception('Class is not an instance of Model');
        }

        return $model;
    }

    /**
     * Get class name from path.
     *
     * @param object $file
     * @param string $basePath
     * @return string
     */
    public function getClassName($file, string $basePath): string
    {
        $class = trim(str_replace([$basePath, '.php'], ['', ''], $file), DIRECTORY_SEPARATOR);

        return str_replace(DIRECTORY_SEPARATOR, '\\', ucfirst($class));
    }
}
