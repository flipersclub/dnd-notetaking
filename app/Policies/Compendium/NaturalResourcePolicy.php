<?php

namespace App\Policies\Compendium;

use App\Models\Compendium\NaturalResource;
use App\Models\User;

class NaturalResourcePolicy
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
    public function view(User $user, NaturalResource $naturalResource): bool
    {
        return $user->is($naturalResource->compendium->creator)
            || $user->can("naturalResources.view.$naturalResource->id");
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
    public function update(User $user, NaturalResource $naturalResource): bool
    {
        return $user->is($naturalResource->compendium->creator)
            || $user->can("naturalResources.update.$naturalResource->id");
    }

    /**
     * Determine whether the user can delete the model.
     */
    public function delete(User $user, NaturalResource $naturalResource): bool
    {
        return $user->is($naturalResource->compendium->creator);
    }
}
