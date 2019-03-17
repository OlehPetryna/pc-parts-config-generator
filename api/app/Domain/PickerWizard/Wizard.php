<?php
declare(strict_types=1);

namespace App\Domain\PickerWizard;

use App\Domain\CompatibilityService\CompatibilityService;
use App\Domain\PcParts\PartsCollection;

class Wizard
{
    /**@var CompatibilityService $compatibilityService */
    private $compatibilityService;
    /**@var PartsCollection $selectedParts */
    private $selectedParts;
    /**@var int $nextStageIdx */
    private $nextStageIdx;

    /**@var Stage $stage*/
    private $stage;

    public function __construct(CompatibilityService $compatibilityService, PartsCollection $selectedParts, int $nextStageIdx)
    {
        $this->selectedParts = $selectedParts;
        $this->nextStageIdx = $nextStageIdx;

        $this->stage = new Stage();
    }

    public function findCompatiblePartsForNextStage(): PartsCollection
    {
        $nextStagePart = $this->stage->buildDummyPart($this->nextStageIdx);

        return $this->compatibilityService->findCompatiblePartsForCollection($this->selectedParts, $nextStagePart);
    }
}