<?php

namespace App\Enums;

enum ImageType: int
{
    use HasValues;

    case cover = 1;
}
