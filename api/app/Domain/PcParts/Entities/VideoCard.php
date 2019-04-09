<?php
declare(strict_types=1);

namespace App\Domain\PcParts\Entities;


use App\Domain\PcParts\PcPart;

class VideoCard extends PcPart
{
    public function getPowerConsumption(): int
    {
        return (int)$this->getAttribute('specifications')['TDP']['value'];
    }
}