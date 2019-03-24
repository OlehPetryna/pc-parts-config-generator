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
        $namespace = PcPart::ENTITIES_NAMESPACE;
        foreach ($map as $class => $id) {
            $className = "{$namespace}{$class}";
            $items[] = $className::find($id);
        }

        return new self($items);
    }


    public function asArray(): array
    {
        return $this->items;
    }
}