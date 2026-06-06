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

test('filters deprecation warnings when deprecation channel is not configured', function () {
    Config::set('logging.deprecations.channel', null);

    $this->handler->log(new MessageLogged('warning', 'Function deprecated since v2.0', []));

    $contents = file_get_contents($this->pailDir.'/test.pail');

    expect($contents)->toBeEmpty();
});

test('shows deprecation warnings when deprecation channel is configured', function () {
    Config::set('logging.deprecations.channel', 'stack');

    $this->handler->log(new MessageLogged('warning', 'Function deprecated since v2.0', []));

    $contents = file_get_contents($this->pailDir.'/test.pail');

    expect($contents)->toContain('Function deprecated since v2.0');
});

test('does not filter non-deprecation warning messages', function () {
    Config::set('logging.deprecations.channel', null);

    $this->handler->log(new MessageLogged('warning', 'Disk space running low', []));

    $contents = file_get_contents($this->pailDir.'/test.pail');

    expect($contents)->toContain('Disk space running low');
});
