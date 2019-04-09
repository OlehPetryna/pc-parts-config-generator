<?php
declare(strict_types=1);

namespace App\Domain\PcParts\Entities;

use App\Domain\PcParts\PcPart;

class CPU extends PcPart
{
    protected $collection = 'cpu';

    public function getMaximumSupportedMemory(): int
    {
        return (int)$this->getAttribute('specifications')['Maximum Supported Memory']['value'];
    }

    public function getPowerConsumption(): int
    {
        return (int)$this->getAttribute('specifications')['Thermal Design Power']['value'];
    }
}