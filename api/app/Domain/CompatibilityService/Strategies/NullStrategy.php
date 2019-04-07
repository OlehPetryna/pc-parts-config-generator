<?php
declare(strict_types=1);

namespace App\Domain\CompatibilityService\Strategies;


use Illuminate\Database\Eloquent\Builder;

class NullStrategy extends AbstractStrategy
{
    public function addAcceptanceCriteria(Builder $query): void
    {

    }
}