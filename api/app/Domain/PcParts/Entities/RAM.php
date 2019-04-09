<?php
declare(strict_types=1);

namespace App\Domain\PcParts\Entities;


use App\Domain\PcParts\PcPart;

class RAM extends PcPart
{
    public function getSize(): ?int
    {
        $rawSize = $this->getAttribute('specifications')['Size']['value'];

        if (!$rawSize) {
            return null;
        }

        preg_match('/^(?<size>\d+) GB/', $rawSize, $matches);

        return (int)$matches['size'];
    }
}