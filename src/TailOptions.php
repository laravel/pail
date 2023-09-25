<?php

namespace Laravel\Pail;

use Illuminate\Console\Command;
use Stringable;

class TailOptions implements Stringable
{
    /**
     * Creates a new instance of the tail options.
     */
    public function __construct(
        public ?string $filter,
        public ?string $authId,
    ) {
        //
    }

    /**
     * Creates a new instance of the tail options from the given console command.
     */
    public static function fromCommand(Command $command): self
    {
        $filter = $command->option('filter');
        $authId = $command->option('auth') ?? $command->option('user');

        assert(is_string($filter) || $filter === null);
        assert(is_string($authId) || $authId === null);

        return new self($filter, $authId);
    }

    /**
     * Returns the string representation of the tail options.
     */
    public function __toString(): string
    {
        $options = '';

        if (is_string($this->filter)) {
            $options .= "filter: {$this->filter}";
        }

        if (is_string($this->authId)) {
            $options .= ($options === '' ? '' : ', ')."user: {$this->authId}";
        }

        return $options;
    }
}
