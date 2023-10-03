<?php

namespace Laravel\Pail\ValueObjects\Origin;

class Queue
{
    /**
     * Creates a new instance of the console origin.
     */
    public function __construct(
        public string $queue,
        public string $job,
    ) {
        //
    }

    /**
     * Creates a new instance of the queue origin from the given json string.
     *
     * @param  array{queue: string, job: string}  $array
     */
    public static function fromArray(array $array): static
    {
        [
            'queue' => $queue,
            'job' => $job,
        ] = $array;

        return new static($queue, $job);
    }
}
