<?php
declare(strict_types=1);

namespace App\Domain\SuggestService\Strategies;

use App\Domain\SuggestService\SuggestionStrategy;
use Jenssegers\Mongodb\Eloquent\Builder;

class MotherboardSuggestionStrategy extends SuggestionStrategy
{
    private $amdSockets = [
        'top' => ['AM4'],
        'mid' => ['AM3', 'AM3+'],
        'low' => ['AM2', 'AM2+', 'FM2', 'FM2+', 'FM1']
    ];

    private $intelSockets = [
        'top' => ['LGA2066', 'LGA1151'],
        'mid' => ['LGA1155', 'LGA1150', 'LGA1151'],
        'low' => ['LGA775']
    ];

    public function addSuggestionCriteria(Builder $query): void
    {
        $vendorRandomize = random_int(0, 100);

        $socket = $vendorRandomize >= 50 ? $this->intelSockets[(string)$this->suggestionPriority] : $this->amdSockets[(string)$this->suggestionPriority];

        $query->whereIn('specifications.Socket / CPU.value', $socket);
    }
}