<?php

declare(strict_types=1);

namespace NunoMaduro\Pail;

use Illuminate\Log\Events\MessageLogged;
use Illuminate\Support\Facades\Auth;
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
     * Reports the given message logged.
     */
    public function log(MessageLogged $messageLogged): void
    {
        if ($this->tailedFile->exists()) {
            $context = ['__pail' => [
                'user_id' => null,
            ]];

            if ($userId = Auth::user()?->id ?? null) {
                $context['__pail']['user_id'] = (string) $userId;
            }

            $this->logger->log($messageLogged->level, $messageLogged->message, array_merge(
                $messageLogged->context,
                $context
            ));
        }
    }
}
