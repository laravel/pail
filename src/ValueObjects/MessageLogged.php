<?php

declare(strict_types=1);

namespace NunoMaduro\Pail\ValueObjects;

use Illuminate\Support\Carbon;

/**
 * @internal
 */
final readonly class MessageLogged
{
    /**
     * Creates a new instance of the message logged.
     *
     * @param  array{__pail: array{user_id: string,}, exception?: array{class: string, file: string}}  $context
     */
    private function __construct(
        private string $message,
        private string $datetime,
        private string $levelName,
        private array $context,
    ) {
        //
    }

    /**
     * Creates a new instance of the message logged from a json string.
     */
    public static function fromJson(string $json): self
    {
        /** @var array{message: string, context: array{__pail: array{user_id: string,}, exception?: array{class: string, file: string}}, level_name: string, datetime: string} $array */
        $array = json_decode($json, true, 512, JSON_THROW_ON_ERROR);

        [
            'message' => $message,
            'datetime' => $datetime,
            'level_name' => $levelName,
            'context' => $context,
        ] = $array;

        return new self($message, $datetime, $levelName, $context);
    }

    /**
     * Gets the log message's message.
     */
    public function message(): string
    {
        return $this->message;
    }

    /**
     * Gets the log message's time.
     */
    public function time(): string
    {
        if ($_ENV['APP_ENV'] === 'testing') {
            return '03:04:05';
        }

        $time = Carbon::createFromFormat('Y-m-d\TH:i:s.uP', $this->datetime);

        assert($time instanceof Carbon);

        return $time->format('H:i:s');
    }

    /**
     * Gets the log message's class.
     */
    public function classOrType(): string
    {
        return $this->context['exception']['class'] ?? $this->levelName;
    }

    /**
     * Gets the log message's color.
     */
    public function color(): string
    {
        return match ($this->levelName) {
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

    /**
     * Gets the log message's file.
     */
    public function file(): string
    {
        return $this->context['exception']['file'] ?? '';
    }

    /**
     * Gets the log message's user id.
     */
    public function userId(): ?string
    {
        return $this->context['__pail']['user_id'] ?? null;
    }
}
