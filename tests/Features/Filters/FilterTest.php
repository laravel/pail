<?php

test('by message', function () {
    expect([
        'throw new RuntimeException("my re exception")',
        'app("log")->critical("my cr message")',
        'throw new Exception("my e message")',
    ])->toPail(<<<'EOF'
        ┌ 03:04:05 CRITICAL ─────────────────────────────┐
        │ my cr message                                  │
        └─────────────────────────────────────── artisan ┘

        EOF,
        [
            'filter' => 'my cr message',
        ]);
});

test('by level', function () {
    expect([
        'throw new RuntimeException("my re exception")',
        'app("log")->critical("my cr message")',
        'throw new Exception("my e message")',
    ])->toPail(<<<'EOF'
        ┌ 03:04:05 CRITICAL ─────────────────────────────┐
        │ my cr message                                  │
        └─────────────────────────────────────── artisan ┘

        EOF,
        [
            'filter' => 'critical',
        ]);
});
