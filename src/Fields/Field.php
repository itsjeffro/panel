<?php

namespace Itsjeffro\Panel\Fields;

use Illuminate\Http\Request;

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
     * @var string
     */
    public $relation = '';

    /**
     * @var bool
     */
    public $isRelationshipField = false;

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
     * @var string
     */
    public $component = 'Text';

    /**
     * @var array
     */
    public $rules = [];

    /**
     * Field constructor.
     *
     * @param string $name
     * @param string $column
     * @param string $relation
     */
    public function __construct(string $name = '', string $column = '', string $relation = '')
    {
        $this->name = $name;
        $this->column = $column;
        $this->relation = $relation;
    }

    /**
     * Instantiate a new instance of the child class utilising
     * methods from the extended Field class to use.
     *
     * @param string $name
     * @param string $column
     * @param string $relation
     * @return \Itsjeffro\Panel\Fields\Field
     */
    public static function make(string $name = '', string $column = '', string $relation = ''): Field
    {
        $childClass = static::class;
        $classSegments = explode('\\', $childClass);

        $name = empty($name) ? end($classSegments) : $name;
        $column = empty($column) ? strtolower($name) : $column;
        $relation = empty($relation) ? $name : $relation;

        return new $childClass($name, $column, $relation);
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

    /**
     * Rules for the specified field.
     *
     * @param array $rules
     * @return $this
     */
    public function rules(array $rules = []): self
    {
        $this->rules = $rules;
        return $this;
    }

    /**
     * Fill attribute from request.
     *
     * @param Request $request
     * @param $model
     * @param $field
     */
    public function fillAttributeFromRequest(Request $request, $model, $field)
    {
        $model->{$field} = $request->input($field);
    }
}
