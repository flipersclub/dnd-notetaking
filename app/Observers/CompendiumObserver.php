<?php

namespace App\Observers;

use App\Models\Compendium\Compendium;
use Spatie\Permission\Models\Permission;

class CompendiumObserver
{
    public function created(Compendium $compendium)
    {
        Permission::findOrCreate("compendia.view.{$compendium->id}");
        Permission::findOrCreate("compendia.update.{$compendium->id}");
        Permission::findOrCreate("compendia.delete.{$compendium->id}");
    }
}
