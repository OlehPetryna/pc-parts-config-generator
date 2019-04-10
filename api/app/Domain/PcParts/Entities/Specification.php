<?php
declare(strict_types=1);

namespace App\Domain\PcParts\Entities;

use Jenssegers\Mongodb\Eloquent\Model;

class Specification extends Model
{
    public static function translateFor(string $key): string
    {
        $spec = static::findSpec($key);
        return $spec ? $spec->translate() : $key;
    }

    private static $specs = [];
    private static function findSpec(string $key): ?self
    {
        if (!isset(static::$specs[$key])) {
            static::$specs[$key] = static::query()->where('key', '=', $key)->first();
        }

        return static::$specs[$key];
    }

    public function translate(): string
    {
        return $this->translation;
    }

}