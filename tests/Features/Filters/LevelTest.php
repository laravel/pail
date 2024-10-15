<?php

test('accepts level', function () {
    expect([
        'throw new RuntimeException("my re exception")',
        'app("log")->critical("my cr message")',
        'throw new Exception("my e message")',
    ])->toPail(<<<'EOF'
        ┌ 03:04:05 CRITICAL ─────────────────────────────┐
        │ my cr message                                  │
        └────────────────────────────────── artisan eval ┘

        EOF,
        [
            'level' => 'critical',
        ]);
});

test('is case insensitive', function () {
    expect([
        'app("log")->critical("my cr message")',
    ])->toPail(<<<'EOF'
        ┌ 03:04:05 CRITICAL ─────────────────────────────┐
        │ my cr message                                  │
        └────────────────────────────────── artisan eval ┘

        EOF,
        [
            'level' => 'CRitiCAL',
        ]);
});
