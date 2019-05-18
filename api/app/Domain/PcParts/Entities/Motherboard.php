<?php
declare(strict_types=1);

namespace App\Domain\PcParts\Entities;


use App\Domain\PcParts\PcPart;

class Motherboard extends PcPart
{
    public function getAvailableMemoryTypes(): string
    {
        return $this->getAttribute('specifications')['Memory Type']['value'];
    }

    public function getMaximumSupportedMemory(): int
    {
        return (int)$this->getAttribute('specifications')['Max RAM']['value'];
    }

    public function getAvailableStorageTypes(): array
    {
        $types = [];

        foreach (['M.2', 'SATA 6 GB/s'] as $type) {
            if ($this->getSlotsAmount($type)) {
                $types[] = $type;
            }
        }

        return $types;
    }

    public function getSlotsAmount(string $slotName): int
    {
        if ($slotName === 'M.2' || $slotName === 'M_2') {
            return (int)$this->getAttribute('specifications')['M_2 Ports']['value'];
        }

        if (strtolower($slotName)=== 'sata 6gb/s' || strtolower($slotName) === 'sata 6 gb/s') {
            return (int)$this->getAttribute('specifications')['SATA 6Gb/s Ports']['value'];
        }


        return 0;
    }
}