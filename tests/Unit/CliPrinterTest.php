<?php

use Illuminate\Support\Str;
use Laravel\Pail\Printers\CliPrinter;
use Laravel\Pail\ValueObjects\MessageLogged;
use Symfony\Component\Console\Output\BufferedOutput;

function output(array $message): string
{
    $output = new BufferedOutput();
    $printer = new CliPrinter($output, base_path());

    $printer->print(
        MessageLogged::fromJson(json_encode($message))
    );

    $output = $output->fetch();
    $output = preg_replace('/\e\[[\d;]*m/', '', $output);

    $output = Str::of($output)
        ->explode("\n")
        ->map(fn (string $line) => rtrim($line))
        ->implode("\n");

    return $output;
}

test('output', function () {
    $output = output([
        'message' => 'Hello World',
        'level_name' => 'info',
        'datetime' => '2021-01-01 00:00:00',
        'context' => [
            '__pail' => [
                'origin' => [
                    'type' => 'console',
                    'command' => 'inspire',
                ],
            ],
        ],
    ], null);

    expect($output)->toBe(<<<'EOF'
        ┌ 03:04:05 INFO ─────────────────────────────────┐
        │ Hello World                                    │
        └─────────────────────────────── artisan inspire ┘

        EOF,
    );
});

test('responsive output', function () {
    putenv('COLUMNS=20');

    $output = output([
        'message' => 'My info message that does this and that',
        'level_name' => 'info',
        'datetime' => '2021-01-01 00:00:00',
        'context' => [
            '__pail' => [
                'origin' => [
                    'type' => 'console',
                    'command' => 'inspire',
                ],
            ],
        ],
    ], null);

    expect($output)->toBe(<<<'EOF'
        ┌ 03:04:05 INFO ───┐
        │ My info message… │
        └─ artisan inspire ┘

        EOF,
    );
});

test('output exceptions', function () {
    $output = output([
        'message' => 'Exception message',
        'level_name' => 'error',
        'datetime' => '2021-01-01 00:00:00',
        'context' => [
            'exception' => [
                'class' => 'Exception',
                'file' => base_path().'/app/Exceptions/Handler.php',
            ],
            '__pail' => [
                'origin' => [
                    'type' => 'http',
                    'method' => 'GET',
                    'path' => '/logs',
                    'auth_id' => null,
                ],
            ],
        ],
    ], null);

    expect($output)->toBe(<<<'EOF'
        ┌ 03:04:05 Exception ──────── app/MyClass.php:12 ┐
        │ Exception message                              │
        └──────────────────── GET /logs • Auth ID: guest ┘

        EOF,
    );
});

test('responsive output exceptions', function () {
    putenv('COLUMNS=26');

    $output = output([
        'message' => 'Exception message that does this and that',
        'level_name' => 'error',
        'datetime' => '2021-01-01 00:00:00',
        'context' => [
            'exception' => [
                'class' => 'Exception',
                'file' => base_path().'/app/Exceptions/Handler.php',
            ],
            '__pail' => [
                'origin' => [
                    'type' => 'http',
                    'method' => 'GET',
                    'path' => '/logs',
                    'auth_id' => null,
                ],
            ],
        ],
    ], null);

    expect($output)->toBe(<<<'EOF'
        ┌ 03:04:05 Exception  a… ┐
        │ Exception message tha… │
        └ GET /logs • Auth ID: guest ┘

        EOF,
    );
});
