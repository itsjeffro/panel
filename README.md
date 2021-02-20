# Panel

<p align="center">
    <a href="https://github.com/itsjeffro/panel/actions"><img src="https://github.com/itsjeffro/panel/workflows/tests/badge.svg" alt="Build Status"></a>
    <a href="https://packagist.org/packages/itsjeffro/panel"><img src="https://poser.pugx.org/itsjeffro/panel/d/total.svg" alt="Total Downloads"></a>
    <a href="https://packagist.org/packages/itsjeffro/panel"><img src="https://poser.pugx.org/itsjeffro/panel/v/stable.svg" alt="Latest Stable Version"></a>
    <a href="https://packagist.org/packages/itsjeffro/panel"><img src="https://poser.pugx.org/itsjeffro/panel/license.svg"></a>
</p>

Inspired by Laravel Nova. This package provides a separate administration panel to manage model data.

This is by no means a replacement or a competitor. I mainly created this package to see if I could create 
something similar as a learning experience.

### Installation
The package's main service provider will be automatically registered with Laravel's package auto-discovery.

```bash
composer require itsjeffro/panel
```

Publish the config, assets and application service provider.
```bash
php artisan vendor:publish --tag=panel-config
php artisan vendor:publish --tag=panel-assets
php artisan vendor:publish --tag=panel-provider
```

The published application service provider is where the path for the resources is configured. You may register the Panel service 
provider in the providers array in your config/app.php configuration file:

```php
App\Providers\PanelServiceProvider::class,
```

### Resources
Resources allow for mapping to your applications model.

You may generate a new resource by using the panel:resource console command.

```bash
php artisan panel:resource User
```

### Fields

When a resource is generated, it will contain an ID field to begin with. You may use any of the fields below by adding them
to the fields method inside your generated resource.

- ID
- Password
- Text
- Textarea

```php
public function fields()
{
    return [
        ID::make(),
        Text::make('Title'),
    ];
}
```

#### Field Visibility

- showOnIndex()
- showOnDisplay()
- showOnCreate()
- showOnUpdate()
- hideOnIndex()
- hideOnDisplay()
- hideOnCreate()
- hideOnUpdate()

### Relationships

- BelongsTo

### Roadmap

Since this is a project I plan to use quite often, there will be additional features I would like to add when needed.

#### Relationships

- [ ] HasMany

#### Fields

- [ ] WYSIWYG
- [ ] File
- [ ] Date

#### Other

- [ ] Pass currently authenticated user data
- [ ] Group resources in menu
- [ ] Observers