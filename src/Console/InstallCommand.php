<?php

namespace Itsjeffro\Panel\Console;

use Exception;
use Illuminate\Console\Command;
use Illuminate\Support\Str;

class InstallCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'panel:install';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Install package config, assets and provider';

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct()
    {
        parent::__construct();
    }

    /**
     * Execute the console command.
     *
     * @return int
     * @throws Exception
     */
    public function handle()
    {
        $this->comment('Publishing Service Provider...');
        $this->callSilent('vendor:publish', ['--tag' => 'panel-provider']);

        $this->comment('Publishing Assets...');
        $this->callSilent('vendor:publish', ['--tag' => 'panel-assets']);

        $this->comment('Publishing Configuration...');
        $this->callSilent('vendor:publish', ['--tag' => 'panel-config']);

        $this->registerServiceProvider();

        $this->info('Itsjeffro/panel installed successfully.');
    }

    /**
     * Registers the service provider in the app.php config file.
     *
     * @return void
     */
    protected function registerServiceProvider()
    {
        $namespace = Str::replaceLast('\\', '', $this->laravel->getNamespace());
        $appConfig = file_get_contents(config_path('app.php'));

        if (Str::contains($appConfig, $namespace.'\\Providers\\PanelServiceProvider::class')) {
            return;
        }

        // Add the service provider.
        file_put_contents(config_path('app.php'), str_replace(
            "{$namespace}\\Providers\EventServiceProvider::class,".PHP_EOL,
            "{$namespace}\\Providers\EventServiceProvider::class,".PHP_EOL."        {$namespace}\Providers\PanelServiceProvider::class,".PHP_EOL,
            $appConfig
        ));

        // Update the service provider's namespace to match with with project's namespacing.
        file_put_contents(app_path('Providers/PanelServiceProvider.php'), str_replace(
            "namespace App\Providers;",
            "namespace {$namespace}\Providers;",
            file_get_contents(app_path('Providers/PanelServiceProvider.php'))
        ));
    }
}
