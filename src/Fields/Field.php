<?php

namespace Itsjeffro\Panel\Fields;

abstract class Field
{
    /**
     * @var string
     */
    public $name = '';

    /**
     * @var string
     */
    public $column = '';

    /**
     * @var bool
     */
    public $showOnIndex = false;
    
    /**
     * @var bool
     */
    public $showOnDetail = false;
    
    /**
     * @var bool
     */
    public $showOnCreate = false;
    
    /**
     * @var bool
     */
    public $showOnUpdate = false;

    /**
     * Field constructor.
     *
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
        $classSegments = explode('\\', $childClass);

        $name = empty($name) ? end($classSegments) : $name;
        $column = empty($column) ? strtolower($name) : $column;

        return new $childClass($name, $column);
    }
    
    /**
     * Set the field to be indexed everywhere.
     *
     * @return self
     */
    public function index(): self
    {
        $this->showOnIndex = true;
        $this->showOnDetail = true;
        $this->showOnCreate = true;
        $this->showOnUpdate = true;
        return $this;
    }

    /**
     * Set the field to be hidden from resource index.
     *
     * @return self
     */
    public function hideFromIndex(): self
    {
        $this->showOnIndex = false;
        return $this;
    }

    /**
     * Set the field to be hidden from resource details.
     *
     * @return self
     */
    public function hideFromDetail(): self
    {
        $this->showOnDetail = false;
        return $this;
    }

    /**
     * Set the field to be hidden from resource create.
     *
     * @return self
     */
    public function hideFromCreate(): self
    {
        $this->showOnCreate = false;
        return $this;
    }

    /**
     * Set the field to be hidden from resource update.
     *
     * @return self
     */
    public function hideFromUpdate(): self
    {
        $this->showOnUpdate = false;
        return $this;
    }

    /**
     * Set the field to be indexed on the resource index.
     *
     * @return self
     */
    public function showOnIndex(): self
    {
        $this->showOnIndex = true;
        return $this;
    }

    /**
     * Set the field to be indexed on resource details.
     *
     * @return self
     */
    public function showOnDetail(): self
    {
        $this->showOnDetail = true;
        return $this;
    }

    /**
     * Set the field to be indexed on resource create.
     *
     * @return self
     */
    public function showOnCreate(): self
    {
        $this->showOnCreate = true;
        return $this;
    }

    /**
     * Set the field to be indexed on resource update.
     *
     * @return self
     */
    public function showOnUpdate(): self
    {
        $this->showOnUpdate = true;
        return $this;
    }
    
    /**
     * @return self
     */ 
    public function sortable(): self
    {
        return $this;
    }
}