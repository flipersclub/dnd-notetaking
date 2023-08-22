<?php

namespace App\Policies\Compendium;

use App\Models\Compendium\Species;
use App\Models\User;

class SpeciesPolicy
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
    public function view(User $user, Species $species): bool
    {
        return $user->is($species->compendium->creator)
            || $user->can("species.view.$species->id");
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
    public function update(User $user, Species $species): bool
    {
        return $user->is($species->compendium->creator)
            || $user->can("species.update.$species->id");
    }

    /**
     * Determine whether the user can delete the model.
     */
    public function delete(User $user, Species $species): bool
    {
        return $user->is($species->compendium->creator);
    }
}
