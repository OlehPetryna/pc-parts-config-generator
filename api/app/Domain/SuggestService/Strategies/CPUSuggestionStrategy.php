<?php
declare(strict_types=1);

namespace App\Domain\SuggestService\Strategies;

use App\Domain\PcParts\PartsCollection;
use App\Domain\PcParts\PcPart;
use App\Domain\SuggestService\SuggestionStrategy;
use Jenssegers\Mongodb\Eloquent\Builder;

class CPUSuggestionStrategy extends SuggestionStrategy
{
    public function addSuggestionCriteria(Builder $query): void
    {
    }

    public function filterSelectedCompatibleParts(PartsCollection $collection): PcPart
    {
        $collection = $collection->sort(function (PcPart $partA, PcPart $partB) {
            $aCores = (int)$partA->getAttribute('specifications')['Cores']['value'];
            $bCores = (int)$partB->getAttribute('specifications')['Cores']['value'];

            $aFrequency = (float)$partA->getAttribute('specifications')['Operating Frequency']['value'];
            $bFrequency = (float)$partB->getAttribute('specifications')['Operating Frequency']['value'];

            $aTurboFrequency = (float)$partA->getAttribute('specifications')['Turbo Frequency']['value'];
            $bTurboFrequency = (float)$partB->getAttribute('specifications')['Turbo Frequency']['value'];

            $aIsMorePowerfullThanB = -1;
            $bIsMorePowerfullThanA = 1;

            if ($aCores > $bCores) {
                return $aIsMorePowerfullThanB;
            }

            if ($bCores > $aCores) {
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