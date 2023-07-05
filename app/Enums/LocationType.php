<?php

namespace App\Enums;

use Illuminate\Support\Str;

enum LocationType: string
{
    use HasValues;

    case World = 'world';
    case Region = 'region';
    case City = 'city';
    case Town = 'town';
    case Village = 'village';
    case Neighbourhood = 'neighbourhood';
    case Establishment = 'establishment';
    case Forest = 'forest';
    case Mountain = 'mountain';
    case Cave = 'cave';
    case Island = 'island';
    case Ruins = 'ruins';
    case Castle = 'castle';
    case Fortress = 'fortress';
    case Temple = 'temple';
    case Shrine = 'shrine';
    case Tower = 'tower';
    case Farm = 'farm';
    case Harbor = 'harbor';
    case Market = 'market';
    case Tavern = 'tavern';
    case Inn = 'inn';
    case Library = 'library';
    case School = 'school';
    case MageTower = 'mage_tower';
    case Cemetery = 'cemetery';
    case Graveyard = 'graveyard';
    case Swamp = 'swamp';
    case Desert = 'desert';
    case Oasis = 'oasis';
    case UnderwaterCity = 'underwater_city';
    case AstralPlane = 'astral_plane';
    case ElementalPlane = 'elemental_plane';
    case AbyssalRealm = 'abyssal_realm';
    case CelestialRealm = 'celestial_realm';

    public function label(): string
    {
        return Str::headline($this->name);
    }
}
