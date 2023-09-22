<?php

declare(strict_types=1);

namespace NunoMaduro\Pail;

use Illuminate\Console\Events\CommandFinished;
use Illuminate\Console\Events\CommandStarting;
use Illuminate\Log\Events\MessageLogged;
use Illuminate\Support\Facades\Auth;

/**
 * @internal
 */
final class Handler
{
    /**
     * The last lifecycle captured event.
     */
    private CommandStarting|CommandFinished|null $lastLifecycleEvent = null;

    /**
     * Creates a new instance of the handler.
     */
    public function __construct(private readonly TailedFiles $tailedFiles)
    {
        //
    }

    /**
     * Reports the given message logged.
     */
    public function log(MessageLogged $messageLogged): void
    {
        $context = $this->context();

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

    /**
     * Sets the last application lifecycle event.
     */
    public function setLastLifecycleEvent(CommandStarting|CommandFinished $event): void
    {
        $this->lastLifecycleEvent = $event;
    }

    /**
     * Builds the context array.
     *
     * @return array<string, mixed>
     */
    private function context(): array
    {
        $lastLifecycleEventClass = $this->lastLifecycleEvent ? $this->lastLifecycleEvent::class : null;

        $context = ['__pail' => ['origin' => match ($lastLifecycleEventClass) {
            CommandStarting::class => [
                'type' => 'console',
                'command' => $this->lastLifecycleEvent->command, // @phpstan-ignore-line
            ],
            default => [
                'type' => 'http',
                'method' => request()->method(), // @phpstan-ignore-line
                'path' => request()->path(), // @phpstan-ignore-line
                'auth_id' => Auth::id(),
            ],
        }]];

        return collect($context)->filter()->toArray();
    }
}
