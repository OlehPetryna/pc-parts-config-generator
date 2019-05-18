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
    protected $alreadySuggestedParts;

    public function __construct(PartsCollection $alreadySuggestedParts, SuggestionPriority $suggestionPriority)
    {
        $this->alreadySuggestedParts = $alreadySuggestedParts;
        $this->suggestionPriority = $suggestionPriority;
    }

    public function filterSelectedCompatibleParts(PartsCollection $collection): PcPart
    {
        return $collection->random();
    }

    protected function pickRandomElementFromMiddle(PartsCollection $collection): PcPart
    {
        $keys = $collection->keys();

        $chunkSize = ceil($keys->count() / 4);
        $offset = floor($keys->count() - $chunkSize / 2);

        return $collection->get($keys->slice($offset, $chunkSize)->random());
    }
    
    protected function pickRandomElementFromTop(PartsCollection $collection): PcPart
    {
        $keys = $collection->keys();

        $chunkSize = ceil($keys->count() / 4);

        return $collection->get($keys->slice(0, $chunkSize)->random());
    }
    
    protected function pickRandomElementFromBottom(PartsCollection $collection): PcPart
    {
        $keys = $collection->keys();

        $chunkSize = ceil($keys->count() / 4);
        $offset = floor($keys->count() - $chunkSize);

        return $collection->get($keys->slice($offset)->random());
    }

    abstract public function addSuggestionCriteria(Builder $query): void;
}