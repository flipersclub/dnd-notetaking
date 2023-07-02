<?php

namespace App\Enums;

trait HasValues
{
    public static function values(): array
    {
        return array_map(function ($case) {
            return $case->value;
        }, self::cases());
    }
}
