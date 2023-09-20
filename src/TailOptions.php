<?php

declare(strict_types=1);

namespace NunoMaduro\Pail;

use Illuminate\Console\Command;

/**
 * @internal
 */
final readonly class TailOptions
{
    /**
     * Creates a new instance of the tail options.
     */
    private function __construct(
        public ?string $filter,
        public ?string $userId,
    ) {
        //
    }

    /**
     * Creates a new instance of the tail options from the given console command.
     */
    public static function fromCommand(Command $command): self
    {
        $filter = $command->option('filter');
        $userId = $command->option('user');

        assert(is_string($filter) || $filter === null);
        assert(is_string($userId) || $userId === null);

        return new self($filter, $userId);
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

        if (is_string($this->userId)) {
            $options .= ($options === '' ? '' : ', ')."user: {$this->userId}";
        }

        return $options;
    }
}
