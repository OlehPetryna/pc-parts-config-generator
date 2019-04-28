<?php
declare(strict_types=1);

namespace App\Tools;

use App\Domain\PcParts\PcPart;

class ColumnListing
{
    private static $listingCache;

    public static function getSpecificationListing(PcPart $part): array
    {
        $key = $part->getClass();
        if (!isset(self::$listingCache[$key])) {
            $listing = array_keys($part->newQuery()->first()->getAttribute('specifications'));
            self::$listingCache[$key] = $listing;
        }

        return self::$listingCache[$key];
    }
}