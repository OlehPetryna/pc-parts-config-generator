<?php
declare(strict_types=1);

namespace App\Domain\PcParts;

use App\Domain\PcParts\Entities\Specification;
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


    public function addTranslationToSpecs(): void
    {
        $specifications = $this->getAttribute('specifications') ?? [];

        foreach ($specifications as $key => $specification) {
            $specifications[$key]['translation'] = Specification::translateFor($key);
        }

        $this->setAttribute('specifications', $specifications);
    }

    public function getPowerConsumption(): int
    {
        return 0;
    }

    public function getLargeImg(): ?string
    {
        $img = $this->img ?? '';

        $imgHasDimension = preg_match('/\.\d+p\.(?>jpg|png)$/', $img);
        if ($imgHasDimension) {
            return str_replace('256p', '1600', $img);
        }

        return null;
    }

    public function jsonSerialize(): array
    {
        $this->addTranslationToSpecs();
        return array_replace(parent::jsonSerialize(), [
            'largeImg' => $this->getLargeImg(),
        ]);
    }
}