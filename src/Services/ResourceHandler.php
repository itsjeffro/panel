<?php

namespace Itsjeffro\Panel\Services;

use Exception;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Http\Request;
use Illuminate\Support\Arr;
use Itsjeffro\Panel\Fields\Field;
use Itsjeffro\Panel\Fields\MorphToMany;
use Itsjeffro\Panel\Panel;

class ResourceHandler
{
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
            $resource = Panel::resolveResourceByName($resourceName);
            $resourceModel = new ResourceModel($resource);

            $query = $resource->resolveModel()
                ->with($resource::$with)
                ->orderBy('id', 'desc');
        } else {
            $relatedResource = Panel::resolveResourceByName($queryResource);
            $resource = Panel::resolveResourceByName($queryRelationship);
            $resourceModel = new ResourceModel($resource);

            $query = $relatedResource->resolveModel()
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

        $paginationResults = $query->paginate();

        // Handle transforming paginated models.
        $paginationResults
            ->getCollection()
            ->transform(function ($item) use ($resource, $resourceModel) {
                return [
                    'resourceId' => $item->getKey(),
                    'resourceName' => $item->{$resource->title},
                    'resourceFields' => $resourceModel->getGroupedFields($item, Field::SHOW_ON_INDEX),
                ];
            });

        $paginated = $paginationResults->toArray();

        return [
            'data' => Arr::get($paginated, 'data'),
            'meta' => [
                'actions' => (new ResourceAction())->prepareActions($resource->actions($request)),
                'fields' => (new ResourceTable())->tableHeaders($resource),
                'name' => [
                    'plural' => $resource->modelPluralName(),
                    'singular' => $resource->modelName(),
                ]
            ] + Arr::except($paginated, [
                'data',
                'first_page_url',
                'last_page_url',
                'prev_page_url',
                'next_page_url',
            ]),
            'links' => [
                'first' => $paginated['first_page_url'] ?? null,
                'last' => $paginated['last_page_url'] ?? null,
                'prev' => $paginated['prev_page_url'] ?? null,
                'next' => $paginated['next_page_url'] ?? null,
            ]
        ];
    }

    /**
     * Create resource.
     *
     * @return mixed
     * @throws Exception
     */
    public function store(string $resourceName, Request $request)
    {
        $resource = Panel::resolveResourceByName($resourceName);
        $model = $resource->resolveModel();

        $resourceValidator = new ResourceValidator();
        $resourceField = new ResourceField();

        $fields = $resource->fieldsByVisibility(Field::SHOW_ON_CREATE)
            ->map(function ($field) {
                $field->rules = $field->rules + $field->rulesOnCreate;
                return $field;
            });

        $validationRules = $resourceValidator->getValidationRules($model, $fields);

        if ($validationRules) {
            $request->validate($validationRules);
        }

        $syncFields = $fields->filter(function ($field) {
            return $field instanceof MorphToMany;
        });

        $fields = $fields->filter(function ($field) {
            return !$field instanceof MorphToMany;
        });

        foreach ($fields as $field) {
            $column = $resourceField->column($model, $field);

            $field->fillAttributeFromRequest($request, $model, $column);
        }

        $model->save();

        if ($syncFields->isEmpty()) {
            return $model;
        }

        foreach ($syncFields as $syncField) {
            $column = $syncField->column;

            if ($syncField instanceof MorphToMany) {
                $column = $syncField->column;
            }

            $syncField->fillAttributeFromRequest($request, $model, $column);
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
    public function update(string $resourceName, Request $request, string $id)
    {
        $resource = Panel::resolveResourceByName($resourceName);
        $model = $resource->resolveModel()->find($id);

        $resourceValidator = new ResourceValidator();
        $resourceField = new ResourceField();

        if (!$model) {
            throw new ModelNotFoundException();
        }

        $fields = $resource->fieldsByVisibility(Field::SHOW_ON_UPDATE)
            ->map(function ($field) {
                $field->rules = $field->rules + $field->rulesOnUpdate;
                return $field;
            });

        $validationRules = $resourceValidator->getValidationRules($model, $fields);

        if ($validationRules) {
            $request->validate($validationRules);
        }

        foreach ($fields as $field) {
            $column = $resourceField->column($model, $field);

            $field->fillAttributeFromRequest($request, $model, $column);
        }

        $model->save();

        return $model;
    }
}
