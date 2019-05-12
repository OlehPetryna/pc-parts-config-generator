<?php
declare(strict_types=1);

namespace App\Domain\SuggestService;

use App\Domain\PcParts\PartsCollection;
use App\Domain\PcParts\PcPart;
use Jenssegers\Mongodb\Eloquent\Builder;

abstract class SuggestionStrategy
{
    /**
     * @var SuggestionPriority
     */
    protected $suggestionPriority;

    /**
     * @var PartsCollection
     */
    protected  $alreadySuggestedParts;

    public function __construct(PartsCollection $alreadySuggestedParts, SuggestionPriority $suggestionPriority)
    {
        $this->alreadySuggestedParts = $alreadySuggestedParts;
        $this->suggestionPriority = $suggestionPriority;
    }

    public function filterSelectedCompatibleParts(PartsCollection $collection): PcPart
    {
        return $collection->random();
    }

    abstract public function addSuggestionCriteria(Builder $query): void;
}