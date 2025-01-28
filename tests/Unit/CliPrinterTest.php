<?php

beforeEach(function () {
    $_ENV['PAIL_TESTS'] = true;
});

afterEach(function () {
    unset($_ENV['PAIL_TESTS']);
});

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
    ]);

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
    ]);

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
                    'auth_email' => null,
                ],
            ],
        ],
    ]);

    expect($output)->toBe(<<<'EOF'
        ┌ 03:04:05 Exception ──────── app/MyClass.php:12 ┐
        │ Exception message                              │
        └─────────────────── GET: /logs • Auth ID: guest ┘

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
                    'auth_id' => 1,
                    'auth_email' => 'taylor@laravel.com',
                ],
            ],
        ],
    ]);

    expect($output)->toBe(<<<'EOF'
        ┌ 03:04:05 Exception  a… ┐
        │ Exception message tha… │
        └ GET: /logs • Auth ID: 1 (taylor@laravel.com) ┘

        EOF,
    );
});

test('escaping message', function () {
    $output = output([
        'message' => '<div class=3D"gmail-adL" style=3D"box-sizing:border-box">escaping message</div>',
        'level_name' => 'info',
        'datetime' => '2021-01-01 00:00:00',
        'context' => [
            '__pail' => [
                'origin' => [
                    'type' => 'http',
                    'method' => 'GET',
                    'path' => '/logs',
                    'auth_id' => null,
                    'auth_email' => null,
                ],
            ],
        ],
    ], true);

    expect($output)->toBe(<<<'EOF'
    ┌ 2024-01-01 03:04:05 INFO ───────────────────────
    │ <div class=3D"gmail-adL" style=3D"box-sizing:border-box">escaping message</div>
    │ 1. app/MyClass.php:12
    │ 2. app/MyClass.php:34
    └──────────────────── GET: /logs • Auth ID: guest

    EOF
    );
});

test('escaping html options', function () {
    $output = output([
        'message' => 'Context that contains html',
        'level_name' => 'info',
        'datetime' => '2021-01-01 00:00:00',
        'context' => [
            'html' => '<div class=3D"gmail-adL" style=3D"box-sizing:border-box">escaping html options</div>',
            '__pail' => [
                'origin' => [
                    'type' => 'http',
                    'method' => 'GET',
                    'path' => '/logs',
                    'auth_id' => null,
                    'auth_email' => null,
                ],
            ],
        ],
    ], true);

    expect($output)->toBe(<<<'EOF'
    ┌ 2024-01-01 03:04:05 INFO ───────────────────────
    │ Context that contains html
    │ 1. app/MyClass.php:12
    │ 2. app/MyClass.php:34
    └ GET: /logs • Auth ID: guest • html: <div class=3D"gmail-adL" style=3D"box-sizing:border-box">escaping html options</div>

    EOF
    );
});

test('escaping html arrayable options', function () {
    $output = output([
        'message' => 'Context that contains html',
        'level_name' => 'info',
        'datetime' => '2021-01-01 00:00:00',
        'context' => [
            'html' => [
                'first' => '<span class=3D"gmail-adL">first</span>',
                'second' => [
                    'a' => '<span class=3D"gmail-adL">a</span>',
                    'b' => '<span class=3D"gmail-adL">b</span>',
                ],
            ],
            '__pail' => [
                'origin' => [
                    'type' => 'http',
                    'method' => 'GET',
                    'path' => '/logs',
                    'auth_id' => null,
                    'auth_email' => null,
                ],
            ],
        ],
    ], true);

    expect($output)->toBe(<<<'EOF'
    ┌ 2024-01-01 03:04:05 INFO ───────────────────────
    │ Context that contains html
    │ 1. app/MyClass.php:12
    │ 2. app/MyClass.php:34
    └ GET: /logs • Auth ID: guest • html: array ( 'first' => '<span class=3D"gmail-adL">first</span>', 'second' => array ( 'a' => '<span class=3D"gmail-adL">a</span>', 'b' => '<span class=3D"gmail-adL">b</span>', ), )

    EOF
    );
});
