<?php
namespace Hdgarau\Runners;

use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\ServiceProvider;

class RunnerSeriviceProvider extends ServiceProvider
{
    public function boot(): void
    {
        if ($this->app->runningInConsole())
        {
            $this->publishes([
                __DIR__ . '/../config/' => config_path(),
            ], 'laravel-assets');
        }
        Artisan::call('config:cache');
        $modelHandler = config('runners.default');
        $class = config('runners.models.' . $modelHandler . '.class');
        $params = config('runners.models.' . $modelHandler . '.params') ?? [];

        RunnerHandler::setModel(new $class(...$params));
    }
    public function register()
    {
        $this->commands([
            \Hdgarau\Runners\Console\Commands\MakeRunnerCommand::class,
            \Hdgarau\Runners\Console\Commands\RunnerCommand::class,
            \Hdgarau\Runners\Console\Commands\RunnerClearCommand::class,
            \Hdgarau\Runners\Console\Commands\RunnerTablesCommand::class
        ]);
    }
}