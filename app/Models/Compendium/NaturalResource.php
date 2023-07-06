<?php

namespace App\Models\Compendium;

use App\Models\HasTags;
use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class NaturalResource extends Model
{
    use HasFactory, HasTags;
}
