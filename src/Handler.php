<?php

declare(strict_types=1);

namespace NunoMaduro\Pail;

use Illuminate\Log\Events\MessageLogged;
use Illuminate\Support\Facades\Auth;

/**
 * @internal
 */
final readonly class Handler
{
    /**
     * Creates a new instance of the handler.
     */
    public function __construct(private TailedFiles $tailedFiles)
    {
        //
    }

    /**
     * Reports the given message logged.
     */
    public function log(MessageLogged $messageLogged): void
    {
        $context = ['__pail' => [
            'user_id' => null,
        ]];

        if ($userId = Auth::user()?->id ?? null) {
            $context['__pail']['user_id'] = (string) $userId;
        }

        $this->tailedFiles->each(
            static function (TailedFile $tailedFile) use ($messageLogged, $context): void {
                $tailedFile->log(
                    $messageLogged->level,
                    $messageLogged->message, array_merge(
                        $messageLogged->context,
                        $context
                    ),
                );
            }
        );
    }
}
