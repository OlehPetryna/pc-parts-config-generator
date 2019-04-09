<?php
declare(strict_types=1);

namespace App\Domain\CompatibilityService\Strategies;

use App\Domain\PcParts\Entities\Motherboard;
use App\Domain\PcParts\PcPart;
use Jenssegers\Mongodb\Eloquent\Builder;

class SocketStrategy extends AbstractStrategy
{
    public function addAcceptanceCriteria(Builder $query): void
    {
        /**@var Motherboard $motherboard*/
        $motherboard = $this->compatibilityContextCollection->first(function (PcPart $part) {
            return $part instanceof Motherboard;
        });

        $query->where(
            'specifications.Socket.value',
            '=',
            $motherboard->getAttribute('specifications')['CPU Socket']['value']
        );
    }
}