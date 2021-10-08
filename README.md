# Panel

<p align="center">
    <a href="https://github.com/itsjeffro/panel/actions"><img src="https://github.com/itsjeffro/panel/workflows/tests/badge.svg" alt="Build Status"></a>
    <a href="https://packagist.org/packages/itsjeffro/panel"><img src="https://poser.pugx.org/itsjeffro/panel/d/total.svg" alt="Total Downloads"></a>
    <a href="https://packagist.org/packages/itsjeffro/panel"><img src="https://poser.pugx.org/itsjeffro/panel/v/stable.svg" alt="Latest Stable Version"></a>
    <a href="https://packagist.org/packages/itsjeffro/panel"><img src="https://poser.pugx.org/itsjeffro/panel/license.svg"></a>
</p>

## Introduction

Inspired by Laravel Nova. This package provides a separate administration panel to manage model data.

This is by no means a replacement or a competitor. I mainly created this package to see if I could create 
something similar to a learning experience.

<p align="center">
    <img src="https://res.cloudinary.com/dz4tjswiv/image/upload/v1633656076/panel.png" />
</p>

## Requirements

* Laravel 7

## Installation
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

## Documentation

- [Resources](./docs/resources.md)
- [Fields](./docs/fields.md)
- [Actions](./docs/actions.md)

## Roadmap

Since this is a project I plan to use quite often, there will be additional features I would like to add when needed.

#### Fields to support

- WYSIWYG
- File

#### Other features to support

- Group resources in menu
- Observers