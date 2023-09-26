<?php

namespace Laravel\Pail\Console\Commands;

use Illuminate\Console\Command;
use Laravel\Pail\Guards\EnsurePcntlIsAvailable;
use Laravel\Pail\TailedFile;
use Laravel\Pail\TailOptions;
use Laravel\Pail\TailProcessFactory;
use Symfony\Component\Process\Exception\ProcessSignaledException;
use function Termwind\render;
use function Termwind\renderUsing;

class PailCommand extends Command
{
    /**
     * {@inheritdoc}
     */
    protected $signature = 'pail
        {--filter= : Filter the tail}
        {--message= : Filter the tail by the message}
        {--level= : Filter the tail by the level}
        {--auth= : Filter the tail by the authenticated ID}
        {--user= : Filter the tail by the authenticated ID}';

    /**
     * {@inheritdoc}
     */
    protected $description = 'Tails the application logs.';

    /**
     * {@inheritdoc}
     */
    public function handle(TailProcessFactory $processFactory): void
    {
        EnsurePcntlIsAvailable::check();

        $options = TailOptions::fromCommand($this);
        $optionsExplained = '';

        if ((string) $options !== '') {
            $optionsExplained = " (Filtering by {$options})";
        }

        renderUsing($this->output);
        render(<<<HTML
            <div class="mx-2 mb-1 mt-1 flex">
                <div>
                    <span class="px-1 bg-blue uppercase text-white">INFO</span>
                    <span class="flex-1">
                        <span class="ml-1 ">Tailing application logs.</span>
                        <span class="ml-1">$optionsExplained</span>
                    </span>
                </div>
                <span class="flex-1"></span>
                <span class="text-gray">
                    <span class="text-gray">Press Ctrl+C to exit</span>
                </span>
            </div>
            HTML,
        );

        $file = new TailedFile(storage_path('pail/'.uniqid().'.pail'));
        $file->create();
        $this->trap([SIGINT, SIGTERM], fn () => $file->destroy());

        try {
            $processFactory->run($file, $this->output, $this->laravel->basePath(), $options);
        } catch (ProcessSignaledException $e) {
            if (in_array($e->getSignal(), [SIGINT, SIGTERM], true)) {
                $this->newLine();
            }
        } finally {
            $file->destroy();
        }
    }
}
