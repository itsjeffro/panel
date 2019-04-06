# Panel
Laravel package that provides a separate administration panel to manage model data

### Installation
```bash
composer require itsjeffro/panel
```

Register service provider in config/app.php

```php
Itsjeffro\Panel\PanelServiceProvider::class,
```

Publish config and assets
```bash
php artisan vendor:publish --tag=panel-config
php artisan vendor:publish --tag=panel-assets
```

### Resources
Resources allow for mapping to your applications model.

You may generate a new resource by useing the panel:resource console command.

```bash
php artisan panel:resource User
```

### API
Available endpoints.

#### Get resources
```
GET /panel/api/resources
```

```json
[
  "App\\Panel\\User"
]
```

#### Get resource
```
GET /panel/api/resources/{resourceSlug}
```

```json
{
  "name": "User",
  "resource": {
    "model": "App\\User",
    "title": "id"
  },
  "data": {
    "current_page": 1,
    "data": [
      {
        "id": 1,
      }
    }
  }
}
```
      