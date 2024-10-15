<?php

test('does not show trace by default', function () {
    expect('throw new Exception("my exception message")')->toPail(<<<'EOF'
        ┌ 03:04:05 Exception ──────── app/MyClass.php:12 ┐
        │ my exception message                           │
        └────────────────────────────────── artisan eval ┘

        EOF,
    );
});

test('does show trace when verbose', function () {
    expect('throw new Exception("my exception message")')->toPail(<<<'EOF'
        ┌ 2024-01-01 03:04:05 Exception  app/MyClass.php:12
        │ my exception message
        │ 1. app/MyClass.php:12
        │ 2. app/MyClass.php:34
        └─────────────────────────────────── artisan eval

        EOF, verbose: true);
});
