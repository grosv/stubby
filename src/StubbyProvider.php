<?php


namespace Grosv\Stubby;


use Illuminate\Support\ServiceProvider;

class StubbyProvider extends ServiceProvider
{
    public function boot(): void
    {
        $this->commands([
            StubbyCommand::class,
        ]);
    }

    public function register(): void
    {
        $this->mergeConfigFrom(__DIR__.'/../config/config.php', 'stubby');
    }
}