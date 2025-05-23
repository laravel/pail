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
                'file' => '/var/app/routes/artisan.php:17',
            ],
            '__pail' => [
                'origin' => [
                    'type' => 'queue',
                    'queue' => 'emails',
                    'command' => 'queue:work',
                    'job' => 'App\Jobs\WelcomeMail',
                ],
            ],
        ],
    ]);

    expect($output)->toBe(<<<EOF
        ┌ 03:04:05 Exception ──────── app/MyClass.php:12 ┐
        │ my exception message                           │
        └ artisan queue:work • emails • App\Jobs\WelcomeMail ┘

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
                'file' => '/var/app/routes/artisan.php:17',
            ],
            '__pail' => [
                'origin' => [
                    'type' => 'queue',
                    'command' => null,
                    'queue' => 'emails',
                    'job' => 'App\Jobs\WelcomeMail',
                ],
            ],
        ],
    ], true);

    expect($output)->toBe(<<<EOF
        ┌ 2024-01-01 03:04:05 Exception  app/MyClass.php:12
        │ my exception message
        │ 1. app/MyClass.php:12
        │ 2. app/MyClass.php:34
        └────────────────── emails • App\Jobs\WelcomeMail

        EOF,
    );
});
