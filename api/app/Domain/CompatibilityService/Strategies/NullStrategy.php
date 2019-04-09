<?php
declare(strict_types=1);

namespace App\Domain\CompatibilityService\Strategies;

use Jenssegers\Mongodb\Eloquent\Builder;

class NullStrategy extends AbstractStrategy
{
    public function addAcceptanceCriteria(Builder $query): void
    {

    }
}