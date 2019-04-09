<?php

namespace Itsjeffro\Panel\Fields;

abstract class Field
{
    /**
     * @var string
     */
    public $name = '';
    
    /**
     * @var bool
     */
    public $index = false;
    
    /**
     * @param string $name
     * @param string $column
     */
    public function __construct(string $name = '', string $column = '')
    {
        $this->name = $name;
        $this->column = $column;
    }

    /**
     * Instantiate a new instance of the child class utilising
     * methods from the extended Field class to use.
     *
     * @param string $name
     * @param string $column
     * @return \Field
     */
    public static function make(string $name = '', string $column = ''): Field
    {
        $childClass = static::class;
        $name = empty($name) ? $childClass : $name;
        $column = empty($column) ? strtolower($name) : $column;

        return new new $childClass($name, $column);
    }
    
    /**
     * Set the field to be indexed in the table.
     *
     * @return self
     */
    public function index(): self
    {
        $this->index = true;

        return $this;
    }
}