<?php
declare(strict_types=1);

namespace App\Domain\SuggestService;

use App\Domain\PcParts\PartsCollection;
use App\Domain\PickerWizard\Wizard;

class SuggestService
{
    /**@var Wizard $wizard*/
    private $wizard;

    public function __construct(Wizard $wizard)
    {
        $this->wizard = $wizard;
    }

    public function completeSuggestion(SuggestionCategories $categories): void
    {
        $suggestionContext = new SuggestionContext($categories);
        while (!$this->wizard->endReached()) {
            $suggestStagePart = $this->wizard->buildStagePart();
            $suggestQuery = $this->wizard->findCompatiblePartsQuery();

            $suggestionStrategy = $suggestionContext->pickSuggestionStrategy($suggestStagePart, $this->wizard->getStateParts());
            $suggestionStrategy->addSuggestionCriteria($suggestQuery);

            /**@var PartsCollection $suggestionParts*/
            $suggestionParts = $suggestQuery->get();

            $suggestedPart = $suggestionStrategy->filterSelectedCompatibleParts($suggestionParts);
            $this->wizard->addPart($suggestedPart);
        }
    }
}