# Actions

## Defining actions

```bash
php artisan panel:action BulkDelete
```

## Registering actions on a resource

```php
<?php

namespace App\Panel;

use Illuminate\Http\Request;
use Itsjeffro\Panel\Actions\BulkDelete;
use Itsjeffro\Panel\Resource;

class User extends Resource
{
    public function fields(): array
    {
        return [];
    }

    /**
     * Get the actions available for the resource.
     */
    public function actions(Request $request): array
    {
        return [
            new BulkDelete(),
        ];
    }
}
```