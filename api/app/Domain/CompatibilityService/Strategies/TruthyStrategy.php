<?php
declare(strict_types=1);

namespace App\Domain\CompatibilityService\Strategies;


use Illuminate\Database\Eloquent\Builder;

class TruthyStrategy extends AbstractStrategy
{
    public function addAcceptanceCriteria(Builder $query): array
    {
        return ['1', '=', '1'];
    }
}