<?php

beforeEach(function () {
    $_ENV['PAIL_TESTS'] = true;
});

afterEach(function () {
    unset($_ENV['PAIL_TESTS']);
});

test('non verbose', function () {
    $output = output([
        'message' => 'my exception message',
        'level_name' => 'error',
        'datetime' => '2021-01-01 00:00:00',
        'context' => [
            'exception' => [
                'class' => 'Exception',
                'message' => 'my exception message',
                'code' => 0,
                'file' => '/var/app/routes/web.php:17',
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
        │ my exception message                           │
        └─────────────────── GET: /logs • Auth ID: guest ┘

        EOF,
    );
});

test('verbose', function () {
    $output = output([
        'message' => 'my exception message',
        'level_name' => 'error',
        'datetime' => '2021-01-01 00:00:00',
        'context' => [
            'exception' => [
                'class' => 'Exception',
                'message' => 'my exception message',
                'code' => 0,
                'file' => '/var/app/routes/web.php:17',
            ],
            '__pail' => [
                'origin' => [
                    'type' => 'http',
                    'method' => 'POST',
                    'path' => '/users/1',
                    'auth_id' => 1,
                    'auth_email' => 'taylor@gmail.com',
                ],
            ],
        ],
    ], true);

    expect($output)->toBe(<<<'EOF'
        ┌ 2024-01-01 03:04:05 Exception  app/MyClass.php:12
        │ my exception message
        │ 1. app/MyClass.php:12
        │ 2. app/MyClass.php:34
        └─ POST: /users/1 • Auth ID: 1 (taylor@gmail.com)

        EOF,
    );
});
