<?php
declare(strict_types=1);

namespace App\Domain\PickerWizard;

use App\Domain\CompatibilityService\CompatibilityService;
use App\Domain\PcParts\PartsCollection;
use App\Domain\PcParts\PcPart;
use Psr\Http\Message\ResponseInterface;

class Wizard
{
    /**@var CompatibilityService $compatibilityService */
    private $compatibilityService;

    /**@var Stage $stage*/
    private $stage;
    /**@var State $state*/
    private $state;

    public function __construct(CompatibilityService $compatibilityService, Stage $stage)
    {
        $this->stage = $stage;
    }

    public function withStateParts(): self
    {
        return $this->withParts($this->getStateParts());
    }

    public function withParts(PartsCollection $selectedParts): self
    {
        $this->state = new State($this->stage, $selectedParts);

        return $this;
    }

    public function findCompatibleParts(): PartsCollection
    {
        $nextStagePart = $this->stage->buildDummyPart();

        return $this->compatibilityService->findCompatiblePartsForCollection($this->getStateParts(), $nextStagePart);
    }

    public function getCurrentStepName(): string
    {
        return $this->stage->getName();
    }

    public function getCurrentStepIdx(): int
    {
        return $this->stage->getIdx();
    }

    public function getStepsCount(): int
    {
        return $this->stage->getStagesCount();
    }

    public function addPart(PcPart $part): void
    {

    }

    public function keepState(ResponseInterface $response): void
    {
        $this->state->prepare($response);
    }

    public function getStateParts(): ?PartsCollection
    {
        return $this->state->getParts();
    }

    public function buildStagePart(): PcPart
    {
        return $this->stage->buildDummyPart();
    }
}