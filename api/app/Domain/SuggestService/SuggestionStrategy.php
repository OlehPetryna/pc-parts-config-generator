<?php
declare(strict_types=1);

namespace App\Domain\SuggestService;

use App\Domain\PcParts\PartsCollection;
use App\Domain\PcParts\PcPart;
use Illuminate\Support\Collection;
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

        $chunkSize = $this->determineChunkSize($keys);
        $offset = floor($keys->count() - $chunkSize / 2);

        return $collection->get($this->pickRandomKeyFromSlice($keys, $chunkSize, $offset));
    }
    
    protected function pickRandomElementFromTop(PartsCollection $collection): PcPart
    {
        $keys = $collection->keys();

        $chunkSize = $this->determineChunkSize($keys);

        return $collection->get($this->pickRandomKeyFromSlice($keys, $chunkSize));
    }
    
    protected function pickRandomElementFromBottom(PartsCollection $collection): PcPart
    {
        $keys = $collection->keys();

        $chunkSize = $this->determineChunkSize($keys);
        $offset = floor($keys->count() - $chunkSize);

        return $collection->get($this->pickRandomKeyFromSlice($keys, null, $offset));
    }

    private function determineChunkSize(Collection $keys): float
    {
        return 3;
    }

    private function pickRandomKeyFromSlice(Collection $keys, $chunkSize = null, $offset = null): int
    {
        if ($chunkSize <= 2) {
            return $keys->random();
        }

        return $keys->slice((int)$offset, (int)$chunkSize)->random();
    }

    abstract public function addSuggestionCriteria(Builder $query): void;
}