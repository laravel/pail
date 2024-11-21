<?php

test('accepts exclude', function () {
    expect([
        'throw new RuntimeException("my excluded exception")',
        'app("log")->critical("my excluded message")',
        'throw new Exception("my excluded message")',
    ])->toPail(<<<'EOF'

        EOF,
        [
            'exclude' => 'excluded',
        ]);
});

test('accepts multiple excludes', function () {
    expect([
        'throw new RuntimeException("my excluded exception")',
        'app("log")->critical("my excluded message")',
        'throw new Exception("my excluded message")',
        'throw new RuntimeException("my other-excl exception")',
        'app("log")->critical("my other-excl message")',
        'throw new Exception("my other-excl message")',
    ])->toPail(<<<'EOF'

        EOF,
        [
            'exclude' => 'excluded,other-excl',
        ]);
});

test('accepts multiple excludes and ignores spaces after commas', function () {
    expect([
        'throw new RuntimeException("my excluded exception")',
        'app("log")->critical("my excluded message")',
        'throw new Exception("my excluded message")',
        'throw new RuntimeException("my other-excl exception")',
        'app("log")->critical("my other-excl message")',
        'throw new Exception("my other-excl message")',
        'throw new RuntimeException("my third-excl exception")',
        'app("log")->critical("my third-excl message")',
        'throw new Exception("my third-excl message")',
    ])->toPail(<<<'EOF'

        EOF,
        [
            'exclude' => 'excluded, other-excl, third',
        ]);
});

test('accepts excludes case-insensitive', function () {
    expect([
        'throw new RuntimeException("my excluded exception")',
        'app("log")->critical("my excluded message")',
        'throw new Exception("my excluded message")',
        'throw new RuntimeException("my other-excl exception")',
        'app("log")->critical("my other-excl message")',
        'throw new Exception("my other-excl message")',
    ])->toPail(<<<'EOF'

        EOF,
        [
            'exclude' => 'EXCLUDED,OTHER-excl',
        ]);
});

test('logs other than excluded', function () {
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
            'exclude' => 'excluded, other-excl',
        ]);
});
