<?php
declare(strict_types=1);

namespace App\Domain\SuggestService\Strategies;

use App\Domain\PcParts\PartsCollection;
use App\Domain\PcParts\PcPart;
use App\Domain\SuggestService\SuggestionStrategy;
use Jenssegers\Mongodb\Eloquent\Builder;

class GraphicsSuggestionStrategy extends SuggestionStrategy
{

    private $memoryType = [
        'top' => ['GDDR5', 'GDDR6'],
        'mid' => ['GDDR5'],
        'low' => ['GDDR3'],
    ];

    private $chipsets = [
        'professional' => ['Quadro', 'Radeon Pro'],
        'other' => ['GeForce', 'Radeon']
    ];

    public function addSuggestionCriteria(Builder $query): void
    {
        $memoryType = $this->memoryType[(string)$this->suggestionPriority];

        $query
            ->whereIn('specifications.Memory Type.value', $memoryType)
            ->whereNested(function ($query) {
                $chipsets = $this->chipsets[$this->suggestionPriority->professionalPurpose() ? 'professional' : 'other'];
                foreach ($chipsets as $chipset) {
                    $query->orWhere('specifications.Chipset.value', 'like', "%$chipset%");
                }
            });
    }

    public function filterSelectedCompatibleParts(PartsCollection $collection): PcPart
    {
        $collection = $collection->sort(function (PcPart $partA, PcPart $partB) {
            $aMemory = (int)$partA->getAttribute('specifications')['Memory']['value'];
            $bMemory = (int)$partB->getAttribute('specifications')['Memory']['value'];

            $aFrequency = (float)$partA->getAttribute('specifications')['Core Clock']['value'];
            $bFrequency = (float)$partB->getAttribute('specifications')['Core Clock']['value'];

            $aTurboFrequency = (float)$partA->getAttribute('specifications')['Boost Clock']['value'];
            $bTurboFrequency = (float)$partB->getAttribute('specifications')['Boost Clock']['value'];

            $aIsMorePowerfullThanB = -1;
            $bIsMorePowerfullThanA = 1;

            if ($aMemory > $bMemory) {
                return $aIsMorePowerfullThanB;
            }

            if ($bMemory > $aMemory) {
                return $bIsMorePowerfullThanA;
            }

            if ($aFrequency > $bFrequency) {
                return $aIsMorePowerfullThanB;
            }

            if ($aFrequency < $bFrequency) {
                return $bIsMorePowerfullThanA;
            }

            if ($aTurboFrequency > $bTurboFrequency) {
                return $aIsMorePowerfullThanB;
            }

            if ($aTurboFrequency < $bTurboFrequency) {
                return $bIsMorePowerfullThanA;
            }

            return 0;
        });

        if ($this->suggestionPriority->isHighest()) {
            return $collection->first();
        }

        if ($this->suggestionPriority->isLowest()) {
            return $collection->last();
        }

        return $collection->get((int)($collection->count() / 2));
    }
}