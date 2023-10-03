<?php

namespace Laravel\Pail;

use Illuminate\Console\Events\CommandStarting;
use Illuminate\Foundation\Auth\User;
use Illuminate\Log\Events\MessageLogged;
use Illuminate\Queue\Events\JobProcessing;
use Illuminate\Support\Facades\Auth;

class Handler
{
    /**
     * The last lifecycle captured event.
     */
    protected CommandStarting|JobProcessing|null $lastLifecycleEvent = null;

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
    public function setLastLifecycleEvent(CommandStarting|JobProcessing|null $event): void
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
        $context = ['__pail' => ['origin' => match (true) {
            $this->runningInConsole && $this->lastLifecycleEvent instanceof CommandStarting => [
                'type' => 'console',
                'command' => $this->lastLifecycleEvent->command,
            ],
            $this->runningInConsole && $this->lastLifecycleEvent instanceof JobProcessing => [
                'type' => 'queue',
                'queue' => $this->lastLifecycleEvent->job->getQueue(),
                'job' => $this->lastLifecycleEvent->job->resolveName(),
            ],
            $this->runningInConsole => [
                'type' => 'console',
            ],
            default => [
                'type' => 'http',
                'method' => request()->method(), // @phpstan-ignore-line
                'path' => request()->path(), // @phpstan-ignore-line
                'auth_id' => Auth::id(),
                'auth_email' => Auth::user() instanceof User ? Auth::user()->email : null, // @phpstan-ignore-line
            ],
        }]];

        $context['__pail']['origin']['trace'] = isset($messageLogged->context['exception'])
            ? collect($messageLogged->context['exception']->getTrace()) // @phpstan-ignore-line
                ->filter(fn (array $frame) => isset($frame['file'])) // @phpstan-ignore-line
                ->map(fn (array $frame) => [ // @phpstan-ignore-line
                    'file' => $frame['file'],
                    'line' => $frame['line'] ?? null,
                ])->values()
            : null;

        return collect($messageLogged->context)->merge($context)->toArray();
    }
}
