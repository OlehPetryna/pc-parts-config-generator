<?php
declare(strict_types=1);

namespace App\Domain\CompatibilityService;

use Jenssegers\Mongodb\Eloquent\Builder;

interface PartsCompatibilityStrategy
{
    public function addAcceptanceCriteria(Builder $query): void;
}