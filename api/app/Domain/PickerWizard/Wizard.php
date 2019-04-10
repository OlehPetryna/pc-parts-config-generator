<?php
declare(strict_types=1);

namespace App\Domain\PickerWizard;

use App\Domain\CompatibilityService\CompatibilityService;
use App\Domain\PcParts\PartsCollection;
use App\Domain\PcParts\PcPart;
use HansOtt\PSR7Cookies\RequestCookies;
use Jenssegers\Mongodb\Eloquent\Builder;
use Psr\Http\Message\ResponseInterface;

class Wizard
{
    /**@var CompatibilityService $compatibilityService */
    private $compatibilityService;
    /**@var RequestCookies $requestCookies */
    private $requestCookies;

    /**@var Stage $stage*/
    private $stage;
    /**@var State $state*/
    private $state;

    public function __construct(CompatibilityService $compatibilityService, Stage $stage, RequestCookies $cookies)
    {
        $this->stage = $stage;
        $this->compatibilityService = $compatibilityService;
        $this->requestCookies = $cookies;
    }

    public function withStateParts(): self
    {
        $this->state = State::fromCookies($this->requestCookies, $this->stage);
        $this->refreshStage();
        return $this;
    }

    public function withParts(PartsCollection $selectedParts): self
    {
        $this->refreshState($selectedParts);

        return $this;
    }

    public function findCompatiblePartsQuery(): Builder
    {
        $nextStagePart = $this->stage->buildDummyPart();

        return $this->compatibilityService->findCompatiblePartsQueryForCollection($this->getStateParts(), $nextStagePart);
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

    public function addPart(PcPart $part = null): void
    {
        $this->state->addPart($part);
        $this->refreshStage();
    }

    public function keepState(ResponseInterface $response): ResponseInterface
    {
        return $this->state->prepare($response);
    }

    public function rewindOneStep(): void
    {
        $desirablePartsCount = $this->state->countParts() === 0 ? 0 : $this->state->countParts() - 1;

        $this->refreshStage($desirablePartsCount);
        $this->refreshState($this->getStateParts());
        $this->state->rewindOneStep();
    }

    public function getStateParts(): ?PartsCollection
    {
        return $this->state->getParts();
    }

    public function buildStagePart(): PcPart
    {
        return $this->stage->buildDummyPart();
    }

    public function endReached(): bool
    {
        return$this->stage->allStagesPassed();
    }

    private function refreshStage(int $newStageIdx = null): void
    {
        $this->stage = new Stage($newStageIdx ?? $this->state->countParts());
    }

    private function refreshState(PartsCollection $partsCollection): void
    {
        $this->state = new State($this->stage, $partsCollection);
    }
}