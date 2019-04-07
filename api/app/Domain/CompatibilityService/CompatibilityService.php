<?php
declare(strict_types=1);

namespace App\Domain\CompatibilityService;

use App\Domain\PcParts\PartsCollection;
use App\Domain\PcParts\PcPart;
use Jenssegers\Mongodb\Eloquent\Builder;

class CompatibilityService
{
    /**@var CompatibilityContext $context*/
    private $context;

    public function __construct(CompatibilityContext $context)
    {
        $this->context = $context;
    }

    public function findCompatiblePartsQueryForCollection(PartsCollection $partsCollection, PcPart $partOfNeededType): Builder
    {
        $query = $partOfNeededType->newQuery();
        foreach ($partsCollection as $item) {
            $strategy = $this->context->pickCompatibilityStrategy($item, $partOfNeededType, $partsCollection);
            $strategy->addAcceptanceCriteria($query);
        }

        return $query;
    }

    public function findCompatiblePartsForCollection(PartsCollection $partsCollection, PcPart $partOfNeededType): PartsCollection
    {
        return new PartsCollection($this->findCompatiblePartsQueryForCollection($partsCollection, $partOfNeededType)->get());
    }
}
