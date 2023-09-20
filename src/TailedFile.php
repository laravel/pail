<?php

declare(strict_types=1);

namespace NunoMaduro\Pail;

/**
 * @internal
 */
final readonly class TailedFile implements \Stringable
{
    /**
     * Creates a new instance of the tailed file.
     */
    public function __construct(
        private string $file,
    ) {
        //
    }

    /**
     * Ensure the tailed file exists.
     */
    public function create(): void
    {
        if (! $this->exists()) {
            touch($this->file);
        }
    }

    /**
     * Determines if the tailed file exists.
     */
    public function exists(): bool
    {
        return file_exists($this->file);
    }

    /**
     * Deletes the tailed file.
     */
    public function destroy(): void
    {
        if ($this->exists()) {
            unlink($this->file);
        }
    }

    /**
     * Returns the tailed file as string.
     */
    public function __toString(): string
    {
        return $this->file;
    }
}
