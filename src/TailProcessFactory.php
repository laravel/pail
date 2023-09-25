<?php

namespace Laravel\Pail;

use Illuminate\Support\Facades\Process;
use Illuminate\Support\Str;
use Laravel\Pail\Printers\CliPrinter;
use Laravel\Pail\ValueObjects\MessageLogged;
use Symfony\Component\Console\Output\OutputInterface;

class TailProcessFactory
{
    /**
     * Creates a new instance of the tail process factory.
     */
    public function run(TailedFile $file, OutputInterface $output, string $basePath, TailOptions $options): void
    {
        $printer = new CliPrinter($output, $basePath);

        Process::timeout(3600)
            ->tty(false)
            ->run(
                $this->command($file),
                function (string $type, string $buffer) use ($options, $printer): void {
                    Str::of($buffer)
                        ->explode("\n")
                        ->filter(fn (string $line) => $line !== '')
                        ->map(fn (string $line) => MessageLogged::fromJson($line))
                        ->filter(fn (MessageLogged $messageLogged) => $options->accepts($messageLogged))
                        ->each(fn (MessageLogged $messageLogged) => $printer->print($messageLogged, $options));
                }
            );
    }

    /**
     * Returns the raw command.
     */
    protected function command(TailedFile $file): string
    {
        return '\\tail -F "'.$file->__toString().'"';
    }
}
