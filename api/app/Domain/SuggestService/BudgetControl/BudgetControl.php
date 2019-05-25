<?php
declare(strict_types=1);

namespace App\Domain\SuggestService\BudgetControl;

use App\Domain\PcParts\Entities\CPU;
use App\Domain\PcParts\Entities\Motherboard;
use App\Domain\PcParts\Entities\PcCase;
use App\Domain\PcParts\Entities\PowerSupply;
use App\Domain\PcParts\Entities\RAM;
use App\Domain\PcParts\Entities\Storage;
use App\Domain\PcParts\Entities\VideoCard;
use App\Domain\PcParts\PcPart;
use App\Domain\SuggestService\SuggestionCategories;
use Jenssegers\Mongodb\Eloquent\Builder;

class BudgetControl
{
    private $suggestionCategories;
    /**@var int|float $budget*/
    private $budget;

    public function __construct(SuggestionCategories $suggestionCategories, $budget)
    {
        $this->budget = $budget;
        $this->suggestionCategories = $suggestionCategories;
    }

    public function addPriceConstraint(Builder $query): void
    {
        /**@var PcPart $model*/
        $model = $query->getModel();

        $query->where('priceNumber', '<=', $this->getUpperPriceLimit($model->getClass()));
    }

    private function getUpperPriceLimit(string $key): float
    {
        $ratio = $this->calcRatios();
        $percentage = $ratio[$key];

        return $this->budget * ($percentage/100);
    }

    private $ratio;
    private function calcRatios(): array
    {
        if (isset($this->ratio)) {
            return $this->ratio;
        }

        $percentagesDistribution = $this->getPercentageDistribution();

        $coefs = [
            'highest' => count($percentagesDistribution['highest']),
            'high' => count($percentagesDistribution['high']),
            'med' => count($percentagesDistribution['med']),
            'low' => count($percentagesDistribution['low'])
        ];

        $percentagesValues = ['highest' => 25, 'high' => 18, 'med' => 12, 'low' => 5];

        while (!$this->percentagesSumIsHundred($coefs, $percentagesValues)) {
            $percentagesValues['highest']--;
            $percentagesValues['high']--;
        }

        $this->ratio = $this->fillRatioFromDistributionValues($percentagesDistribution, $percentagesValues);

        return $this->ratio;
    }

    private function percentagesSumIsHundred(array $coefs, array $percentageValues): bool
    {
        $sum = 0;
        foreach ($coefs as $type => $value) {
            $sum += $value * $percentageValues[$type];
        }

        return $sum <= 100;
    }

    private function getPercentageDistribution(): array
    {
        $percentagesDistribution = ['highest' => [], 'high' => [], 'med' => [], 'low' => []];

        $percentagesDistribution['low'][] = PowerSupply::class;
        $percentagesDistribution['low'][] = PcCase::class;

        if ($this->suggestionCategories->getMotherboardPriority()->isHighest()) {
            $percentagesDistribution['high'][] = Motherboard::class;
        } else {
            $percentagesDistribution['med'][] = Motherboard::class;
        }

        if ($this->suggestionCategories->getGraphicsPriority()->isHighest()) {
            $percentagesDistribution['highest'][] = VideoCard::class;
        } else {
            $percentagesDistribution['high'][] = VideoCard::class;
        }

        if ($this->suggestionCategories->getCPUPriority()->isHighest()) {
            $percentagesDistribution['highest'][] = CPU::class;
        } else {
            $percentagesDistribution['high'][] = CPU::class;
        }

        if ($this->suggestionCategories->getMemoryPriority()->isHighest()) {
            $percentagesDistribution['high'][] = RAM::class;
        } else {
            $percentagesDistribution['med'][] = RAM::class;
        }

        if ($this->suggestionCategories->getStoragePriority()->isHighest()) {
            $percentagesDistribution['high'][] = Storage::class;
        } else {
            $percentagesDistribution['med'][] = Storage::class;
        }


        return $percentagesDistribution;
    }

    private function fillRatioFromDistributionValues(array $distribution, array $values): array
    {
        $ratio = [];

        foreach ($distribution as $type => $classes) {
            foreach ($classes as $class) {
                $ratio[$class] = $values[$type];
            }
        }

        return $ratio;
    }
}