<?php

namespace App\Policies\Compendium\Location;

use App\Models\Compendium\Compendium;
use App\Models\Compendium\Location\Location;
use App\Models\User;

class LocationPolicy
{
    /**
     * Determine whether the user can view any models.
     */
    public function viewAny(User $user): bool
    {
        return $user->compendia()->exists();
    }

    /**
     * Determine whether the user can view the model.
     */
    public function view(User $user, Location $location): bool
    {
        return $user->is($location->compendium->creator)
            || ($location->is_public && $user->can("compendia.view.$location->compendium_id"))
            || $user->can("locations.view.$location->id");
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
    public function update(User $user, Location $location): bool
    {
        return $user->is($location->compendium->creator)
            || $user->can("locations.update.$location->id");
    }

    /**
     * Determine whether the user can delete the model.
     */
    public function delete(User $user, Location $location): bool
    {
        return $user->is($location->compendium->creator);
    }
}
