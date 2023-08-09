<?php

namespace App\Enums;

use Illuminate\Support\Str;

enum GovernmentType: int
{
    use HasValues;

    case Democracy = 1;
    case Autocracy = 2;
    case Plutocracy = 3;

    public function label(): string
    {
        return Str::headline($this->name);
    }
}
