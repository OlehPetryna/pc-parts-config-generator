<?php
declare(strict_types=1);

namespace App\Domain\CompatibilityService\Strategies;

use App\Domain\PcParts\Entities\Motherboard;
use App\Domain\PcParts\Entities\VideoCard;
use App\Domain\PcParts\PcPart;
use Jenssegers\Mongodb\Eloquent\Builder;

class CaseStrategy extends AbstractStrategy
{
    public function addAcceptanceCriteria(Builder $query): void
    {
        /**@var Motherboard $motherboard */
        $motherboard = $this->compatibilityContextCollection->first(function (PcPart $part) {
            return $part instanceof Motherboard;
        });

        /**@var VideoCard $videocard */
        $videocard = $this->compatibilityContextCollection->first(function (PcPart $part) {
            return $part instanceof VideoCard;
        });

        if ($motherboard) {
            $formFactor = $motherboard->getAttribute('specifications')['Form Factor']['value'];

            $query->where('specifications.Motherboard Form Factor.value', 'regex', "/($formFactor$)|(^$formFactor)|(, $formFactor,)/gi");
        }

        if ($videocard) {
            $query->where('maxVideoCardLength', '>=', $videocard->getLength());
        }
    }
}