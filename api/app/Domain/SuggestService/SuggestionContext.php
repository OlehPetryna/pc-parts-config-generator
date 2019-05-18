<?php
declare(strict_types=1);

namespace App\Domain\SuggestService;

use App\Domain\PcParts\Entities\CPU;
use App\Domain\PcParts\Entities\Motherboard;
use App\Domain\PcParts\Entities\RAM;
use App\Domain\PcParts\Entities\Storage;
use App\Domain\PcParts\Entities\VideoCard;
use App\Domain\PcParts\PartsCollection;
use App\Domain\PcParts\PcPart;
use App\Domain\SuggestService\Strategies\CPUSuggestionStrategy;
use App\Domain\SuggestService\Strategies\GraphicsSuggestionStrategy;
use App\Domain\SuggestService\Strategies\MemorySuggestionStrategy;
use App\Domain\SuggestService\Strategies\MotherboardSuggestionStrategy;
use App\Domain\SuggestService\Strategies\RandomStrategy;
use App\Domain\SuggestService\Strategies\StorageSuggestionStrategy;

class SuggestionContext
{
    /**@var SuggestionCategories $categories*/
    private $categories;

    public function __construct(SuggestionCategories $categories)
    {
        $this->categories = $categories;
    }

    public function pickSuggestionStrategy(PcPart $part, PartsCollection $alreadySuggestedParts): SuggestionStrategy
    {
        if ($part instanceof Motherboard) {
            return new MotherboardSuggestionStrategy($alreadySuggestedParts, $this->categories->getMotherboardPriority());
        }

        if ($part instanceof CPU) {
            return new CPUSuggestionStrategy($alreadySuggestedParts, $this->categories->getCPUPriority());
        }

        if ($part instanceof VideoCard) {
            return new GraphicsSuggestionStrategy($alreadySuggestedParts, $this->categories->getGraphicsPriority());
        }

        if ($part instanceof RAM) {
            return new MemorySuggestionStrategy($alreadySuggestedParts, $this->categories->getMemoryPriority());
        }

        if ($part instanceof Storage) {
            return new StorageSuggestionStrategy($alreadySuggestedParts, $this->categories->getStoragePriority());
        }

        return new RandomStrategy($alreadySuggestedParts, SuggestionPriority::medium());
    }
}