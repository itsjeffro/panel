# Panel
Laravel package that provides a separate administration panel to manage model data.

### Installation
The package's main service provider will be automatically registered with Laravel's package auto-discovery.

```bash
composer require itsjeffro/panel
```

Publish config, assets and application service provider.
```bash
php artisan vendor:publish --tag=panel-config
php artisan vendor:publish --tag=panel-assets
php artisan vendor:publish --tag=panel-provider
```

The published application service provider is where the path to the resources is configured. Register the Panel service 
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

* ID
* Password
* Text
* Textarea

#### Field Visibility

* showOnIndex()
* showOnDisplay()
* showOnCreate()
* showOnUpdate()
* hideOnIndex()
* hideOnDisplay()
* hideOnCreate()
* hideOnUpdate()

### Relationships

* BelongsTo
