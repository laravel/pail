<?php

use Illuminate\Log\Events\MessageLogged;
use Illuminate\Support\Facades\Config;
use Laravel\Pail\Files;
use Laravel\Pail\Handler;

beforeEach(function () {
    $this->pailDir = sys_get_temp_dir().'/pail-test-'.uniqid();
    mkdir($this->pailDir, 0755, true);
    touch($this->pailDir.'/test.pail');

    $this->files = new Files($this->pailDir);

    $this->handler = new Handler(
        $this->app,
        $this->files,
        true,
    );
});

afterEach(function () {
    @unlink($this->pailDir.'/test.pail');
    @rmdir($this->pailDir);
});

test('filters deprecation warnings when no deprecations channel is configured', function () {
    Config::set('logging.deprecations.channel', null);

    $this->handler->log(new MessageLogged('warning', 'Function foo() is deprecated', []));

    expect(file_get_contents($this->pailDir.'/test.pail'))->toBeEmpty();
});

test('filters deprecation warnings when the deprecations channel is the framework default of "null"', function () {
    Config::set('logging.deprecations.channel', 'null');

    $this->handler->log(new MessageLogged('warning', 'Function foo() is deprecated', []));

    expect(file_get_contents($this->pailDir.'/test.pail'))->toBeEmpty();
});

test('shows deprecation warnings once a deprecations channel is configured', function () {
    Config::set('logging.deprecations.channel', 'stack');

    $this->handler->log(new MessageLogged('warning', 'Function foo() is deprecated', []));

    expect(file_get_contents($this->pailDir.'/test.pail'))->toContain('Function foo() is deprecated');
});

test('does not touch other warning messages', function () {
    Config::set('logging.deprecations.channel', null);

    $this->handler->log(new MessageLogged('warning', 'Disk usage is high', []));

    expect(file_get_contents($this->pailDir.'/test.pail'))->toContain('Disk usage is high');
});
