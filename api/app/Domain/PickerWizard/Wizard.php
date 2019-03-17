<?php
declare(strict_types=1);

namespace App\Domain\PickerWizard;

use App\Domain\CompatibilityService\CompatibilityService;
use App\Domain\PcParts\PartsCollection;
use App\Domain\PcParts\PcPart;

class Wizard
{
    /**@var CompatibilityService $compatibilityService */
    private $compatibilityService;
    /**@var PartsCollection $selectedParts */
    private $selectedParts;
    /**@var int $nextStageIdx */
    private $nextStageIdx;
    /**@var array $stagesConfig*/
    private $stagesConfig;

    public function __construct(CompatibilityService $compatibilityService, PartsCollection $selectedParts, int $nextStageIdx)
    {
        $this->selectedParts = $selectedParts;
        $this->nextStageIdx = $nextStageIdx;
        $this->stagesConfig = $this->readStagesConfig();
    }

    public function findCompatiblePartsForNextStage(): PartsCollection
    {
        $nextStagePart = $this->buildDummyPartFromStage($this->stagesConfig[$this->nextStageIdx]);

        return $this->compatibilityService->findCompatiblePartsForCollection($this->selectedParts, $nextStagePart);
    }

    private function readStagesConfig(): array
    {
        return json_decode(file_get_contents(__DIR__ . '/config.json'), true);
    }

    private function buildDummyPartFromStage(string $stageClassName): PcPart
    {
        $class = PcPart::ENTITIES_NAMESPACE . $stageClassName;

        return new $class;
    }
}