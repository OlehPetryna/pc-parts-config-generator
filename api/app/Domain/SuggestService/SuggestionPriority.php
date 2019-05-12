<?php
declare(strict_types=1);

namespace App\Domain\SuggestService;

use InvalidArgumentException;

class SuggestionPriority
{
    private $priority;

    public static $availablePriorities = [
        0 => 'low',
        1 => 'mid',
        2 => 'top',
    ];

    public static function highest(): self
    {
        return new self(2);
    }

    public static function medium(): self
    {
        return new self(1);
    }

    public static function lowest(): self
    {
        return new self(0);
    }

    public function __construct(int $priority)
    {
        if (!isset(self::$availablePriorities[$priority])) {
            throw new InvalidArgumentException();
        }

        $this->priority = $priority;
    }

    public function isHighest(): bool
    {
        return $this->priority === 2;
    }

    public function isMedium(): bool
    {
        return $this->priority === 1;
    }

    public function isLowest(): bool
    {
        return $this->priority === 0;
    }

    public function __toString(): string
    {
        return (string)self::$availablePriorities[$this->priority];
    }
}