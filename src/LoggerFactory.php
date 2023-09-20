<?php

declare(strict_types=1);

namespace NunoMaduro\Pail;

use Monolog\Formatter\JsonFormatter;
use Monolog\Handler\StreamHandler;
use Monolog\Level;
use Monolog\Logger;
use Psr\Log\LoggerInterface;

/**
 * @internal
 */
final readonly class LoggerFactory
{
    /**
     * Creates a new instance of the logger factory.
     */
    public function __construct(
        private TailedFile $tailedFile,
    ) {
        //
    }

    /**
     * Creates a new instance of the logger.
     */
    public function create(): LoggerInterface
    {
        $handler = new StreamHandler($this->tailedFile->__toString(), Level::Debug);
        $handler->setFormatter(new JsonFormatter());

        return new Logger('pail', [$handler]);
    }
}
