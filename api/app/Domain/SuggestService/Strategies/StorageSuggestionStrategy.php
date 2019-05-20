<?php
declare(strict_types=1);

namespace App\Domain\SuggestService\Strategies;

use App\Domain\PcParts\PartsCollection;
use App\Domain\PcParts\PcPart;
use App\Domain\SuggestService\SuggestionStrategy;
use Jenssegers\Mongodb\Eloquent\Builder;

class StorageSuggestionStrategy extends SuggestionStrategy
{

    private $type = [
        'top' => ['M.2', 'SATA 6 Gb/s'],
        'mid' => ['SATA 6 Gb/s', 'SATA 3 Gb/s'],
        'low' => ['SATA 6 Gb/s', 'SATA 3 Gb/s'],
    ];

    public function addSuggestionCriteria(Builder $query): void
    {
        $types = $this->type[(string)$this->suggestionPriority];

        $query->whereNested(function ($query) use ($types) {
            foreach ($types as $type) {
                $query->orWhere('specifications.Interface.value', 'like', "%$type%");
            }
        });

        $query->where('specifications.Type.value', $this->suggestionPriority->isHighest() ? '=' : '!=', 'SSD');
    }
}