<?php

use Illuminate\Support\Facades\Artisan;

Artisan::command('eval {code}', fn () => eval($this->argument('code')));
