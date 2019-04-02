<?php
declare(strict_types=1);

namespace App\Domain\PickerWizard;


use App\Domain\PcParts\PartsCollection;
use App\Domain\PcParts\PcPart;
use HansOtt\PSR7Cookies\SetCookie;
use Psr\Http\Message\ResponseInterface;

class State
{
    private $stage;
    private $pickedParts;

    private $cookieName = 'wizardState';

    public function __construct(Stage $stage, PartsCollection $pickedParts = null)
    {
        $this->stage = $stage;
        $this->pickedParts = $pickedParts ?: new PartsCollection([]);
    }

    public function prepare(ResponseInterface $response): void
    {
        $cookie = SetCookie::thatExpires($this->cookieName, json_encode($this->compose()), (new \DateTimeImmutable())->modify('tomorrow'));
    }

    public function rewindOneStep(): void
    {

    }

    public function addPart(PcPart $part): void
    {
        $this->pickedParts->add($part);
    }

    public function getParts(): ?PartsCollection
    {
        return $this->pickedParts;
    }

    private function compose(): array
    {
        $data = [
            'parts' => [],
            'stage' => $this->stage->getIdx()
        ];
    }
}