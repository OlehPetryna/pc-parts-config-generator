<?php
declare(strict_types=1);

namespace App\Domain\PcParts\Entities;

use App\Domain\PcParts\PcPart;

class CPU extends PcPart
{
    protected $collection = 'cpu';
}