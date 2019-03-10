<?php
declare(strict_types=1);

namespace App\Domain\CompatibilityService;


use Illuminate\Database\Eloquent\Builder;

interface PartsCompatibilityStrategy
{
    public function addAcceptanceCriteria(Builder $query): array;
}