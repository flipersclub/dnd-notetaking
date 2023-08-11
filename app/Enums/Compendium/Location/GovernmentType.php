<?php

namespace App\Enums\Compendium\Location;

use App\Enums\HasValues;
use Illuminate\Support\Str;

enum GovernmentType: int
{
    use HasValues;

    case Monarchy = 1;
    case Oligarchy = 2;
    case Theocracy = 3;
    case Democracy = 4;
    case Autocracy = 5;
    case Anarchy = 6;
    case Dictatorship = 7;
    case Feudalism = 8;
    case TribalCouncil = 9;
    case Magocracy = 10;
    case Meritocracy = 11;
    case Confederacy = 12;
    case TribalFederation = 13;
    case Plutocracy = 14;
    case Matriarchy = 15;
    case Patriarchy = 16;
    case Technocracy = 17;
    case Syndicate = 18;
    case Guildocracy = 19;
    case Commune = 20;
    case Utopia = 21;
    case Totalitarianism = 22;

    public function label(): string
    {
        return Str::headline($this->name);
    }
}
