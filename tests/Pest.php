<?php

use Illuminate\Support\Arr;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Str;
use Laravel\Pail\Printers\CliPrinter;
use Laravel\Pail\ValueObjects\MessageLogged;
use Symfony\Component\Console\Output\BufferedOutput;
use Symfony\Component\Console\Output\OutputInterface;

use function Orchestra\Testbench\package_path;

putenv('COLUMNS=50');
$_ENV['COLUMNS'] = 50;

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

uses(Tests\TestCase::class)
    ->afterEach(function () {
        File::deleteDirectory(storage_path('pail'));
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
    $formattedOutput = function ($process) {
        $output = $process->output();
        $output = preg_replace('/\e\[[\d;]*m/', '', $output);

        return Str::of($output)
            ->explode("\n")
            ->map(fn (string $line) => rtrim($line))
            ->implode("\n");
    };

    $process = Process::path(base_path())
        ->env(['TESTBENCH_WORKING_PATH' => package_path()])
        ->timeout(20)
        ->start(sprintf(
            'php artisan pail %s %s',
            collect($options)->map(fn ($value, $key) => "--{$key}=\"{$value}\"")->implode(' '),
            $verbose ? '-vvv' : '',
        ));

    do {
        usleep(10);
    } while (! str_contains($formattedOutput($process), 'Tailing application logs. Press Ctrl+C to exit'));

    collect(Arr::wrap($this->value))
        ->each(fn (string $code) => Process::path(base_path())
            ->env(['TESTBENCH_WORKING_PATH' => package_path()])
            ->timeout(20)
            ->start(sprintf("php artisan eval '%s;'", $code))
        );
    // collect(Arr::wrap($this->value))
    //     ->each(function (string $code) {
    //         test()->post('eval', ['code' => $code]);
    //     });

    do {
        usleep(10);
    } while (! str_contains($formattedOutput($process), $expectedOutput));

    expect($formattedOutput($process))->toContain($expectedOutput);

    $process->stop();

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
