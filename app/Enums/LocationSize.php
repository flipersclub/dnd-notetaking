<?php

namespace App\Enums;

use Illuminate\Support\Str;

enum LocationSize: int
{
    use HasValues;

    case City = 1;
    case Town = 2;
    case Village = 3;

    public function label(): string
    {
        return Str::headline($this->name);
    }
}
