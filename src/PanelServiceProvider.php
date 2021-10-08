<?php

namespace Itsjeffro\Panel;

use Illuminate\Support\Facades\Route;
use Illuminate\Support\ServiceProvider;
use Itsjeffro\Panel\Console\ActionCommand;
use Itsjeffro\Panel\Console\InstallCommand;
use Itsjeffro\Panel\Console\ResourceCommand;

class PanelServiceProvider extends ServiceProvider
{
    /**
     * Bootstrap any package services.
     *
     * @return void
     */
    public function boot(): void
    {
        $this->registerRoutes();
        $this->registerPublishing();

        $this->loadViewsFrom(
            __DIR__ . '/../resources/views', 'panel'
        );
    }

    /**
     * Register any package services.
     *
     * @return void
     */
    public function register(): void
    {
        $this->registerCommands();
    }
    
    /**
     * Register routes.
     * 
     * @return void
     */
    private function registerRoutes(): void
    {
        Route::group($this->routeConfiguration(), function () {
            $this->loadRoutesFrom(__DIR__ . '/Http/routes.php');
        });
    }
    
    /**
     * Route configuration.
     * 
     * @return array
     */
    private function routeConfiguration(): array
    {
        return [
            'namespace' => 'Itsjeffro\Panel\Http\Controllers',
            'prefix' => config('panel.prefix'),
            'middleware' => config('panel.middleware_group'),
        ];
    }
    
    
    /**
     * Register publishing.
     *
     * @return void
     */
    private function registerPublishing(): void
    {
        if ($this->app->runningInConsole()) {
            // Assets
            $this->publishes([
                __DIR__ . '/../public' => public_path('vendor/panel'),
            ], 'panel-assets');
            
            // Config
            $this->publishes([
                __DIR__ . '/../config/panel.php' => config_path('panel.php'),
            ], 'panel-config');
            
            // The application service provider
            $this->publishes([
                __DIR__ . '/../stubs/PanelServiceProvider.stub' => app_path('Providers/PanelServiceProvider.php'),
            ], 'panel-provider');
        }
    }

    /**
     * Register console commands.
     *
     * @return void
     */
    private function registerCommands(): void
    {
        if ($this->app->runningInConsole()) {
            $this->commands([
                ActionCommand::class,
                InstallCommand::class,
                ResourceCommand::class,
            ]);
        }
    }
}