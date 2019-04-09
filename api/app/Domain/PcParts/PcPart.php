<?php
declare(strict_types=1);

namespace App\Domain\PcParts;

use Jenssegers\Mongodb\Eloquent\Builder;
use Jenssegers\Mongodb\Eloquent\Model;

abstract class PcPart extends Model
{
    const ENTITIES_NAMESPACE = __NAMESPACE__ . '\\Entities\\';

    protected $connection = 'mongodb';

    public function newCollection(array $models = [])
    {
        return new PartsCollection($models);
    }

    public function getClass(): string
    {
        return static::class;
    }

    public function newQuery(): Builder
    {
        return parent::newQuery();
    }

    public function getPowerConsumption(): int
    {
        return 0;
    }
}