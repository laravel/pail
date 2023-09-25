<?php

namespace Laravel\Pail\Printers;

use Illuminate\Support\Collection;
use Illuminate\Support\Str;
use Laravel\Pail\Contracts\Printer;
use Laravel\Pail\TailOptions;
use Laravel\Pail\ValueObjects\MessageLogged;
use Laravel\Pail\ValueObjects\Origin\Http;
use Symfony\Component\Console\Output\OutputInterface;

use function Termwind\render;
use function Termwind\renderUsing;
use function Termwind\terminal;

class CliPrinter implements Printer
{
    /**
     * Creates a new instance printer instance.
     */
    public function __construct(protected OutputInterface $output, protected string $basePath)
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

        if (is_string($options->authId) && $messageLogged->authId() !== $options->authId) {
            return;
        }

        $classOrType = $this->truncateClassOrType($messageLogged->classOrType());
        $color = $messageLogged->color();
        $message = $this->truncateMessage($messageLogged->message());
        $time = $messageLogged->time();

        $tagsHtml = $this->tagsHtml($messageLogged);
        $fileHtml = $this->fileHtml($messageLogged->file(), $classOrType);

        $messageClasses = $this->output->isVerbose() ? '' : 'truncate';

        render(<<<HTML
            <div class="max-w-150">
                <div class="flex">
                    <div>
                        <span class="mr-1 text-gray">┌</span>
                        <span class="text-gray">$time</span>
                        <span class="px-1 text-$color font-bold">$classOrType</span>
                    </div>
                    <span class="flex-1 content-repeat-[─] text-gray"></span>
                    <span class="text-gray">
                        $fileHtml
                        <span class="text-gray">┐</span>
                    </span>
                </div>
                <div class="flex $messageClasses">
                    <span>
                        <span class="mr-1 text-gray">│</span>
                        <span>$message</span>
                    </span>
                    <span class="flex-1"></span>
                    <span class="flex-1 text-gray text-right">│</span>
                </div>
                <div class="flex text-gray">
                    <span>└</span>
                    <span class="mr-1 flex-1 content-repeat-[─]"></span>
                    $tagsHtml
                    <span class="ml-1">┘</span>
                </div>
            </div>
        HTML);
    }

    /**
     * Gets the file html.
     */
    protected function fileHtml(?string $file, string $classOrType): ?string
    {
        if (is_null($file)) {
            return null;
        }

        if ($_ENV['APP_ENV'] === 'testing') {
            $file = $this->basePath.'/app/MyClass.php:12';
        }

        $file = str_replace($this->basePath.'/', '', $file);

        if (! $this->output->isVerbose()) {
            $file = (string) Str::of($file)
                ->explode('/')
                ->when(
                    fn (Collection $file) => $file->count() > 4,
                    fn (Collection $file) => $file->take(2)->merge(
                        ['…', (string) $file->last()],
                    ),
                )->implode('/');

            $fileSize = max(0, min(terminal()->width() - strlen($classOrType) - 16, 145));

            if (strlen($file) > $fileSize) {
                $file = mb_substr($file, 0, $fileSize).'…';
            }
        }

        if ($file === '…') {
            return null;
        }

        $file = str_replace('……', '…', $file);

        return <<<HTML
            <span class="text-gray mx-1">
                $file
            </span>
        HTML;
    }

    /**
     * Truncates the class or type, if needed.
     */
    protected function truncateClassOrType(string $classOrType): string
    {
        if ($this->output->isVerbose()) {
            return $classOrType;
        }

        return Str::of($classOrType)
            ->explode('\\')
            ->when(
                fn (Collection $classOrType) => $classOrType->count() > 4,
                fn (Collection $classOrType) => $classOrType->take(2)->merge(
                    ['…', (string) $classOrType->last()]
                ),
            )->implode('\\');
    }

    /**
     * Truncates the message, if needed.
     */
    protected function truncateMessage(string $message): string
    {
        if (! $this->output->isVerbose()) {
            $messageSize = max(0, min(terminal()->width() - 5, 145));

            if (strlen($message) > $messageSize) {
                $message = mb_substr($message, 0, $messageSize).'…';
            }
        }

        return $message;
    }

    /**
     * Gets the tags html.
     */
    public function tagsHtml(MessageLogged $messageLogged): string
    {
        $origin = $messageLogged->origin();

        if ($origin instanceof Http) {
            if (str_starts_with($path = $origin->path, '/') === false) {
                $path = '/'.$origin->path;
            }

            $tags = [
                strtoupper($origin->method) => $path,
                'Auth ID: ' => $origin->authId ?: 'guest',
            ];
        } else {
            $tags = [
                '' => $origin->command ?: 'artisan',
            ];
        }

        return collect($tags)
            ->map(fn (string $value, string $key): string => "<span class=\"font-bold\">$key $value</span>")->implode(' | ');
    }
}
