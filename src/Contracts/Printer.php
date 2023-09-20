<?php

declare(strict_types=1);

namespace NunoMaduro\Pail\Contracts;

/**
 * @internal
 */
interface Printer
{
    /**
     * Prints the given buffer.
     */
    public function print(string $logMessage): void;
}
