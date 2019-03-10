<?php
declare(strict_types=1);

namespace App\Domain\CompatibilityService;


use App\Domain\PcParts\PartsCollection;
use App\Domain\PcParts\PcPart;

class CompatibilityService
{
    /**@var CompatibilityContext $context*/
    private $context;

    public function __construct(CompatibilityContext $context)
    {
        $this->context = $context;
    }

    public function findCompatiblePartsForCollection(PartsCollection $partsCollection, PcPart $partOfNeededType): PartsCollection
    {
        $query = $partOfNeededType->newQuery();
        foreach ($partsCollection as $item) {
            $strategy = $this->context->pickCompatibilityStrategy($item, $partOfNeededType, $partsCollection);
            $strategy->addAcceptanceCriteria($query);
        }

        return new PartsCollection($query->get());
    }
}
