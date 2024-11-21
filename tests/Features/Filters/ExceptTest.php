<?php

test('accepts except', function () {
    expect([
        'throw new RuntimeException("my excepted exception")',
        'app("log")->critical("my excepted message")',
        'throw new Exception("my excepted message")',
    ])->toPail(<<<'EOF'

        EOF,
        [
            'except' => 'excepted',
        ]);
});

test('accepts multiple excepts', function () {
    expect([
        'throw new RuntimeException("my excepted exception")',
        'app("log")->critical("my excepted message")',
        'throw new Exception("my excepted message")',
        'throw new RuntimeException("my other-exception")',
        'app("log")->critical("my other-exception")',
        'throw new Exception("my other-exception")',
    ])->toPail(<<<'EOF'

        EOF,
        [
            'except' => 'excepted,other-exception',
        ]);
});

test('accepts multiple excepts and ignores spaces after commas', function () {
    expect([
        'throw new RuntimeException("my excepted exception")',
        'app("log")->critical("my excepted message")',
        'throw new Exception("my excepted message")',
        'throw new RuntimeException("my other-exception")',
        'app("log")->critical("my other-exception")',
        'throw new Exception("my other-exception")',
        'throw new RuntimeException("my third-exception")',
        'app("log")->critical("my third-message")',
        'throw new Exception("my third-exception")',
    ])->toPail(<<<'EOF'

        EOF,
        [
            'except' => 'excepted, other-exception, third',
        ]);
});

test('accepts excepts case-insensitive', function () {
    expect([
        'throw new RuntimeException("my excepted exception")',
        'app("log")->critical("my excepted message")',
        'throw new Exception("my excepted message")',
        'throw new RuntimeException("my other-exception")',
        'app("log")->critical("my other-exception")',
        'throw new Exception("my other-exception")',
    ])->toPail(<<<'EOF'

        EOF,
        [
            'except' => 'EXCEPTED,OTHER-exc',
        ]);
});

test('logs other than excepted', function () {
    expect([
        'throw new RuntimeException("my re exception")',
        'app("log")->critical("my cr message")',
        'throw new Exception("my e message")',
    ])->toPail(<<<'EOF'
        ┌ 03:04:05 RuntimeException ─ app/MyClass.php:12 ┐
        │ my re exception                                │
        └────────────────────────────────── artisan eval ┘
        ┌ 03:04:05 CRITICAL ─────────────────────────────┐
        │ my cr message                                  │
        └────────────────────────────────── artisan eval ┘
        ┌ 03:04:05 Exception ──────── app/MyClass.php:12 ┐
        │ my e message                                   │
        └────────────────────────────────── artisan eval ┘

        EOF,
        [
            'except' => 'excepted, other-exception',
        ]);
});
