<?php

namespace Laravel\Pail;

use Illuminate\Console\Events\CommandFinished;
use Illuminate\Console\Events\CommandStarting;
use Illuminate\Log\Events\MessageLogged;
use Illuminate\Support\Facades\Auth;

class Handler
{
    /**
     * The last lifecycle captured event.
     */
    protected CommandStarting|CommandFinished|null $lastLifecycleEvent = null;

    /**
     * Creates a new instance of the handler.
     */
    public function __construct(
        protected TailedFiles $tailedFiles,
        protected bool $runningInConsole,
    ) {
        //
    }

    /**
     * Reports the given message logged.
     */
    public function log(MessageLogged $messageLogged): void
    {
        $tailedFiles = $this->tailedFiles->all();

        if ($tailedFiles->isEmpty()) {
            return;
        }

        $context = $this->context($messageLogged);

        $tailedFiles->each(
            fn (TailedFile $tailedFile) => $tailedFile->log(
                $messageLogged->level,
                $messageLogged->message,
                $context,
            ),
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
    protected function context(MessageLogged $messageLogged): array
    {
        $lastLifecycleEventClass = $this->lastLifecycleEvent ? $this->lastLifecycleEvent::class : null;

        $context = ['__pail' => ['origin' => match (true) {
            $lastLifecycleEventClass === CommandStarting::class => [
                'type' => 'console',
                'command' => $this->lastLifecycleEvent->command, // @phpstan-ignore-line
            ],
            $this->runningInConsole => [
                'type' => 'console',
            ],
            default => [
                'type' => 'http',
                'method' => request()->method(), // @phpstan-ignore-line
                'path' => request()->path(), // @phpstan-ignore-line
                'auth_id' => Auth::id(),
            ],
        }]];

        $context['__pail']['origin']['trace'] = isset($messageLogged->context['exception'])
            ? $messageLogged->context['exception']->getTrace()
            : null;

        return collect($messageLogged->context)->merge($context)->toArray();
    }
}
