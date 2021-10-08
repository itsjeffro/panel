<?php

namespace Itsjeffro\Panel;

use Itsjeffro\Panel\Contracts\ResourceInterface;
use Symfony\Component\Finder\Finder;
use Symfony\Component\Finder\SplFileInfo;

class Panel
{
    /**
     * The registered resources in the specified resource path.
     *
     * @var array
     */
    private static $resources = [];

    /**
     * Get registered resources.
     */
    public static function getResources(): array
    {
        $resources = collect(self::$resources);

        return $resources->map(function ($resourcePath) {
            $resource = new $resourcePath;

            return [
                'name' => $resource->name(),
                'slug' => $resource->slug(),
                'path' => $resourcePath,
            ];
        })
        ->filter(function ($resource) {
            $resource = $resource['path'];

            return $resource::$displayInNavigation;
        })
        ->values()
        ->toArray();
    }

    /**
     * Return resources.
     */
    public static function resourcesIn(string $path): void
    {
        $finder = new Finder();
        $files = $finder->files()->in($path);

        $resources = [];

        foreach ($files as $file) {
            $resources[] = self::getClassName($file, base_path());
        }

        self::$resources = $resources;
    }

    /**
     * Get class name from path.
     */
    public static function getClassName(SplFileInfo $file, string $basePath): string
    {
        $class = trim(str_replace([$basePath, '.php'], ['', ''], $file->getRealPath()), DIRECTORY_SEPARATOR);

        return str_replace(DIRECTORY_SEPARATOR, '\\', ucfirst($class));
    }

    /**
     * Return panel script variables.
     */
    public static function scriptVariables(): array
    {
        return [
            'auth' => auth()->user(),
            'prefix' => config('panel.prefix'),
            'resources' => self::getResources(),
        ];
    }

    /**
     * Resolve instance of resource.
     */
    public static function resolveResourceByName(string $resourceName): ResourceInterface
    {
        foreach (static::getResources() as $resource) {
            if (strtolower($resource['slug']) === strtolower($resourceName)) {
                return (new $resource['path']);
            }
        }

        throw new \InvalidArgumentException(sprintf("Resource [%s] is not registered.", $resourceName));
    }
}
