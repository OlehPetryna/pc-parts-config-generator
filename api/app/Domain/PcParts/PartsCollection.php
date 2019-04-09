<?php
declare(strict_types=1);

namespace App\Domain\PcParts;


use Illuminate\Database\Eloquent\Collection;

class PartsCollection extends Collection
{
    /**@var PcPart[] $collection*/
    protected $items;

    public static function fromIdsMap(array $map): self
    {
        $items = [];
        foreach ($map as $class => $id) {
            $items[] = $class::find($id);
        }

        return new self($items);
    }

    public function asArray(): array
    {
        return $this->items;
    }

    public function buildIdsMap(): array
    {
        $map = [];
        foreach ($this->items as $item) {
            $map[$item->getClass()] = $item->getKey();
        }

        return $map;
    }

    public function toArray(): array
    {
        return $this->jsonSerialize();
    }
}