<?php

namespace App\Enums\Compendium\Location;

use App\Enums\HasValues;
use Illuminate\Support\Str;

enum LocationType: int
{
    use HasValues;

    case Plane = 1;
    case World = 2;
    case Region = 3;
    case Hamlet = 4;
    case Village = 5;
    case Town = 6;
    case City = 7;
    case SettlementOther = 8;
    case Neighbourhood = 9;
    case Establishment = 10;
    case Mountain = 11;
    case Cave = 12;
    case Island = 13;
    case Ruins = 14;
    case Castle = 15;
    case Fortress = 16;
    case Temple = 17;
    case Shrine = 18;
    case Tower = 19;
    case Farm = 20;
    case Harbor = 21;
    case Market = 22;
    case Tavern = 23;
    case Inn = 24;
    case Library = 25;
    case School = 26;
    case MageTower = 27;
    case CemeteryGraveyard = 28;
    case Swamp = 29;
    case Desert = 30;
    case Oasis = 31;
    case Forest = 32;
    case Bathhouse = 33;
    case Bank = 34;
    case Arena = 35;
    case Theatre = 36;

    public function label(): string
    {
        return Str::headline($this->name);
    }
}
