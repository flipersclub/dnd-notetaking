<?php

namespace App\Enums\Compendium\Location;

use App\Enums\HasValues;
use Illuminate\Support\Str;

enum LocationService: int
{
    use HasValues;

    case GeneralGoods = 1;
    case Jewelry = 2;
    case Weaponry = 3;
    case Tailoring = 4;
    case HerbalRemedies = 5;
    case AlchemicalSupplies = 6;
    case BooksAndScrolls = 7;
    case ArtisanCrafts = 8;
    case GemstonesAndCrystals = 9;
    case PotionsAndElixirs = 10;
    case CuriosAndOddities = 11;
    case Glassworks = 12;
    case CandlesAndFragrances = 13;
    case Leathercraft = 14;
    case ClocksAndTimepieces = 15;
    case Toys = 16;
    case QuillsAndInk = 17;
    case Healing = 18;
    case Beauty = 19;
    case Divination = 20;
    case TattooAndBodyArt = 22;
    case ShrineAndBlessings = 23;
    case BathhouseAndRelaxation = 24;
    case MessageAndCommunication = 25;
    case FinancialExchange = 26;
    case FoodAndDrink = 27;
    case SpicesAndHerbs = 28;
    case Butchery = 29;
    case Tea = 30;
    case Coffee = 31;

    public function label(): string
    {
        return match ($this) {
            default => Str::headline($this->name)
        };
    }
}
