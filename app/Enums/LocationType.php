<?php

namespace App\Enums;

use Illuminate\Support\Str;

enum LocationType: int
{
    use HasValues;

    case World = 1;
    case Region = 2;
    case City = 3;
    case Town = 4;
    case Village = 5;
    case Neighbourhood = 6;
    case Establishment = 7;
    case Forest = 8;
    case Mountain = 9;
    case Cave = 10;
    case Island = 11;
    case Ruins = 12;
    case Castle = 13;
    case Fortress = 14;
    case Temple = 15;
    case Shrine = 16;
    case Tower = 17;
    case Farm = 18;
    case Harbor = 19;
    case Market = 20;
    case Tavern = 21;
    case Inn = 22;
    case Library = 23;
    case School = 24;
    case MageTower = 25;
    case Cemetery = 26;
    case Graveyard = 27;
    case Swamp = 28;
    case Desert = 29;
    case Oasis = 30;
    case UnderwaterCity = 31;
    case AstralPlane = 32;
    case ElementalPlane = 33;
    case AbyssalRealm = 34;
    case CelestialRealm = 35;

    public function label(): string
    {
        return Str::headline($this->name);
    }
}
