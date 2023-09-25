<?php

namespace Laravel\Pail\Contracts;

use Laravel\Pail\TailOptions;
use Laravel\Pail\ValueObjects\MessageLogged;

interface Printer
{
    /**
     * Prints the given message logged.
     */
    public function print(MessageLogged $messageLogged): void;
}
