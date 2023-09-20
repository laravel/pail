<?php

declare(strict_types=1);

namespace NunoMaduro\Pail;

use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Process;
use Illuminate\Support\Str;
use NunoMaduro\Pail\Printers\CliPrinter;
use Symfony\Component\Console\Output\OutputInterface;

/**
 * @internal
 */
final readonly class TailProcessFactory
{
    /**
     * Creates a new instance of the tail process factory.
     */
    public function run(TailedFile $file, OutputInterface $output, string $basePath, ?string $filter): void
    {
        $printer = new CliPrinter($output, $basePath);

        Process::forever()
            ->tty(false)
            ->run(
                $this->command($file),
                function (string $type, string $buffer) use ($filter, $printer): void {
                    /** @var array<int, string> $lines */
                    $lines = Str::of($buffer)
                        ->explode("\n")
                        ->filter(fn (string $line): bool => $line !== '')
                        ->when(
                            $filter,
                            fn (Collection $lines, string $filter): Collection => $lines->filter(
                                fn (string $line): bool => str_contains($line, $filter)
                            )
                        )->values();

                    foreach ($lines as $line) {
                        $printer->print($line);
                    }
                }
            );
    }

    /**
     * Returns the raw command.
     */
    private function command(TailedFile $file): string
    {
        return '\\tail -F "'.$file->__toString().'"';
    }
}
