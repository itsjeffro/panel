<?php

namespace Itsjeffro\Panel\Tests;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Itsjeffro\Panel\Panel;
use Itsjeffro\Panel\PanelServiceProvider;
use Itsjeffro\Panel\Tests\Resources\User;

class TestCase extends \Orchestra\Testbench\TestCase
{
    use RefreshDatabase;

    public function setUp(): void
    {
        parent::setUp();

        $this->loadLaravelMigrations(['--database' => 'testing']);

        $this->artisan('migrate', ['--database' => 'testing']);

        Panel::resources([
            User::class,
        ]);
    }

    protected function getPackageProviders($app): array
    {
        return [
            PanelServiceProvider::class,
        ];
    }

    protected function getEnvironmentSetUp($app)
    {
        config(['panel.middleware' => 'bindings']);

        config(['database.default' => 'testing']);

        config([
            'database.connections.testing' => [
                'driver' => 'sqlite',
                'username' => env('DB_USERNAME', 'root'),
                'host' => env('DB_HOST', '127.0.0.1'),
                'port' => env('DB_PORT', '3306'),
                'password' => env('DB_PASSWORD'),
                'database' => 'panel_test',
            ]
        ]);
    }
}