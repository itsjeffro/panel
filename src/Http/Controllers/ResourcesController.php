<?php

namespace Itsjeffro\Panel\Http\Controllers;

use Illuminate\Routing\Controller;
use Illuminate\Support\Str;
use Symfony\Component\Finder\Finder;

class ResourcesController extends Controller
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