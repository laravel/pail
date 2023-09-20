<?php

declare(strict_types=1);

namespace NunoMaduro\Pail\Console\Commands;

use Illuminate\Console\Command;
use NunoMaduro\Pail\Guards\EnsurePcntlIsAvailable;
use NunoMaduro\Pail\TailedFile;
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
    protected $signature = 'pail {--filter= : Filter the tail}';

    /**
     * {@inheritdoc}
     */
    protected $description = 'Tails the application logs.';

    /**
     * {@inheritdoc}
     */
    public function handle(TailedFile $file, TailProcessFactory $processFactory): void
    {
        EnsurePcntlIsAvailable::check();

        $this->components->info('Tailing application logs.');
        $this->comment('  <fg=yellow;options=bold>Press Ctrl+C to exit</>');
        $this->newLine();

        $file->create();
        $this->trap([SIGINT, SIGTERM], fn () => $file->destroy());

        try {
            $filter = $this->option('filter');

            assert(is_string($filter) || $filter === null);

            $processFactory->run($file, $this->output, $this->laravel->basePath(), $filter);
        } catch (ProcessSignaledException $e) {
            if (in_array($e->getSignal(), [SIGINT, SIGTERM], true)) {
                $this->newLine();
            }
        } finally {
            $file->destroy();
        }
    }
}
