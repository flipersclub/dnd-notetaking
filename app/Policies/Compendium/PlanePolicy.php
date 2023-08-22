<?php

namespace App\Policies\Compendium;

use App\Models\Compendium\Plane;
use App\Models\User;

class PlanePolicy
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
    public function view(User $user, Plane $plane): bool
    {
        return $user->is($plane->compendium->creator)
            || $user->can("planes.view.$plane->id");
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
    public function update(User $user, Plane $plane): bool
    {
        return $user->is($plane->compendium->creator)
            || $user->can("planes.update.$plane->id");
    }

    /**
     * Determine whether the user can delete the model.
     */
    public function delete(User $user, Plane $plane): bool
    {
        return $user->is($plane->compendium->creator);
    }
}
