# Fields

When a resource is generated, it will contain an ID field to begin with. You may use any of the fields below by adding them
to the fields' method inside your generated resource.

- `BelongsTo`
- `DateTime`
- `ID`
- `MorphToMany`
- `Password`
- `Text`
- `Textarea`

```php
<?php

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

## Field blocks

Using the `Block` class can help group fields so that it's easier to view many fields on the page.

```php
use Itsjeffro\Panel\Block;
use Itsjeffro\Panel\Fields\DateTime;
use Itsjeffro\Panel\Fields\ID;

public function fields(): array
{
    return [
        ID::make(),
    
        new Block('Timestamps', [
            DateTime::make('Created At')->hideFromCreate()->hideFromUpdate(),
            DateTime::make('Updated At')->hideFromCreate()->hideFromUpdate(),
        ])
    ];
}
```