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
class StorageStrategy extends AbstractStrategy
{
    public function addAcceptanceCriteria(Builder $query): void
    {
        /**@var Motherboard $motherboard */
        $motherboard = $this->compatibilityContextCollection->first(function (PcPart $part) {
            return $part instanceof Motherboard;
        });


        if ($motherboard) {
            $query->whereNested(function ($query) use ($motherboard) {
                foreach ($motherboard->getAvailableStorageTypes() as $type) {
                    $query->orWhere('specifications.Interface.value', 'like', "%$type%");
                }
            });
        }

    }
}