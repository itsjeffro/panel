<?php

namespace Itsjeffro\Panel\Fields;

class FieldBuilder
{
    /**
     * @var string
     */
    public $name;

    /**
     * @var string
     */
    public $column;

    /**
     * @var bool
     */
    public $index = false;

    /**
     * FieldBuilder constructor.
     *
     * @param string $name
     * @param string $column
     */
    public function __construct(string $name, string $column)
    {
        $this->name = $this->getFieldNameFromClass($name);
        $this->column = empty($column) ? strtolower($this->getFieldNameFromClass($name)) : $column;
    }

    /**
     * @return $this
     */
    public function index()
    {
        $this->index = true;

        return $this;
    }

    /**
     * @param string $name
     * @return string
     */
    protected function getFieldNameFromClass(string $name): string
    {
        $name = explode('\\', $name);

        return end($name);
    }
}