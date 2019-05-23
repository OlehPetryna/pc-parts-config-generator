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

        $default = $this->getDefaultRatio();

        $highPercentage = 20;
        $lowPercentage = 10;

        $default[PowerSupply::class] = $lowPercentage;
        $default[PcCase::class] = $lowPercentage;

        if ($this->suggestionCategories->getGraphicsPriority()->isHighest()) {
            $default[VideoCard::class] = $highPercentage;
        }

        if ($this->suggestionCategories->getCPUPriority()->isHighest()) {
            $default[CPU::class] = $highPercentage;
        }

        if ($this->suggestionCategories->getMemoryPriority()->isHighest()) {
            $default[RAM::class] = $highPercentage;
        }

        $this->ratio = $default;

        return $this->ratio;
    }

    private function getDefaultRatio(): array
    {
        $partsAmount = 7;
        $defaultPerPart = 100  / $partsAmount;

        return [
            Motherboard::class => $defaultPerPart,
            CPU::class => $defaultPerPart,
            VideoCard::class=> $defaultPerPart,
            RAM::class=> $defaultPerPart,
            Storage::class => $defaultPerPart,
            PowerSupply::class => $defaultPerPart,
            PcCase::class => $defaultPerPart,
        ];
    }
}