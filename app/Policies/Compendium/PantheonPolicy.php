<?php

namespace App\Policies\Compendium;

use App\Models\Compendium\Pantheon;
use App\Models\User;

class PantheonPolicy
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
    public function view(User $user, Pantheon $pantheon): bool
    {
        return $user->is($pantheon->compendium->creator)
            || $user->can("pantheons.view.$pantheon->id");
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
    public function update(User $user, Pantheon $pantheon): bool
    {
        return $user->is($pantheon->compendium->creator)
            || $user->can("pantheons.update.$pantheon->id");
    }

    /**
     * Determine whether the user can delete the model.
     */
    public function delete(User $user, Pantheon $pantheon): bool
    {
        return $user->is($pantheon->compendium->creator);
    }
}
