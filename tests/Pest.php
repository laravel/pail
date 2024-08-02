<?php

use Illuminate\Support\Arr;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Str;
use Laravel\Pail\Printers\CliPrinter;
use Laravel\Pail\ValueObjects\MessageLogged;
use Symfony\Component\Console\Output\BufferedOutput;
use Symfony\Component\Console\Output\OutputInterface;

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
        $process = $GLOBALS['process'] = Process::path(__DIR__.'/Fixtures')
            ->start(sprintf(
                'php artisan pail %s %s',
                collect($options)->map(fn ($value, $key) => "--{$key}=\"{$value}\"")->implode(' '),
                $verbose ? '-vvv' : '',
            ));

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
