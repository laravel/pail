<?php

use Illuminate\Support\Facades\Artisan;

Artisan::command('eval {code}', fn ($code) => eval($code));
