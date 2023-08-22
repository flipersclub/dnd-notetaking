<?php

namespace App\Policies\Compendium;

use App\Models\Compendium\Faction;
use App\Models\User;

class FactionPolicy
{
    /**
     * Determine whether the user can view any models.
     */
    public function viewAny(User $user): bool
    {
        return true;
    }

    /**
     * Determine whether the user can view the model.
     */
    public function view(User $user, Faction $faction): bool
    {
        return $user->is($faction->compendium->creator)
            || $user->can("factions.view.$faction->id");
    }

    /**
     * Determine whether the user can create models.
     */
    public function create(User $user): bool
    {
        return $user->compendia()->exists();
    }

    /**
     * Determine whether the user can update the model.
     */
    public function update(User $user, Faction $faction): bool
    {
        return $user->is($faction->compendium->creator)
            || $user->can("factions.update.$faction->id");
    }

    /**
     * Determine whether the user can delete the model.
     */
    public function delete(User $user, Faction $faction): bool
    {
        return $user->is($faction->compendium->creator);
    }
}
