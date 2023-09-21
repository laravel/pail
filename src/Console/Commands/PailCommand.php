<?php

declare(strict_types=1);

namespace NunoMaduro\Pail\Console\Commands;

use Illuminate\Console\Command;
use NunoMaduro\Pail\Guards\EnsurePcntlIsAvailable;
use NunoMaduro\Pail\TailedFile;
use NunoMaduro\Pail\TailOptions;
use NunoMaduro\Pail\TailProcessFactory;
use Symfony\Component\Process\Exception\ProcessSignaledException;

/**
 * @internal
 */
final class PailCommand extends Command
{
    /**
     * {@inheritdoc}
     */
    protected $signature = 'pail {--filter= : Filter the tail} {--user= : Filter the tail by the authenticated user ID}';

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

        $optionsExplained = '';
        $options = TailOptions::fromCommand($this);

        if ((string) $options !== '') {
            $optionsExplained = " (Filtering by {$options})";
        }

        $this->components->info('Tailing application logs'.$optionsExplained);
        $this->comment('  <fg=yellow;options=bold>Press Ctrl+C to exit</>');
        $this->newLine();

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
