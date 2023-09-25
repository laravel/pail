<?php

namespace Laravel\Pail\Contracts;

use Laravel\Pail\TailOptions;

interface Printer
{
    /**
     * Prints the given buffer.
     */
    public function print(TailOptions $options, string $messageLogged): void;
}
