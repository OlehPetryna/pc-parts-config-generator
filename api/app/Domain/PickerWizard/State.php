<?php
declare(strict_types=1);

namespace App\Domain\PickerWizard;


use App\Domain\PcParts\PartsCollection;
use App\Domain\PcParts\PcPart;
use HansOtt\PSR7Cookies\RequestCookies;
use HansOtt\PSR7Cookies\SetCookie;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;

class State
{
    private $stage;
    private $pickedParts;

    private static $cookieName = 'wizardState';

    public function __construct(Stage $stage, PartsCollection $pickedParts = null)
    {
        $this->stage = $stage;
        $this->pickedParts = $pickedParts ?: new PartsCollection([]);
    }

    public static function fromCookies(RequestCookies $cookies, Stage $stage): self
    {
        $state = $cookies->has(self::$cookieName) ? $cookies->get(self::$cookieName) : null;
        if ($state) {
            $state = json_decode($state->getValue(), true);
            return new self($stage, PartsCollection::fromIdsMap($state['parts']));
        } else {
            return new self($stage);
        }
    }

    public function prepare(ResponseInterface $response): ResponseInterface
    {
        $cookie = SetCookie::thatExpires(self::$cookieName, json_encode($this->compose()), (new \DateTimeImmutable())->modify('tomorrow'));

        return $cookie->addToResponse($response);
    }

    public function rewindOneStep(): void
    {
        $removePartClass = $this->stage->buildDummyPart()->getClass();
        $this->pickedParts = $this->pickedParts->filter(function (PcPart $part) use ($removePartClass) {
            return $part->getClass() !== $removePartClass;
        });
    }

    public function addPart(PcPart $part = null): void
    {
        if ($part) {
            $this->pickedParts->add($part);
        }
    }

    public function getParts(): ?PartsCollection
    {
        return $this->pickedParts;
    }

    public function countParts(): int
    {
        return $this->pickedParts->count();
    }

    private function compose(): array
    {
        return [
            'parts' => $this->pickedParts->buildIdsMap(),
            'stage' => $this->stage->getIdx()
        ];
    }
}