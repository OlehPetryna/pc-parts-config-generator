<?php
declare(strict_types=1);

namespace App\Domain\PcParts\Entities;


use App\Domain\PcParts\PcPart;

class Motherboard extends PcPart
{
    public function getAvailableMemoryTypes(): array
    {
        $rawTypes = $this->getAttribute('specifications')['Memory Type']['value'];
        $types = explode(' / ', $rawTypes);
        $typePrefix = preg_filter('/-\d+$/', '', $types[0]);

        $result = [array_shift($types)];
        foreach ($types as $type) {
            $result[] = $typePrefix . '-' . $type;
        }

        return $result;
    }

    public function getMaximumSupportedMemory(): int
    {
        return (int)$this->getAttribute('specifications')['Maximum Supported Memory']['value'];
    }
}