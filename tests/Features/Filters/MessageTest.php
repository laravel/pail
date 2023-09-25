<?php

test('accepts message', function () {
    expect([
        'throw new RuntimeException("my re exception")',
        'app("log")->critical("my cr message")',
        'throw new Exception("my e message")',
    ])->toPail(<<<'EOF'
        ┌ 03:04:05 RuntimeException ─ app/MyClass.php:12 ┐
        │ my re exception                                │
        └─────────────────────────────────────── artisan ┘

        EOF,
        [
            'message' => 'my re exception',
        ]);
});

test('is case insensitive', function () {
    expect([
        'throw new RuntimeException("my re MESSAGE")',
        'app("log")->critical("my cr MESSAGE")',
        'throw new Exception("my e MESSAGE")',
    ])->toPail(<<<'EOF'
        ┌ 03:04:05 RuntimeException ─ app/MyClass.php:12 ┐
        │ my re MESSAGE                                  │
        └─────────────────────────────────────── artisan ┘
        ┌ 03:04:05 Exception ──────── app/MyClass.php:12 ┐
        │ my e MESSAGE                                   │
        └─────────────────────────────────────── artisan ┘

        EOF,
        [
            'message' => 'E message',
        ]);
});
