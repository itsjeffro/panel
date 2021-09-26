# Fields

When a resource is generated, it will contain an ID field to begin with. You may use any of the fields below by adding them
to the fields' method inside your generated resource.

- ID
- Password
- Text
- Textarea

```php
use Itsjeffro\Panel\Fields\ID;
use Itsjeffro\Panel\Fields\Text;

public function fields(): array
{
    return [
        ID::make(),
        Text::make('Title'),
    ];
}
```

## Field Visibility

- `showOnIndex()`
- `showOnDisplay()`
- `showOnCreate()`
- `showOnUpdate()`
- `hideOnIndex()`
- `hideOnDisplay()`
- `hideOnCreate()`
- `hideOnUpdate()`

## Relationships

### BelongsTo

```php
use Itsjeffro\Panel\Fields\BelongsTo;

BelongsTo::make('User'),
```

### MorphToMany

```php
use Itsjeffro\Panel\Fields\MorphToMany;

MorphToMany::make('Roles'),
```

### HasMany

```php
use Itsjeffro\Panel\Fields\HasMany;

HasMany::make('Posts'),
```