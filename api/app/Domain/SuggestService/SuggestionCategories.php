<?php
declare(strict_types=1);

namespace App\Domain\SuggestService;

class SuggestionCategories
{
    public static $availableCategories = [
        'gaming',
        'multimedia',
        'light-usage',
        'graphics',
        'cpu-intensive',
    ];

    private $ratedCategories;

    public function __construct(array $categories)
    {
        $this->ratedCategories = $categories;
    }

    public function getMotherboardPriority(): SuggestionPriority
    {
        $shouldReturnHighestPriority = $this->atLeastOneCategoryRatedHigh(['gaming', 'cpu-intensive', 'graphics']);
        $shouldReturnMidPriority = $this->atLeastOneCategoryRatedMedium(['gaming', 'cpu-intensive', 'graphics']);

        if ($shouldReturnHighestPriority) {
            return SuggestionPriority::highest();
        }

        if ($shouldReturnMidPriority) {
            return SuggestionPriority::medium();
        }

        return SuggestionPriority::lowest();
    }

    public function getCPUPriority(): SuggestionPriority
    {
        $shouldReturnHighestPriority = $this->atLeastOneCategoryRatedHigh(['gaming', 'cpu-intensive']);
        $shouldReturnMidPriority = $this->atLeastOneCategoryRatedMedium(['gaming', 'cpu-intensive']) || $this->isCategoryRatedHigh('multimedia');

        $isProfessionalPurpose = $this->isCategoryRatedHigh('cpu-intensive') || $this->isCategoryRatedMedium('cpu-intensive');

        if ($shouldReturnHighestPriority) {
            return SuggestionPriority::highest($isProfessionalPurpose);
        }

        if ($shouldReturnMidPriority) {
            return SuggestionPriority::medium($isProfessionalPurpose);
        }

        return SuggestionPriority::lowest();
    }

    public function getGraphicsPriority(): SuggestionPriority
    {
        $shouldReturnHighestPriority = $this->atLeastOneCategoryRatedHigh(['gaming', 'graphics']);
        $shouldReturnMidPriority = $this->atLeastOneCategoryRatedMedium(['gaming', 'graphics']) || $this->isCategoryRatedHigh('multimedia');

        $isProfessionalPurpose = $this->isCategoryRatedHigh('graphics') || $this->isCategoryRatedMedium('graphics');

        if ($shouldReturnHighestPriority) {
            return SuggestionPriority::highest($isProfessionalPurpose);
        }

        if ($shouldReturnMidPriority) {
            return SuggestionPriority::medium($isProfessionalPurpose);
        }

        return SuggestionPriority::lowest();
    }

    public function getMemoryPriority(): SuggestionPriority
    {
        $shouldReturnHighestPriority = $this->atLeastOneCategoryRatedHigh(['gaming', 'graphics', 'cpu-intensive']);
        $shouldReturnMidPriority = $this->atLeastOneCategoryRatedMedium(['gaming', 'graphics', 'cpu-intensive']);

        if ($shouldReturnHighestPriority) {
            return SuggestionPriority::highest();
        }

        if ($shouldReturnMidPriority) {
            return SuggestionPriority::medium();
        }

        return SuggestionPriority::lowest();
    }

    private function atLeastOneCategoryRatedHigh(array $categories): bool
    {
        return $this->atLeastOneCategoryRated($categories, function ($category) {
            return $this->isCategoryRatedHigh($category);
        });
    }

    private function atLeastOneCategoryRatedMedium(array $categories): bool
    {
        return $this->atLeastOneCategoryRated($categories, function ($category) {
            return $this->isCategoryRatedMedium($category);
        });
    }

    private function atLeastOneCategoryRatedLow(array $categories): bool
    {
        return $this->atLeastOneCategoryRated($categories, function ($category) {
            return $this->isCategoryRatedLow($category);
        });
    }

    private function atLeastOneCategoryRated(array $categories, callable $ratingFn): bool
    {
        foreach ($categories as $category) {
            if ($ratingFn($category)) {
                return true;
            }
        }

        return false;
    }

    private function isCategoryRatedHigh(string $category): bool
    {
        return $this->getCategoryPriority($category) === 5;
    }

    private function isCategoryRatedMedium(string $category): bool
    {
        return $this->getCategoryPriority($category) >= 3 && $this->getCategoryPriority($category) <= 4;
    }

    private function isCategoryRatedLow(string $category): bool
    {
        return $this->getCategoryPriority($category) <= 2;
    }

    private function getCategoryPriority(string $category): int
    {
        return (int)$this->ratedCategories[$category];
    }


}