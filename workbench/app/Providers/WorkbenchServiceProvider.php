<?php

namespace Workbench\App\Providers;

use Illuminate\Support\ServiceProvider;

class WorkbenchServiceProvider extends ServiceProvider
{
    /**
     * Register services.
     */
    public function register(): void
    {
        $_ENV['PAIL_TESTS'] = true;
    }

    /**
     * Bootstrap services.
     */
    public function boot(): void
    {
        //
    }
}
