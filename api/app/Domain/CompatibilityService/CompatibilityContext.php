<?php
declare(strict_types=1);

namespace App\Domain\CompatibilityService;


use App\Domain\CompatibilityService\Strategies\MemoryStrategy;
use App\Domain\CompatibilityService\Strategies\NullStrategy;
use App\Domain\CompatibilityService\Strategies\SocketStrategy;
use App\Domain\PcParts\Entities\CPU;
use App\Domain\PcParts\Entities\RAM;
use App\Domain\PcParts\Entities\VideoCard;
use App\Domain\PcParts\PartsCollection;
use App\Domain\PcParts\PcPart;

class CompatibilityContext
{
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

        return new NullStrategy($findingCompatibilityForPart, $wholeCollection);
    }
    //null object, strategies could be created from both should & findingd parts
}