<?php

namespace App\Policies\Pivots;

use App\Models\Pivots\LocationLocationService;
use App\Models\User;
use Illuminate\Auth\Access\Response;

class LocationLocationServicePolicy
{
    /**
     * Determine whether the user can view any models.
     */
    public function viewAny(User $user): bool
    {
        //
    }

    /**
     * Determine whether the user can view the model.
     */
    public function view(User $user, LocationLocationService $locationService): bool
    {
        //
    }

    /**
     * Determine whether the user can create models.
     */
    public function create(User $user): bool
    {
        //
    }

    /**
     * Determine whether the user can update the model.
     */
    public function update(User $user, LocationLocationService $locationService): bool
    {
        //
    }

    /**
     * Determine whether the user can delete the model.
     */
    public function delete(User $user, LocationLocationService $locationService): bool
    {
        //
    }

    /**
     * Determine whether the user can restore the model.
     */
    public function restore(User $user, LocationLocationService $locationService): bool
    {
        //
    }

    /**
     * Determine whether the user can permanently delete the model.
     */
    public function forceDelete(User $user, LocationLocationService $locationService): bool
    {
        //
    }
}
