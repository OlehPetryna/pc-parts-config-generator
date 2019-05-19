<?php
declare(strict_types=1);

namespace App\Domain\CompatibilityService;


use App\Domain\CompatibilityService\Strategies\CaseStrategy;
use App\Domain\CompatibilityService\Strategies\MemoryStrategy;
use App\Domain\CompatibilityService\Strategies\NullStrategy;
use App\Domain\CompatibilityService\Strategies\PowerSupplyStrategy;
use App\Domain\CompatibilityService\Strategies\SocketStrategy;
use App\Domain\CompatibilityService\Strategies\StorageStrategy;
use App\Domain\PcParts\Entities\CPU;
use App\Domain\PcParts\Entities\PcCase;
use App\Domain\PcParts\Entities\PowerSupply;
use App\Domain\PcParts\Entities\RAM;
use App\Domain\PcParts\Entities\Storage;
use App\Domain\PcParts\Entities\VideoCard;
use App\Domain\PcParts\PartsCollection;
use App\Domain\PcParts\PcPart;
use HansOtt\PSR7Cookies\RequestCookies;
use HansOtt\PSR7Cookies\ResponseCookies;

class CompatibilityContext
{
    private $requestCookies;

    const SHOW_WITHOUT_PRICE_COOKE = 'showWithoutPrice';

    public function __construct(RequestCookies $cookies)
    {
        $this->requestCookies = $cookies;
    }

    public function pickCompatibilityStrategy(
        PcPart $findingCompatibilityForPart,
        PartsCollection $wholeCollection
    ): PartsCompatibilityStrategy
    {

        if ($findingCompatibilityForPart instanceof CPU) {
            return new SocketStrategy($findingCompatibilityForPart, $wholeCollection);
        }

        if ($findingCompatibilityForPart instanceof RAM) {
            return new MemoryStrategy($findingCompatibilityForPart, $wholeCollection);
        }

        if ($findingCompatibilityForPart instanceof Storage) {
            return new StorageStrategy($findingCompatibilityForPart, $wholeCollection);
        }

        if ($findingCompatibilityForPart instanceof PowerSupply) {
            return new PowerSupplyStrategy($findingCompatibilityForPart, $wholeCollection);
        }

        if ($findingCompatibilityForPart instanceof PcCase) {
            return new CaseStrategy($findingCompatibilityForPart, $wholeCollection);
        }

        return new NullStrategy($findingCompatibilityForPart, $wholeCollection);
    }

    public function arePartsWithoutPricesDesired(): bool
    {
        return $this->requestCookies->has(self::SHOW_WITHOUT_PRICE_COOKE) && $this->requestCookies->get(self::SHOW_WITHOUT_PRICE_COOKE)->getValue();
    }
}