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

Publish the package's assets, config and provider by running `panel:install` Artisan command:

```bash
php artisan panel:install
```

### Dashboard

A dashboard will be exposed at the `/panel` URI by default, but can be changed in the config.

### Configuration

The package config is located at `config/panel.php`.

## Documentation

- [Resources](./docs/resources.md)
- [Fields](./docs/fields.md)
- [Actions](./docs/actions.md)

## Roadmap

Since this is a project I plan to use quite often, there will be additional features I would like to add when needed.

### Fields to support

- WYSIWYG
- File

### Other features to support

- Group resources in menu
- Model observers
- Policies to manage authorization to Panel pages and resources.