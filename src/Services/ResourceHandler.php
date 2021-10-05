<?php

namespace Itsjeffro\Panel\Services;

use Exception;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Http\Request;
use Illuminate\Support\Collection;
use Itsjeffro\Panel\Fields\Field;

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
    public function index(Request $request): array
    {
        $resource = $this->resourceModel->resolveResource();
        $model = $resource->resolveModel();
        $with = $this->resourceModel->getWith()->toArray();

        $relations = $request->get('relation', []);
        $models = $model::with($with)->orderBy('id', 'desc');

        foreach ($relations as $relation => $id) {
            $models = $models->where($relation, $id);
        }

        if ($request->exists('search')) {
            $models = $models->where(function ($query) use ($resource, $request) {
                foreach ($resource->search as $column) {
                    $query->orWhere($column, 'LIKE', $request->input('search').'%');
                }
            });
        }

        $indexResults = $models->paginate();

        $indexResults->getCollection()->transform(function ($item) use ($resource) {
            return [
                'resourceId' => $item->getKey(),
                'resourceName' => $item->{$resource->title},
                'resourceFields' => $this->resourceModel->getResourceIndexFields($item),
            ];
        });

        return [
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

            if ($field->isRelationshipField) {
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
}
