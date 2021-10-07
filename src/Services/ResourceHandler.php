<?php

namespace Itsjeffro\Panel\Services;

use Exception;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Http\Request;
use Illuminate\Support\Collection;
use Illuminate\Support\Str;
use Itsjeffro\Panel\Fields\BelongsTo;
use Itsjeffro\Panel\Fields\Field;
use Itsjeffro\Panel\Fields\HasMany;
use Itsjeffro\Panel\Fields\MorphToMany;

class ResourceHandler
{
    /**
     * @var ResourceModel
     */
    private $resourceModel;

    public function __construct(ResourceModel $resourceModel)
    {
        $this->resourceModel = $resourceModel;
    }

    /**
     * Return built data response.
     *
     * @throws Exception
     */
    public function index(string $resourceName, Request $request): array
    {
        $queryResource = $request->get('resource');
        $queryResourceId = $request->get('resourceId');
        $queryRelationship = $request->get('relationship');

        // Handle filtering by relationship from query.
        if (!$queryResource || !$queryResourceId || !$queryRelationship) {
            $resourceModel = new ResourceModel($resourceName);
            $resource = $resourceModel->resolveResource();

            $query = $resource->resolveModel()
                ->with($resource::$with)
                ->orderBy('id', 'desc');
        } else {
            $resourceModel = new ResourceModel($queryResource);
            $resource = $resourceModel->resolveResource();

            $query = $resource->resolveModel()
                ->find($queryResourceId)
                ->{$queryRelationship}()
                ->getQuery();
        }

        // Handle search query.
        if ($request->exists('search')) {
            $query = $query->where(function ($query) use ($resource, $request) {
                foreach ($resource->search as $column) {
                    $query->orWhere($column, 'LIKE', $request->input('search').'%');
                }
            });
        }

        $indexResults = $query->paginate();

        // Handle transforming paginated models.
        $indexResults
            ->getCollection()
            ->transform(function ($item) use ($resource, $resourceModel) {
                return [
                    'resourceId' => $item->getKey(),
                    'resourceName' => $item->{$resource->title},
                    'resourceFields' => $resourceModel->getResourceIndexFields($item),
                ];
            });

        return [
            'actions' => $this->prepareActions($resource->actions($request)),
            'name' => [
                'singular' => $resource->modelName(),
                'plural' => $resource->modelPluralName(),
            ],
            'model_data' => $indexResults,
        ];
    }

    /**
     * Create resource.
     *
     * @return mixed
     * @throws Exception
     */
    public function store(Request $request)
    {
        $resourceValidator = new ResourceValidator();
        $resource = $this->resourceModel->resolveResource();
        $model = $resource->resolveModel();

        $fields = $this->fields(Field::SHOW_ON_CREATE)->toArray();
        $validationRules = $resourceValidator->getValidationRules($model, $fields);

        if ($validationRules) {
            $request->validate($validationRules);
        }

        foreach ($fields as $field) {
            $column = $field->column;

            if ($field->isRelationshipField) {
                $column = $model->{$column}()->getForeignKeyName();
            }

            $field->fillAttributeFromRequest($request, $model, $column);
        }

        $model->save();

        return $model;
    }

    /**
     * Validate and update model.
     *
     * @return mixed
     * @throws ModelNotFoundException
     * @throws Exception
     */
    public function update(Request $request, string $id)
    {
        $resourceValidator = new ResourceValidator();
        $resource = $this->resourceModel->resolveResource();
        $model = $resource->resolveModel()->find($id);

        if (!$model) {
            throw new ModelNotFoundException();
        }

        $fields = $this->fields(Field::SHOW_ON_UPDATE)->toArray();
        $validationRules = $resourceValidator->getValidationRules($model, $fields);

        if ($validationRules) {
            $request->validate($validationRules);
        }

        foreach ($fields as $field) {
            $column = $field->column;

            if ($field instanceof MorphToMany) {
                $column = $field->column;
            }

            if ($field instanceof HasMany) {
                $column = $model->{$column};
            }

            if ($field instanceof BelongsTo) {
                $column = $model->{$column}()->getForeignKeyName();
            }

            $field->fillAttributeFromRequest($request, $model, $column);
        }

        $model->save();

        return $model;
    }

    /**
     * Get updatable fields.
     *
     * @throws Exception
     */
    protected function fields(string $visibility): Collection
    {
        return $this->resourceModel
            ->getFields()
            ->filter(function ($field) use ($visibility) {
                return $field instanceof Field && $field->hasVisibility($visibility);
            })
            ->map(function ($field) {
                $field->rules = $field->rules + $field->rulesOnCreate;
                return $field;
            });
    }

    /**
     * Return resource's actions.
     */
    protected function prepareActions(array $actions)
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
