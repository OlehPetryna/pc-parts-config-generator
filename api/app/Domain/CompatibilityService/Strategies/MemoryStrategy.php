<?php
declare(strict_types=1);

namespace App\Domain\CompatibilityService\Strategies;

use App\Domain\PcParts\Entities\CPU;
use App\Domain\PcParts\Entities\Motherboard;
use App\Domain\PcParts\Entities\RAM;
use App\Domain\PcParts\PcPart;
use Jenssegers\Mongodb\Collection;
use Jenssegers\Mongodb\Eloquent\Builder;

/**
 * @property RAM $findingCompatibilityForPart
 */
class MemoryStrategy extends AbstractStrategy
{
    public function addAcceptanceCriteria(Builder $query): void
    {
        /**@var Motherboard $motherboard */
        $motherboard = $this->compatibilityContextCollection->first(function (PcPart $part) {
            return $part instanceof Motherboard;
        });

        /**@var CPU $cpu */
        $cpu = $this->compatibilityContextCollection->first(function (PcPart $part) {
            return $part instanceof CPU;
        });

        if ($motherboard) {
            $query->where('specifications.Speed.value', 'like', "%{$motherboard->getAvailableMemoryTypes()}%");
            $query->where('memorySize', '<=', $motherboard->getMaximumSupportedMemory());
        }

        if ($cpu && ($cpuMaxSupportedMemory = $cpu->getMaximumSupportedMemory())) {
            $query->where('memorySize', '<=', $cpuMaxSupportedMemory);
        }
    }
}