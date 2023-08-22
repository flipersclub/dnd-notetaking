<?php

namespace App\Policies\Compendium;

use App\Models\Compendium\Concept;
use App\Models\User;

class ConceptPolicy
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
    public function view(User $user, Concept $concept): bool
    {
        return $user->is($concept->compendium->creator)
            || $user->can("concepts.view.$concept->id");
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
    public function update(User $user, Concept $concept): bool
    {
        return $user->is($concept->compendium->creator)
            || $user->can("concepts.update.$concept->id");
    }

    /**
     * Determine whether the user can delete the model.
     */
    public function delete(User $user, Concept $concept): bool
    {
        return $user->is($concept->compendium->creator);
    }
}
