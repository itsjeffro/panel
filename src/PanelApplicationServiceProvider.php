<?php

namespace Itsjeffro\Panel;

use Illuminate\Support\ServiceProvider;

class PanelApplicationServiceProvider extends ServiceProvider
{
    /**
     * Bootstrap any application services.
     *
     * @return void
     */
    public function boot()
    {
        $this->resources();
    }
    
    /**
     * Register any application services.
     *
     * @return void
     */
    public function register()
    {
        //
    }
    
    /**
     * Register any resources.
     *
     * @return void
     */
    protected function resources(): void
    {
        Panel::resourcesIn(app_path('Panel'));
    }
}
