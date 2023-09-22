<?php

declare(strict_types=1);

namespace NunoMaduro\Pail;

use Illuminate\Console\Events\CommandFinished;
use Illuminate\Console\Events\CommandStarting;
use Illuminate\Contracts\Foundation\Application;
use Illuminate\Log\Events\MessageLogged;
use Illuminate\Support\ServiceProvider;
use NunoMaduro\Pail\Console\Commands\PailCommand;

/**
 * @internal
 */
final class PailServiceProvider extends ServiceProvider
{
    /**
     * Registers the application services.
     */
    public function register(): void
    {
        $this->app->singleton(
            TailedFiles::class,
            static fn (Application $app): TailedFiles => new TailedFiles($app->storagePath('pail'))
        );

        $this->app->singleton(Handler::class, fn (Application $app): Handler => new Handler(
            $app->make(TailedFiles::class), // @phpstan-ignore-line
            $app->runningInConsole(),
        ));
    }

    /**
     * Bootstraps the application services.
     */
    public function boot(): void
    {
        /** @var \Illuminate\Contracts\Events\Dispatcher $events */
        $events = $this->app->make('events');

        $events->listen(MessageLogged::class, function (MessageLogged $messageLogged): void {
            /** @var Handler $handler */
            $handler = $this->app->make(Handler::class);

            $handler->log($messageLogged);
        });

        $events->listen([
            CommandStarting::class,
            CommandFinished::class,
        ], function (CommandStarting|CommandFinished $lifecycleEvent): void {
            /** @var Handler $handler */
            $handler = $this->app->make(Handler::class);

            $handler->setLastLifecycleEvent($lifecycleEvent);
        });

        if ($this->app->runningInConsole()) {
            $this->commands([
                PailCommand::class,
            ]);
        }
    }
}
