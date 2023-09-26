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
        protected Files $files,
        protected bool $runningInConsole,
    ) {
        //
    }

    /**
     * Reports the given message logged.
     */
    public function log(MessageLogged $messageLogged): void
    {
        $files = $this->files->all();

        if ($files->isEmpty()) {
            return;
        }

        $context = $this->context($messageLogged);

        $files->each(
            fn (File $file) => $file->log(
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
            in_array($lastLifecycleEventClass, [CommandStarting::class, CommandFinished::class], true) => [
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
            ? collect($messageLogged->context['exception']->getTrace())->map(
                fn (array $frame) => [
                    'file' => $frame['file'],
                    'line' => $frame['line'],
                ],
            ) : null;

        return collect($messageLogged->context)->merge($context)->toArray();
    }
}
