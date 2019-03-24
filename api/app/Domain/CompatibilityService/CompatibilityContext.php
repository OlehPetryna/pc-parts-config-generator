<?php
declare(strict_types=1);

namespace App\Domain\CompatibilityService;


use App\Domain\CompatibilityService\Strategies\NullStrategy;
use App\Domain\PcParts\PartsCollection;
use App\Domain\PcParts\PcPart;

class CompatibilityContext
{
    public function pickCompatibilityStrategy(
        PcPart $shouldBeCompatibleWithPart,
        PcPart $findingCompatibilityForPart,
        PartsCollection $wholeCollection
    ): PartsCompatibilityStrategy
    {


        return new NullStrategy($shouldBeCompatibleWithPart, $wholeCollection);
    }
    //null object, strategies could be created from both should & findingd parts
}