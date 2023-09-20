<?php

declare(strict_types=1);

namespace NunoMaduro\Pail\Contracts;

use NunoMaduro\Pail\TailOptions;

/**
 * @internal
 */
interface Printer
{
    /**
     * Prints the given buffer.
     */
    public function print(TailOptions $options, string $logMessage): void;
}
