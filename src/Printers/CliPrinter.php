<?php

declare(strict_types=1);

namespace NunoMaduro\Pail\Printers;

use NunoMaduro\Pail\Contracts\Printer;
use NunoMaduro\Pail\TailOptions;
use NunoMaduro\Pail\ValueObjects\MessageLogged;
use Symfony\Component\Console\Output\OutputInterface;

use function Termwind\render;
use function Termwind\renderUsing;
use function Termwind\terminal;

/**
 * @internal
 */
final readonly class CliPrinter implements Printer
{
    /**
     * Creates a new instance printer instance.
     */
    public function __construct(private OutputInterface $output, private string $basePath)
    {
        //
    }

    /**
     * {@inheritdoc}
     */
    public function print(TailOptions $options, string $messageLogged): void
    {
        renderUsing($this->output);

        $messageLogged = MessageLogged::fromJson($messageLogged);

        if (is_string($options->userId) && $messageLogged->userId() !== $options->userId) {
            return;
        }

        $classOrType = $messageLogged->classOrType();
        $color = $messageLogged->color();
        $file = $this->truncateFile($messageLogged->file(), $classOrType);
        $message = $this->truncateMessage($messageLogged->message());
        $messageClasses = $this->output->isVerbose() ? '' : 'truncate';
        $time = $messageLogged->time();

        render(<<<HTML
            <div class="max-w-150">
                <div class="flex mx-2">
                    <div>
                        <span class="text-gray-500">$time</span>
                        <span class="px-1 text-$color font-bold">$classOrType</span>
                    </div>
                    <span class="flex-1 content-repeat-[.] text-gray"></span>
                    <span class="text-gray ml-1">
                       $file
                    </span>
                </div>
                <div class="ml-2 $messageClasses">
                    <span class="">$message</span>
                </div>
            </div>
        HTML);

    }

    /**
     * Truncates the file name, if needed.
     */
    private function truncateFile(string $file, string $classOrType): string
    {
        if ($file && $_ENV['APP_ENV'] === 'testing') {
            $file = $this->basePath.'/app/MyClass.php:12';
        }

        $file = str_replace($this->basePath.'/', '', $file);

        if (! $this->output->isVerbose()) {
            $fileSize = max(0, min(terminal()->width() - strlen($classOrType) - 16, 145));

            if (strlen($file) > $fileSize) {
                $file = mb_substr($file, 0, $fileSize).'…';
            }
        }

        if ($file === '…') {
            return '';
        }

        return $file;
    }

    /**
     * Truncates the message, if needed.
     */
    private function truncateMessage(string $message): string
    {
        if (! $this->output->isVerbose()) {
            $messageSize = max(0, min(terminal()->width() - 5, 145));

            if (strlen($message) > $messageSize) {
                $message = mb_substr($message, 0, $messageSize).'…';
            }
        }

        return $message;
    }
}
