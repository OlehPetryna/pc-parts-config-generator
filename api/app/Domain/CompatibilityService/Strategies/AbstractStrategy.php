<?php
declare(strict_types=1);

namespace App\Domain\CompatibilityService\Strategies;


use App\Domain\CompatibilityService\PartsCompatibilityStrategy;
use App\Domain\PcParts\PartsCollection;
use App\Domain\PcParts\PcPart;

abstract class AbstractStrategy implements PartsCompatibilityStrategy
{
    /**@var PcPart $shouldBeCompatibleWithPart*/
    protected $shouldBeCompatibleWithPart;
    /**@var PartsCollection $compatibilityContextCollection*/
    protected $compatibilityContextCollection;

    public function __construct(PcPart $shouldBeCompatibleWithPart, PartsCollection $compatibilityContextCollection)
    {
        $this->shouldBeCompatibleWithPart = $shouldBeCompatibleWithPart;
    }
}