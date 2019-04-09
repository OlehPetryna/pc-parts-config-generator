<?php
declare(strict_types=1);

namespace App\Domain\CompatibilityService\Strategies;


use App\Domain\CompatibilityService\PartsCompatibilityStrategy;
use App\Domain\PcParts\PartsCollection;
use App\Domain\PcParts\PcPart;

abstract class AbstractStrategy implements PartsCompatibilityStrategy
{
    /**@var PcPart $findingCompatibilityForPart*/
    protected $findingCompatibilityForPart;
    /**@var PartsCollection $compatibilityContextCollection*/
    protected $compatibilityContextCollection;

    public function __construct(PcPart $findingCompatibilityForPart, PartsCollection $compatibilityContextCollection)
    {
        $this->findingCompatibilityForPart = $findingCompatibilityForPart;
        $this->compatibilityContextCollection = $compatibilityContextCollection;
    }
}