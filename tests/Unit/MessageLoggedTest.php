<?php

use Laravel\Pail\ValueObjects\MessageLogged;

test('tryFromJson returns null for malformed json', function () {
    expect(MessageLogged::tryFromJson('not valid json'))->toBeNull();
});

test('tryFromJson returns null for empty string', function () {
    expect(MessageLogged::tryFromJson(''))->toBeNull();
});

test('tryFromJson returns null for truncated json', function () {
    expect(MessageLogged::tryFromJson('{"message": "hello"'))->toBeNull();
});

test('tryFromJson returns null for json missing required fields', function () {
    expect(MessageLogged::tryFromJson('{"foo": "bar"}'))->toBeNull();
});

test('tryFromJson returns message for valid json', function () {
    $json = json_encode([
        'message' => 'Hello World',
        'level_name' => 'INFO',
        'datetime' => '2021-01-01T00:00:00.000000+00:00',
        'context' => [
            '__pail' => [
                'origin' => [
                    'type' => 'console',
                    'command' => 'inspire',
                ],
            ],
        ],
    ]);

    $result = MessageLogged::tryFromJson($json);

    expect($result)->toBeInstanceOf(MessageLogged::class)
        ->and($result->message())->toBe('Hello World')
        ->and($result->level())->toBe('INFO');
});
