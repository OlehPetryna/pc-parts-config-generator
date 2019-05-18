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
        'top' => 'M.2',
        'mid' => 'SATA 6 Gb/s',
        'low' => 'SATA 6 Gb/s',
    ];

    public function addSuggestionCriteria(Builder $query): void
    {
        $type = $this->type[(string)$this->suggestionPriority];

        $query
            ->where('specifications.Interface.value', 'like', "%$type%")
            ->where('specifications.Type.value', $this->suggestionPriority->isLowest() ? '!=' : '=', 'SSD');
    }
}