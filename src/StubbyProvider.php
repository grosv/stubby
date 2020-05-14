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

        $this->publishes([
            __DIR__.'/../stubs' => base_path('stubs'),
        ], 'stubs');
    }

    public function register(): void
    {
        $this->mergeConfigFrom(__DIR__.'/../config/config.php', 'stubby');
    }
}