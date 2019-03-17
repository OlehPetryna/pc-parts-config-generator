<?php
declare(strict_types=1);

namespace App\Domain\PcParts;

use Illuminate\Database\Eloquent\Model;

abstract class PcPart extends Model
{
    const ENTITIES_NAMESPACE = __NAMESPACE__ . '\\Entities\\';
}