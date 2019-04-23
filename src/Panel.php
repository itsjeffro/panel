<?php

namespace Itsjeffro\Panel;

use Illuminate\Support\Str;
use Symfony\Component\Finder\Finder;

class Panel
{
    /**
     * @var array
     */
    private static $resources = [];

    /**
     * Get registered resources.
     *
     * @return array
     */
    public static function getResources(): array
    {
        return array_map(function ($resource) {
            $resourcePath = $resource;
            $resource = explode('\\', $resource);
            $name = Str::plural(end($resource));

            return [
                'name' => $name,
                'slug' => Str::kebab($name),
                'path' => $resourcePath,
            ];
        }, self::$resources);
    }

    /**
     * Return resources.
     *
     * @param string path
     * @return void
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
     *
     * @param object $file
     * @param string $basePath
     * @return string
     */
    public static function getClassName($file, string $basePath): string
    {
        $class = trim(str_replace([$basePath, '.php'], ['', ''], $file->getRealPath()), DIRECTORY_SEPARATOR);

        return str_replace(DIRECTORY_SEPARATOR, '\\', ucfirst($class));
    }

    /**
     * Return panel script variables.
     *
     * @return array
     */
    public static function scriptVariables()
    {
        return [
            'prefix' => config('panel.prefix'),
            'resources' => self::getResources(),
        ];
    }
}
