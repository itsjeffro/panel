<?php

namespace Itsjeffro\Panel\Fields;

use Illuminate\Http\Request;
use Illuminate\Support\Str;

abstract class Field
{
    /**
     * Visibility.
     */
    const SHOW_ON_CREATE = 'SHOW_ON_CREATE';
    const SHOW_ON_DETAIL = 'SHOW_ON_DETAIL';
    const SHOW_ON_INDEX = 'SHOW_ON_INDEX';
    const SHOW_ON_UPDATE = 'SHOW_ON_UPDATE';

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
    public $resourceNamespace = '';

    /**
     * @var object|null
     */
    public $relation = null;

    /**
     * @var bool
     */
    public $isRelationshipField = false;

    /**
     * @var string
     */
    public $component = 'Text';

    /**
     * @var array
     */
    public $rules = [];

    /**
     * @var array
     */
    public $rulesOnCreate = [];

    /**
     * @var array
     */
    public $rulesOnUpdate = [];

    /**
     * @var string[]
     */
    public $visibility = [];

    /**
     * Field constructor.
     */
    public function __construct(?string $name = null, ?string $nameColumn = null, ?string $resourceNamespace = null)
    {
        $this->name = $name;
        $this->column = $nameColumn;
        $this->resourceNamespace = $this->namespaceResource($name, $resourceNamespace);
    }

    /**
     * Instantiate a new instance of the child class utilising
     * methods from the extended Field class to use.
     */
    public static function make(?string $name = null, ?string $nameColumn = null, ?string $resourceNamespace = null): Field
    {
        $childClass = static::class;
        $classSegments = explode('\\', $childClass);

        $name = empty($name) ? end($classSegments) : $name;
        $nameColumn = empty($nameColumn) ? Str::snake($name) : $nameColumn;

        return new $childClass($name, $nameColumn, $resourceNamespace);
    }
    
    /**
     * Set the field to be indexed everywhere.
     *
     * @return self
     */
    public function index(): self
    {
        $this->addVisibility(static::SHOW_ON_INDEX);
        $this->addVisibility(static::SHOW_ON_DETAIL);
        $this->addVisibility(static::SHOW_ON_CREATE);
        $this->addVisibility(static::SHOW_ON_UPDATE);

        return $this;
    }

    /**
     * Set the field to be hidden from resource index.
     *
     * @return self
     */
    public function hideFromIndex(): self
    {
        $this->removeVisibility(static::SHOW_ON_INDEX);

        return $this;
    }

    /**
     * Set the field to be hidden from resource details.
     *
     * @return self
     */
    public function hideFromDetail(): self
    {
        $this->removeVisibility(static::SHOW_ON_DETAIL);

        return $this;
    }

    /**
     * Set the field to be hidden from resource create.
     *
     * @return self
     */
    public function hideFromCreate(): self
    {
        $this->removeVisibility(static::SHOW_ON_CREATE);

        return $this;
    }

    /**
     * Set the field to be hidden from resource update.
     *
     * @return self
     */
    public function hideFromUpdate(): self
    {
        $this->removeVisibility(static::SHOW_ON_UPDATE);

        return $this;
    }

    /**
     * Set the field to be indexed on the resource index.
     *
     * @return self
     */
    public function showOnIndex(): self
    {
        $this->addVisibility(static::SHOW_ON_INDEX);

        return $this;
    }

    /**
     * Set the field to be indexed on resource details.
     *
     * @return self
     */
    public function showOnDetail(): self
    {
        $this->addVisibility(static::SHOW_ON_DETAIL);

        return $this;
    }

    /**
     * Set the field to be indexed on resource create.
     *
     * @return self
     */
    public function showOnCreate(): self
    {
        $this->addVisibility(static::SHOW_ON_CREATE);

        return $this;
    }

    /**
     * Set the field to be indexed on resource update.
     *
     * @return self
     */
    public function showOnUpdate(): self
    {
        $this->addVisibility(static::SHOW_ON_UPDATE);

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
     * Rules for the specified field during create.
     *
     * @param array $rules
     * @return $this
     */
    public function createRules(array $rules = []): self
    {
        $this->rulesOnCreate = $rules;
        return $this;
    }

    /**
     * Rules for the specified field during update.
     *
     * @param array $rules
     * @return $this
     */
    public function updateRules(array $rules = []): self
    {
        $this->rulesOnUpdate = $rules;
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
        if ($request->exists($field)) {
            $model->{$field} = $request->input($field);
        }
    }

    /**
     * Returns name of the field.
     */
    public function getName(): string
    {
        return $this->name;
    }

    /**
     * Add field visibility.
     */
    protected function addVisibility(string $visibility): self
    {
        if (!in_array($visibility, $this->visibility)) {
            array_push($this->visibility, $visibility);
        }

        return $this;
    }

    /**
     * Remove field visibility.
     */
    protected function removeVisibility(string $visibilityToRemove):self
    {
        $this->visibility = array_filter($this->visibility, function ($visibility) use ($visibilityToRemove) {
            return $visibility !== $visibilityToRemove;
        });

        return $this;
    }

    /**
     * Returns resource's full namespace.
     */
    protected function namespaceResource(?string $name, ?string $namespaceResource): string
    {
        if ($namespaceResource) {
            return $namespaceResource;
        }

        return 'App\\Panel\\' . Str::singular($name);
    }
}
