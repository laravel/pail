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
            $directory = dirname($this->file);

            if (! is_dir($directory)) {
                mkdir($directory, 0755, true);
            }

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
     * Log a log message to the file.
     *
     * @param  array<string, mixed>  $context
     */
    public function log(string $level, string $message, array $context = []): void
    {
        $pid = (int) explode('.', basename($this->file))[0];

        // TODO: Use lotery to determine if the file is still alive.
        if (! posix_kill($pid, 0)) {
            $this->destroy();

            return;
        }

        $loggerFactory = new LoggerFactory($this);

        $logger = $loggerFactory->create();

        $logger->log($level, $message, $context);
    }

    /**
     * Returns the tailed file as string.
     */
    public function __toString(): string
    {
        return $this->file;
    }
}
