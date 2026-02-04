<?php

use Illuminate\Support\Arr;
use Illuminate\Support\Facades\File;
use Illuminate\Support\ProcessUtils;
use Illuminate\Support\Str;
use Laravel\Pail\Printers\CliPrinter;
use Laravel\Pail\ValueObjects\MessageLogged;
use Symfony\Component\Console\Output\BufferedOutput;
use Symfony\Component\Console\Output\OutputInterface;

use function Orchestra\Testbench\remote;

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
    ->beforeAll(fn () => $_ENV['PAIL_TESTS'] = true)
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

expect()->extend('toPail', function (string $expectedOutput, array $options = [], bool $verbose = false) {
    if ($GLOBALS['process'] === null) {
        $process = $GLOBALS['process'] = remote([
            'pail',
            collect($options)->map(fn ($value, $key) => "--{$key}=\"{$value}\"")->implode(' '),
            $verbose ? '-vvv' : '',
        ], env: [
            'APP_DEBUG' => '(true)',
            'PAIL_TESTS' => '(true)',
        ]);

        $GLOBALS['process'] = $process;

        $process->start();

        $process->waitUntil(function ($type, $output): bool {
            return str_contains($output, 'Tailing application logs.');
        });
    }

    collect(Arr::wrap($this->value))
        ->each(function (string $code) {
            remote(['eval', ProcessUtils::escapeArgument(base64_encode($code.';'))])->run();
        });

    do {
        $output = preg_replace('/\e\[[\d;]*m/', '', $GLOBALS['process']->getOutput());
        usleep(10);
    } while (! str_contains($output, 'artisan eval'));

    $output = Str::of($output)
        ->explode("\n")
        ->map(fn (string $line) => rtrim($line))
        ->implode("\n");

    expect($output)->toBe(<<<EOF

           INFO  Tailing application logs. Press Ctrl+C to exit
                         Use -v|-vv to show more details
        $expectedOutput
        EOF,
    );

    return $this;
});

function output(array $message, bool $verbose = false): string
{
    $output = new BufferedOutput;

    $output->setVerbosity($verbose ? OutputInterface::VERBOSITY_VERY_VERBOSE : OutputInterface::VERBOSITY_NORMAL);
    $printer = new CliPrinter($output, base_path());

    $printer->print(
        MessageLogged::fromJson((string) json_encode($message))
    );

    $output = $output->fetch();
    $output = preg_replace('/\e\[[\d;]*m/', '', $output);

    $output = Str::of($output)
        ->explode("\n")
        ->map(fn (string $line) => rtrim($line))
        ->implode("\n");

    return $output;
}
