<?php

namespace Itsjeffro\Panel\Actions;

use Illuminate\Support\Collection;
use Itsjeffro\Panel\Actions\Action;

class BulkDelete extends Action
{
    /**
     * Performs an action on the given models.
     *
     * @return mixed
     */
    public function handle(array $fields, Collection $models)
    {
        foreach ($models as $model) {
            $model->delete();
        }
    }

    /**
     * Get the fields available for this action.
     */
    public function fields(): array
    {
        return [];
    }
}
