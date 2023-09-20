<?php

declare(strict_types=1);

namespace NunoMaduro\Pail;

use Illuminate\Log\Events\MessageLogged;
use Psr\Log\LoggerInterface;

/**
 * @internal
 */
final readonly class Handler
{
    /**
     * Creates a new instance of the handler.
     */
    public function __construct(private LoggerInterface $logger, private TailedFile $tailedFile)
    {
        //
    }

    /**
     * Reports the given exception.
     */
    public function log(MessageLogged $messageLogged): void
    {
        if ($this->tailedFile->exists()) {
            $this->logger->log($messageLogged->level, $messageLogged->message, $messageLogged->context);
        }
    }
}
