<?php

use Illuminate\Support\Str;
use Laravel\Pail\Printers\CliPrinter;
use Laravel\Pail\TailOptions;
use Symfony\Component\Console\Output\BufferedOutput;

function output(array $message, ?TailOptions $options): string
{
    $options ??= new TailOptions(null, null);
    $output = new BufferedOutput();
    $printer = new CliPrinter($output, base_path());

    $printer->print($options, json_encode($message));

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
        └─────────────────────────────────────── inspire ┘

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
        └───────── inspire ┘

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
        └──────────────────── GET /logs | Auth ID: guest ┘

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
        └ GET /logs | Auth ID: guest ┘

        EOF,
    );
});

test('output with auth id options', function () {
    $message = [
        'message' => 'Hello World',
        'level_name' => 'info',
        'datetime' => '2021-01-01 00:00:00',
        'context' => [
            '__pail' => [
                'origin' => [
                    'type' => 'http',
                    'method' => 'GET',
                    'path' => 'logs',
                    'auth_id' => '123',
                ],
            ],
        ],
    ];

    $output = output($message, new TailOptions(null, null));
    expect($output)->toBe(<<<'EOF'
        ┌ 03:04:05 INFO ─────────────────────────────────┐
        │ Hello World                                    │
        └────────────────────── GET /logs | Auth ID: 123 ┘

        EOF,
    );

    $output = output($message, new TailOptions(null, '123'));
    expect($output)->toBe(<<<'EOF'
        ┌ 03:04:05 INFO ─────────────────────────────────┐
        │ Hello World                                    │
        └────────────────────── GET /logs | Auth ID: 123 ┘

        EOF,
    );

    $output = output($message, new TailOptions(null, '1234'));
    expect($output)->toBe('');
});
