<?php

namespace Exceptions;

use Exception;
use Throwable;

class LeveledException extends Exception
{
    const LEVEL_INFO = 0;
    const LEVEL_WARNING = 1;
    const LEVEL_ERROR = 2;

    private $level = LeveledException::LEVEL_INFO;

    /**
     * LeveledException constructor.
     *
     * @param  string  $message  The message of the exception.
     * @param  int  $level  The level id of the exception.
     * @param  int  $code  The code for this exception.
     * @param  Throwable|null  $previous  The previous exception.
     */
    public function __construct(
        string $message = "",
        int $level = LeveledException::LEVEL_INFO,
        int $code = 0,
        Throwable $previous = null
    ) {
        parent::__construct($message, $code, $previous);

        $this->level = $level;
    }

    public static function stringifyLevel(int $level): string
    {
        switch ($level) {
            case self::LEVEL_INFO:
                return "INFO";
            case self::LEVEL_WARNING:
                return "WARNING";
            case self::LEVEL_ERROR:
                return "ERROR";
            default:
                return "Invalid level";
        }
    }

    /**
     * Returns the level of this exception.
     *
     * @return int The exception level id.
     */
    public function getLevel(): int
    {
        return $this->level;
    }
}