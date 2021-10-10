<?php

namespace Itsjeffro\Panel\Fields;

use Illuminate\Support\Str;
use Itsjeffro\Panel\Block;

class HasMany extends Field
{
    /**
     * @var bool
     */
    public $isRelationshipField = true;

    /**
     * @var string
     */
    public $component = 'HasMany';

    /**
     * {@inheritdoc}
     */
    public $visibility = [
        self::SHOW_ON_DETAIL,
    ];

    /**
     * Instantiate a new instance of the child class utilising
     * methods from the extended Field class to use.
     */
    public static function make(?string $name = null, ?string $nameColumn = null, ?string $resourceNamespace = null)
    {
        $childClass = static::class;
        $classSegments = explode('\\', $childClass);

        $name = empty($name) ? end($classSegments) : $name;
        $nameColumn = empty($nameColumn) ? Str::snake(strtolower($name)) : $nameColumn;

        return new Block($name, [
            new $childClass($name, $nameColumn, $resourceNamespace),
        ]);
    }
}
