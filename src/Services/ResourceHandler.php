<?php

namespace Itsjeffro\Panel\Services;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Http\Request;
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
     * @throws \Exception
     */
    public function index(Request $request): array
    {
        $resource = $this->resourceModel->getResourceClass();
        $model = $this->resourceModel->resolveModel();
        $with = $this->resourceModel->getWith()->toArray();

        $relations = $request->get('relation', []);
        $models = $model::with($with)->orderBy('id', 'desc');

        foreach ($relations as $relation => $id) {
            $models = $models->whereHas($relation, function (Builder $query) use ($id) {
                $query->where('id', $id);
            });
        }

        if ($request->exists('search')) {
            foreach ($resource->search as $column) {
                $models->orWhere($column, 'LIKE', $request->input('search').'%');
            }
        }

        return [
            'name' => [
                'singular' => $resource->modelName(),
                'plural' => $resource->modelPluralName(),
            ],
            'fields' => $this->resourceModel->getFields(Field::SHOW_ON_INDEX),
            'model_data' => $models->paginate(),
        ];
    }

    /**
     * Returns a single resource's data response.
     *
     * @throws \Exception
     */
    public function show(string $id): array
    {
        $resource = $this->resourceModel->getResourceClass();
        $model = $this->resourceModel->resolveModel();
        $with = $this->resourceModel->getWith()->toArray();

        return [
            'name' => [
                'singular' => $resource->modelName(),
                'plural' => $resource->modelPluralName(),
            ],
            'groups' => $this->resourceModel->getGroupedFields(Field::SHOW_ON_DETAIL),
            'model_data' => $model::with($with)->find($id)
        ];
    }

    /**
     * Create resource.
     *
     * @return mixed
     * @throws \Exception
     */
    public function create(Request $request)
    {
        $resourceValidator = new ResourceValidator();
        $resourceModel = $this->resourceModel->resolveModel();

        $fields = $this->fields('showOnCreate');
        $validationRules = $resourceValidator->getValidationRules($resourceModel, $fields);

        if ($validationRules) {
            $request->validate($validationRules);
        }

        $model = new $resourceModel;

        foreach ($fields as $field) {
            $column = $field->column;

            if ($field->isRelationshipField) {
                $column = $resourceModel->{$column}()->getForeignKeyName();
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
     * @throws \Exception
     */
    public function update(Request $request, string $id)
    {
        $resourceValidator = new ResourceValidator();
        $resourceModel = $this->resourceModel->resolveModel();
        $model = $resourceModel::find($id);

        if (!$model) {
            throw new ModelNotFoundException();
        }

        $fields = $this->fields('showOnUpdate');
        $validationRules = $resourceValidator->getValidationRules($resourceModel, $fields);

        if ($validationRules) {
            $request->validate($validationRules);
        }

        foreach ($fields as $field) {
            $column = $field->column;

            if ($field->isRelationshipField) {
                $column = $resourceModel->{$column}()->getForeignKeyName();
            }

            $field->fillAttributeFromRequest($request, $model, $column);
        }

        $model->save();

        return $model;
    }

    /**
     * Delete resource.
     *
     * @throws ModelNotFoundException
     * @throws \Exception
     */
    public function delete(string $id): void
    {
        $resourceModel = $this->resourceModel->resolveModel();
        $model = $resourceModel::find($id);

        if (!is_object($model)) {
            throw new ModelNotFoundException();
        }

        $model->delete();
    }

    /**
     * Get updatable fields.
     *
     * @throws \Exception
     */
    protected function fields(string $showOnMethod): array
    {
        $fields = $this->resourceModel->getFields();

        $fields = array_filter($fields, function ($field) use ($showOnMethod) {
            return $field->{$showOnMethod};
        });

        return array_map(function ($field) {
            $field->rules = $field->rules + $field->rulesOnUpdate;
            return $field;
        }, $fields, []);
    }
}
