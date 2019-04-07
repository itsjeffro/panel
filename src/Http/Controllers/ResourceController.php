<?php

namespace Itsjeffro\Panel\Http\Controllers;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Illuminate\Support\Str;
use Itsjeffro\Panel\Resource;
use Symfony\Component\Finder\Finder;

class ResourceController extends Controller
{
    /**
     * Path to resources.
     *
     * @var string
     */
    public $resourcesPath = 'app/Panel';

    /**
     * List resources.
     *
     * @return Illuminate\Http\JsonResponse
     */
    public function index()
    {
        $registeredResources = $this->resourcesIn();

        $resources = array_map(function ($resource) {
            $resource = explode('\\', $resource);
            $name = Str::plural(end($resource));

            return [
                'name' => $name,
                'slug' => Str::kebab($name),
            ];
        }, $registeredResources);

        return response()->json($resources);
    }

    /**
     * @param string $resource
     * @throws \Exception
     * @return Illuminate\Http\JsonResponse
     */
    public function show(string $resource)
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
     * Return resources.
     *
     * @return array
     */
    public function resourcesIn(): array
    {
        $finder = new Finder();
        $files = $finder->files()->in(base_path($this->resourcesPath));

        $resources = [];

        foreach ($files as $file) {
            $resources[] = $this->getClassName($file, base_path());
        }

        return $resources;
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
