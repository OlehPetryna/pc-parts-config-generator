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

    private $isProfessionalPurpose;

    public static function highest(bool $isProfessionalPurpose = false): self
    {
        return new self(2, $isProfessionalPurpose);
    }

    public static function medium(bool $isProfessionalPurpose = false): self
    {
        return new self(1, $isProfessionalPurpose);
    }

    public static function lowest(bool $isProfessionalPurpose = false): self
    {
        return new self(0, $isProfessionalPurpose);
    }

    public function __construct(int $priority, bool $isProfessionalPurpose = false)
    {
        if (!isset(self::$availablePriorities[$priority])) {
            throw new InvalidArgumentException();
        }

        $this->priority = $priority;
        $this->isProfessionalPurpose = $isProfessionalPurpose;
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

    public function professionalPurpose(): bool
    {
        return $this->isProfessionalPurpose;
    }

    public function __toString(): string
    {
        return (string)self::$availablePriorities[$this->priority];
    }
}