<?php

namespace App\Enums;

enum CampaignVisibility: int
{
    use HasValues;

    case public = 1;
    case private = 2;
}
