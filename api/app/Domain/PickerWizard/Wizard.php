<?php
declare(strict_types=1);

namespace App\Domain\PickerWizard;

use App\Domain\CompatibilityService\CompatibilityService;
use App\Domain\PcParts\PartsCollection;
use App\Domain\PcParts\PcPart;
use HansOtt\PSR7Cookies\RequestCookies;
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

        return $this;
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

    public function addPart(PcPart $part = null): void
    {
        $this->state->addPart($part);
    }

    public function keepState(ResponseInterface $response): ResponseInterface
    {
        return $this->state->prepare($response);
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