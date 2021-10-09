<?php

namespace Itsjeffro\Panel\Services;

use Illuminate\Support\Collection;
use Illuminate\Support\Str;

class ResourceAction
{
    /**
     * Return resource's actions.
     */
    public function prepareActions(array $actions): Collection
    {
        return  collect($actions)->map(function ($action) {
            $class = explode('\\', get_class($action));
            $className = Str::kebab(end($class));

            return [
                'name' => str_replace('-', ' ', Str::title($className)),
                'slug' => $className,
            ];
        });
    }
}
