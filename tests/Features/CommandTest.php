<?php

declare(strict_types=1);

test('debug messages', function () {
    expect('app("log")->debug("my debug message")')->toPail(<<<'EOF'
        ┌ 03:04:05 DEBUG ────────────────────────────────┐
        │ my debug message                               │
        └─────────────────────────────────────── artisan ┘

        EOF,
    );
});

test('info messages', function () {
    expect('app("log")->info("my info message")')->toPail(<<<'EOF'
        ┌ 03:04:05 INFO ─────────────────────────────────┐
        │ my info message                                │
        └─────────────────────────────────────── artisan ┘

        EOF,
    );
});

test('notice messages', function () {
    expect('app("log")->notice("my notice message")')->toPail(<<<'EOF'
        ┌ 03:04:05 NOTICE ───────────────────────────────┐
        │ my notice message                              │
        └─────────────────────────────────────── artisan ┘

        EOF,
    );
});

test('warning messages', function () {
    expect('app("log")->warning("my warning message")')->toPail(<<<'EOF'
        ┌ 03:04:05 WARNING ──────────────────────────────┐
        │ my warning message                             │
        └─────────────────────────────────────── artisan ┘

        EOF,
    );
});

test('error messages', function () {
    expect('app("log")->error("my error message")')->toPail(<<<'EOF'
        ┌ 03:04:05 ERROR ────────────────────────────────┐
        │ my error message                               │
        └─────────────────────────────────────── artisan ┘

        EOF,
    );
});

test('critical messages', function () {
    expect('app("log")->critical("my critical message")')->toPail(<<<'EOF'
        ┌ 03:04:05 CRITICAL ─────────────────────────────┐
        │ my critical message                            │
        └─────────────────────────────────────── artisan ┘

        EOF,
    );
});

test('alert messages', function () {
    expect('app("log")->alert("my alert message")')->toPail(<<<'EOF'
        ┌ 03:04:05 ALERT ────────────────────────────────┐
        │ my alert message                               │
        └─────────────────────────────────────── artisan ┘

        EOF,
    );
});

test('emergency messages', function () {
    expect('app("log")->emergency("my emergency message")')->toPail(<<<'EOF'
        ┌ 03:04:05 EMERGENCY ────────────────────────────┐
        │ my emergency message                           │
        └─────────────────────────────────────── artisan ┘

        EOF,
    );
});

test('multiple messages', function () {
    expect([
        'app("log")->debug("my debug message")',
        'app("log")->info("my info message")',
        'app("log")->notice("my notice message")',
    ])->toPail(<<<'EOF'
        ┌ 03:04:05 DEBUG ────────────────────────────────┐
        │ my debug message                               │
        └─────────────────────────────────────── artisan ┘
        ┌ 03:04:05 INFO ─────────────────────────────────┐
        │ my info message                                │
        └─────────────────────────────────────── artisan ┘
        ┌ 03:04:05 NOTICE ───────────────────────────────┐
        │ my notice message                              │
        └─────────────────────────────────────── artisan ┘

        EOF,
    );
});

test('exceptions', function () {
    expect('throw new Exception("my exception message")')->toPail(<<<'EOF'
        ┌ 03:04:05 Exception ──────── app/MyClass.php:12 ┐
        │ my exception message                           │
        └─────────────────────────────────────── artisan ┘

        EOF,
    );
});

test('runtime exceptions', function () {
    expect('throw new RuntimeException("my runtime exception message")')->toPail(<<<'EOF'
        ┌ 03:04:05 RuntimeException ─ app/MyClass.php:12 ┐
        │ my runtime exception message                   │
        └─────────────────────────────────────── artisan ┘

        EOF,
    );
});

test('multiple exceptions and messages', function () {
    expect([
        'throw new RuntimeException("my runtime exception message")',
        'app("log")->critical("my critical message")',
        'throw new Exception("my exception message")',
    ])->toPail(<<<'EOF'
        ┌ 03:04:05 RuntimeException ─ app/MyClass.php:12 ┐
        │ my runtime exception message                   │
        └─────────────────────────────────────── artisan ┘
        ┌ 03:04:05 CRITICAL ─────────────────────────────┐
        │ my critical message                            │
        └─────────────────────────────────────── artisan ┘
        ┌ 03:04:05 Exception ──────── app/MyClass.php:12 ┐
        │ my exception message                           │
        └─────────────────────────────────────── artisan ┘

        EOF,
    );
});
