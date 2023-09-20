<?php

declare(strict_types=1);

namespace NunoMaduro\Pail\Printers;

use Illuminate\Support\Carbon;
use NunoMaduro\Pail\Contracts\Printer;
use NunoMaduro\Pail\TailOptions;
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
    public function print(TailOptions $options, string $logMessage): void
    {
        renderUsing($this->output);

        /** @var array{message: string, context: array{__pail: array{user_id: string,}, exception?: array{class: string, file: string}}, level_name: string, datetime: string} $logMessage */
        $logMessage = json_decode($logMessage, true, 512, JSON_THROW_ON_ERROR);

        [
            'message' => $message,
            'context' => $context,
            'level_name' => $levelName,
            'datetime' => $datetime,
        ] = $logMessage;

        $time = $this->time($datetime);

        $levelColor = $this->levelColor($levelName);

        [
            'class' => $type,
            'file' => $href,
        ] = $context['exception'] ?? [
            'class' => $levelName,
            'file' => '',
        ];

        if (is_string($options->userId)) {
            if ((string) $context['__pail']['user_id'] !== $options->userId) {
                return;
            }
        }

        $file = str_replace($this->basePath.'/', '', (string) $href);

        $messageClasses = $this->output->isVerbose() ? '' : 'truncate';

        if (! $this->output->isVerbose()) {
            $messageSize = max(0, min(terminal()->width() - 5, 145));

            if (strlen($message) > $messageSize) {
                $message = mb_substr($message, 0, $messageSize).'…';
            }
        }

        if (! $this->output->isVerbose()) {
            $fileSize = max(0, min(terminal()->width() - strlen($type) - 16, 145));

            if (strlen($file) > $fileSize) {
                $file = mb_substr($file, 0, $fileSize).'…';
            }
        }

        if ($file === '…') {
            $file = '';
        }

        render(<<<HTML
            <div class="max-w-150">
                <div class="flex mx-2">
                    <div>
                        <span class="text-gray-500">$time</span>
                        <span class="px-1 text-$levelColor font-bold">$type</span>
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
     * Returns the time of the given date.
     */
    private function time(string $date): string
    {
        $time = Carbon::createFromFormat('Y-m-d\TH:i:s.uP', $date);

        assert($time instanceof Carbon);

        return $time->format('H:i:s');
    }

    /**
     * Returns the color of the given level.
     */
    private function levelColor(string $level): string
    {
        return match ($level) {
            'DEBUG' => 'gray',
            'INFO' => 'blue',
            'NOTICE' => 'yellow',
            'WARNING' => 'yellow',
            'ERROR' => 'red',
            'CRITICAL' => 'red',
            'ALERT' => 'red',
            'EMERGENCY' => 'red',
            default => 'gray',
        };
    }
}
