<?php
declare(strict_types=1);

namespace App\Domain\SuggestService;

use App\Domain\PcParts\PartsCollection;
use App\Domain\PickerWizard\Wizard;
use App\Domain\SuggestService\BudgetControl\BudgetControl;
use Psr\Log\LoggerInterface;

class SuggestService
{
    /**@var Wizard $wizard*/
    private $wizard;

    private $logger;

    public function __construct(Wizard $wizard, LoggerInterface $logger)
    {
        $this->wizard = $wizard;
        $this->logger = $logger;
    }

    public function completeSuggestion(SuggestionCategories $categories, BudgetControl $budgetControl): void
    {
        $suggestionContext = new SuggestionContext($categories);

        while (!$this->wizard->endReached()) {
            $suggestStagePart = $this->wizard->buildStagePart();
            $suggestQuery = $this->wizard->findCompatiblePartsQuery();

            $suggestionStrategy = $suggestionContext->pickSuggestionStrategy($suggestStagePart, $this->wizard->getStateParts());
            $suggestionStrategy->addSuggestionCriteria($suggestQuery);

            $budgetControl->addPriceConstraint($suggestQuery);

            /**@var PartsCollection $suggestionParts*/
            $suggestionParts = $suggestQuery->get();

            if ($suggestionParts->isEmpty()) {
                $this->logger->critical(
                    '[SuggestService] Received empty $suggestionParts.',
                    ['query' => json_encode($suggestQuery->getQuery()->wheres)]
                );
            }

            $suggestedPart = $suggestionStrategy->filterSelectedCompatibleParts($suggestionParts);
            $this->wizard->addPart($suggestedPart);
        }
    }
}