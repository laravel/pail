<?php

declare(strict_types=1);

use Illuminate\Support\Arr;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Str;

/*
|--------------------------------------------------------------------------
| Test Case
|--------------------------------------------------------------------------
|
| The closure you provide to your test functions is always bound to a specific PHPUnit test
| case class. By default, that class is "PHPUnit\Framework\TestCase". Of course, you may
| need to change it using the "uses()" function to bind a different classes or traits.
|
*/

$GLOBALS['process'] = null;

uses(Tests\TestCase::class)
    ->beforeEach(function () {
        putenv('COLUMNS=50');
        $_ENV['COLUMNS'] = 50;
    })->afterEach(function () {
        if ($GLOBALS['process']) {
            (fn () => $this->process->stop())->call($GLOBALS['process']);

            $GLOBALS['process'] = null;

            File::deleteDirectory(storage_path('pail'));
        }
    })->in(__DIR__);

/*
|--------------------------------------------------------------------------
| Expectations
|--------------------------------------------------------------------------
|
| When you're writing tests, you often need to check that values meet certain conditions. The
| "expect()" function gives you access to a set of "expectations" methods that you can use
| to assert different things. Of course, you may extend the Expectation API at any time.
|
*/

expect()->extend('toPail', function (string $expectedOutput) {
    if ($GLOBALS['process'] === null) {
        $process = $GLOBALS['process'] = Process::path(__DIR__.'/Fixtures')
            ->start('php artisan pail');

        $GLOBALS['process'] = $process;

        while ($process->output() === '') {
            usleep(10);
        }
    }

    collect(Arr::wrap($this->value))
        ->each(fn (string $code) => Process::path(__DIR__.'/Fixtures')
            ->run(sprintf("php artisan eval '%s;'", $code))
        );

    $output = $GLOBALS['process']->output();
    $output = preg_replace('/\e\[[\d;]*m/', '', $output);

    $output = Str::of($output)
        ->explode("\n")
        ->map(fn (string $line) => rtrim($line))
        ->implode("\n");

    expect($output)->toBe(<<<'EOF'

           INFO  Tailing application logs.

          Press Ctrl+C to exit


        EOF.$expectedOutput
    );

    return $this;
});
