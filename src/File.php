<?php

namespace Laravel\Pail;

use Stringable;

class File implements Stringable
{
    /**
     * The time to live of the file.
     */
    protected const TTL = 3600;

    /**
     * Creates a new instance of the file.
     */
    public function __construct(
        protected string $file,
    ) {
        //
    }

    /**
     * Ensure the file exists.
     */
    public function create(): void
    {
        if (! $this->exists()) {
            $directory = dirname($this->file);

            if (! is_dir($directory)) {
                mkdir($directory, 0755, true);
            }

            touch($this->file);
        }
    }

    /**
     * Determines if the file exists.
     */
    public function exists(): bool
    {
        return file_exists($this->file);
    }

    /**
     * Deletes the file.
     */
    public function destroy(): void
    {
        if ($this->exists()) {
            unlink($this->file);
        }
    }

    /**
     * Log a log message to the file.
     *
     * @param  array<string, mixed>  $context
     */
    public function log(string $level, string $message, array $context = []): void
    {
        if ($this->isStale()) {
            $this->destroy();

            return;
        }

        $loggerFactory = new LoggerFactory($this);

        $logger = $loggerFactory->create();

        $logger->log($level, $message, $context);
    }

    /**
     * Returns the file as string.
     */
    public function __toString(): string
    {
        return $this->file;
    }

    /**
     * Determines if the file is staled.
     */
    protected function isStale(): bool
    {
        return time() - filemtime($this->file) > static::TTL;
    }
}
