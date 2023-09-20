<?php

declare(strict_types=1);

namespace NunoMaduro\Pail;

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
            TailedFile::class,
            static fn (Application $app): TailedFile => new TailedFile($app->storagePath('logs/pail.log'))
        );

        $this->app->singleton(Handler::class, function (Application $app): Handler {
            /** @var LoggerFactory $loggerFactory */
            $loggerFactory = $app->make(LoggerFactory::class);

            $logger = $loggerFactory->create();

            /** @var TailedFile $tailedFile */
            $tailedFile = $app->make(TailedFile::class);

            return new Handler($logger, $tailedFile);
        });
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

        $this->commands([
            PailCommand::class,
        ]);
    }
}
