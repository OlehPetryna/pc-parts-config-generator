<?php
declare(strict_types=1);

namespace App\Domain\SuggestService\Strategies;

use App\Domain\PcParts\Entities\RAM;
use App\Domain\PcParts\PartsCollection;
use App\Domain\PcParts\PcPart;
use App\Domain\SuggestService\SuggestionStrategy;
use Jenssegers\Mongodb\Eloquent\Builder;

class MemorySuggestionStrategy extends SuggestionStrategy
{
    private $speed = [
        'top' => ['DDR4'],
        'mid' => ['DDR3', 'DDR4'],
        'low' => ['DDR3', 'DDR2'],
    ];

    private $memoryLimit = [
        'top' => ['from' => 16, 'to' => 256],
        'mid' => ['from' => 8, 'to' => 16],
        'low' => ['from' => 4, 'to' => 8],
    ];

    public function addSuggestionCriteria(Builder $query): void
    {
        $speeds = $this->speed[(string)$this->suggestionPriority];

        $query->whereNested(function ($query) use ($speeds) {
            foreach ($speeds as $speed) {
                $query->orWhere('specifications.Speed.value', 'like', "%$speed%");
            }
        });

    }

    public function filterSelectedCompatibleParts(PartsCollection $collection): PcPart
    {
        $lowerMemoryLimit = $this->memoryLimit[(string)$this->suggestionPriority]['from'];
        $upperMemoryLimit = $this->memoryLimit[(string)$this->suggestionPriority]['to'];

        $collection = $collection->filter(function (RAM $part) use ($lowerMemoryLimit, $upperMemoryLimit) {
            $capacity = $part->getSize();
            return $capacity >= $lowerMemoryLimit && $capacity < $upperMemoryLimit;
        });

        $collection = $collection->sort(function (PcPart $partA, PcPart $partB) {
            $aFrequency = (int)explode('-', $partA->getAttribute('specifications')['Speed']['value'])[1];
            $bFrequency = (int)explode('-', $partB->getAttribute('specifications')['Speed']['value'])[1];

            $aIsMorePowerfullThanB = -1;
            $bIsMorePowerfullThanA = 1;

            if ($aFrequency > $bFrequency) {
                return $aIsMorePowerfullThanB;
            }

            if ($aFrequency < $bFrequency) {
                return $bIsMorePowerfullThanA;
            }

            return 0;
        });

        if ($this->suggestionPriority->isHighest()) {
            return $this->pickRandomElementFromTop($collection);
        }

        if ($this->suggestionPriority->isLowest()) {
            return $this->pickRandomElementFromBottom($collection);
        }

        return $this->pickRandomElementFromMiddle($collection);
    }
}