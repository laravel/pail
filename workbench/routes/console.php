<?php

use Illuminate\Support\Facades\Artisan;

Artisan::command('eval {code}', function () {
    eval(base64_decode($this->argument('code')));
});
