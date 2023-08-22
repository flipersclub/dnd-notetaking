<?php

namespace App\Policies\Compendium;

use App\Models\Compendium\Religion;
use App\Models\User;

class ReligionPolicy
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
    public function view(User $user, Religion $religion): bool
    {
        return $user->is($religion->compendium->creator)
            || $user->can("religions.view.$religion->id");
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
    public function update(User $user, Religion $religion): bool
    {
        return $user->is($religion->compendium->creator)
            || $user->can("religions.update.$religion->id");
    }

    /**
     * Determine whether the user can delete the model.
     */
    public function delete(User $user, Religion $religion): bool
    {
        return $user->is($religion->compendium->creator);
    }
}
