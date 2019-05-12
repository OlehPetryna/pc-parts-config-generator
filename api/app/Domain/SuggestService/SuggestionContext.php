<?php
declare(strict_types=1);

namespace App\Domain\SuggestService;

use App\Domain\PcParts\Entities\CPU;
use App\Domain\PcParts\Entities\Motherboard;
use App\Domain\PcParts\Entities\VideoCard;
use App\Domain\PcParts\PartsCollection;
use App\Domain\PcParts\PcPart;
use App\Domain\SuggestService\Strategies\CPUSuggestionStrategy;
use App\Domain\SuggestService\Strategies\MotherboardSuggestionStrategy;

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
            return new CPUSuggestionStrategy($alreadySuggestedParts, $this->categories->getGraphicsPriority());
        }
    }
}