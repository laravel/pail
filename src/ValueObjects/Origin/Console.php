<?php

declare(strict_types=1);

namespace NunoMaduro\Pail\ValueObjects\Origin;

/**
 * @internal
 */
final readonly class Console
{
    /**
     * Creates a new instance of the console origin.
     */
    public function __construct(
        public string $command,
    ) {
        //
    }

    /**
     * Creates a new instance of the console origin from the given json string.
     *
     * @param  array{command: string}  $array
     */
    public static function fromArray(array $array): self
    {
        ['command' => $command] = $array;

        return new self($command);
    }
}
