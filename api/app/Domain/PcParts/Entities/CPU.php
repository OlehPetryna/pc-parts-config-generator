<?php
declare(strict_types=1);

namespace App\Domain\PcParts\Entities;

use App\Domain\PcParts\PcPart;

class CPU extends PcPart
{
    protected $collection = 'cpu';

    public function getMaximumSupportedMemory(): int
    {
        $specs = $this->getAttribute('specifications');
        $value = $specs && isset($specs['Maximum Supported Memory']) ? $this->getAttribute('specifications')['Maximum Supported Memory'] : null;

        return $value ? (int)$value['value'] : 0;
    }

    public function getPowerConsumption(): int
    {
        return (int)$this->getAttribute('specifications')['TDP']['value'];
    }
}