<?php

use Illuminate\Database\Eloquent\Model;
use Illuminate\Foundation\Auth\User;
use Illuminate\Http\Request;
use Illuminate\Log\Events\MessageLogged;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\File as FileFacade;
use Illuminate\Support\Str;
use Laravel\Pail\File;
use Laravel\Pail\Files;
use Laravel\Pail\Handler;

afterEach(function () {
    Model::preventAccessingMissingAttributes(false);
    Auth::forgetUser();
});

test('it logs an API request authenticated by a model without an email attribute', function () {
    $path = storage_path('pail-tests/'.Str::uuid().'/handler.pail');
    $file = new File($path);
    $file->create();

    try {
        $request = Request::create('/api/orders', 'POST');
        $this->app->instance('request', $request);

        $user = new UserWithoutEmail;
        $user->setRawAttributes([
            'id' => 1,
            'contact_email' => 'client@example.com',
        ]);
        $user->exists = true;

        Model::preventAccessingMissingAttributes();
        Auth::setUser($user);

        $handler = new Handler($this->app, new Files(dirname($path)), false);
        $handler->log(new MessageLogged('info', 'Order submitted', []));

        $log = json_decode(
            file_get_contents($path),
            true,
            flags: JSON_THROW_ON_ERROR,
        );

        expect($log['context']['__pail']['origin'])->toMatchArray([
            'type' => 'http',
            'method' => 'POST',
            'path' => 'api/orders',
            'auth_id' => 1,
            'auth_email' => null,
        ]);
    } finally {
        FileFacade::deleteDirectory(dirname($path));
    }
});

test('it includes the authenticated user email in the HTTP origin', function () {
    $path = storage_path('pail-tests/'.Str::uuid().'/handler.pail');
    $file = new File($path);
    $file->create();

    try {
        $request = Request::create('/profile', 'GET');
        $this->app->instance('request', $request);

        $user = new User;
        $user->setRawAttributes([
            'id' => 2,
            'email' => 'user@example.com',
        ]);
        $user->exists = true;

        Model::preventAccessingMissingAttributes();
        Auth::setUser($user);

        $handler = new Handler($this->app, new Files(dirname($path)), false);
        $handler->log(new MessageLogged('info', 'Profile viewed', []));

        $log = json_decode(
            file_get_contents($path),
            true,
            flags: JSON_THROW_ON_ERROR,
        );

        expect($log['context']['__pail']['origin'])->toMatchArray([
            'auth_id' => 2,
            'auth_email' => 'user@example.com',
        ]);
    } finally {
        FileFacade::deleteDirectory(dirname($path));
    }
});

class UserWithoutEmail extends User
{
    protected $table = 'clients';
}
