<?php
declare(strict_types=1);

namespace App\Domain\CompatibilityService\Strategies;

use App\Domain\PcParts\PcPart;
use Jenssegers\Mongodb\Eloquent\Builder;

class PowerSupplyStrategy extends AbstractStrategy
{
    public function addAcceptanceCriteria(Builder $query): void
    {
        $generalConsumption = 0;
        foreach ($this->compatibilityContextCollection as $item) {
            /**@var PcPart $item*/
            $generalConsumption += $item->getPowerConsumption();
        }

        $query->where('wattage', '>=', $generalConsumption + 100);
    }
}