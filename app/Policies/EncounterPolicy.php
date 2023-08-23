<?php

namespace App\Policies;

use App\Models\Compendium\Encounter;
use App\Models\User;

class EncounterPolicy
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
    public function view(User $user, Encounter $encounter): bool
    {
        return $user->is($encounter->compendium->creator)
            || $user->is($encounter->campaign->gameMaster)
            || $user->can("encounters.view.$encounter->id");
    }

    /**
     * Determine whether the user can create models.
     */
    public function create(User $user): bool
    {
        return $user->compendia()->exists()
            || $user->campaigns()->exists();
    }

    /**
     * Determine whether the user can update the model.
     */
    public function update(User $user, Encounter $encounter): bool
    {
        return $user->is($encounter->compendium->creator)
            || $user->is($encounter->campaign->gameMaster)
            || $user->can("encounters.update.$encounter->id");
    }

    /**
     * Determine whether the user can delete the model.
     */
    public function delete(User $user, Encounter $encounter): bool
    {
        return $user->is($encounter->compendium->creator)
            || $user->is($encounter->campaign->gameMaster);
    }
}
