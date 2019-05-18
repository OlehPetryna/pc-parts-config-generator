<?php
declare(strict_types=1);

namespace App\Domain\PcParts\Entities;


use App\Domain\PcParts\PcPart;

class RAM extends PcPart
{
    public function getSize(): ?int
    {
        $rawSize = $this->getAttribute('specifications')['Modules']['value'];

        if (!$rawSize) {
            return null;
        }

        preg_match('/^(?<qty>\d{1}) x (?<size>\d+)/', $rawSize, $matches);

        return (int)$matches['size'] * (int)$matches['qty'];
    }
}