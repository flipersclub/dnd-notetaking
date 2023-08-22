<?php

namespace App\Policies\Compendium;

use App\Models\Compendium\Deity;
use App\Models\User;

class DeityPolicy
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
    public function view(User $user, Deity $deity): bool
    {
        return $user->is($deity->compendium->creator)
            || $user->can("deities.view.$deity->id");
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
    public function update(User $user, Deity $deity): bool
    {
        return $user->is($deity->compendium->creator)
            || $user->can("deities.update.$deity->id");
    }

    /**
     * Determine whether the user can delete the model.
     */
    public function delete(User $user, Deity $deity): bool
    {
        return $user->is($deity->compendium->creator);
    }
}
