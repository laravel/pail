<?php

namespace Laravel\Pail\ValueObjects;

use Illuminate\Support\Carbon;

class MessageLogged
{
    /**
     * Creates a new instance of the message logged.
     *
     * @param  array{__pail: array{origin: array{type: string, command: string, method: string, path: string, auth_id: string}}, exception: array{class: string, file: string}}  $context
     */
    protected function __construct(
        protected string $message,
        protected string $datetime,
        protected string $levelName,
        protected array $context,
    ) {
        //
    }

    /**
     * Creates a new instance of the message logged from a json string.
     */
    public static function fromJson(string $json): self
    {
        /** @var array{message: string, context: array{__pail: array{origin: array{type: string, command: string, method: string, path: string, auth_id: string}}, exception: array{class: string, file: string}}, level_name: string, datetime: string} $array */
        $array = json_decode($json, true, 512, JSON_THROW_ON_ERROR);

        [
            'message' => $message,
            'datetime' => $datetime,
            'level_name' => $levelName,
            'context' => $context,
        ] = $array;

        return new static($message, $datetime, $levelName, $context);
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
        return $this->context['exception']['class'] ?? strtoupper($this->levelName);
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
     * Gets the log message's file, if any.
     */
    public function file(): ?string
    {
        return $this->context['exception']['file'] ?? null;
    }

    /**
     * Gets the log message's auth id.
     */
    public function authId(): ?string
    {
        return $this->context['__pail']['origin']['auth_id'] ?? null;
    }

    /**
     * Gets the log message's origin.
     */
    public function origin(): Origin\Console|Origin\Http
    {
        return match ($this->context['__pail']['origin']['type']) {
            'console' => Origin\Console::fromArray($this->context['__pail']['origin']),
            default => Origin\Http::fromArray($this->context['__pail']['origin']),
        };
    }
}
