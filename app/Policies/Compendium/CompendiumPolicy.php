<?php

namespace App\Policies\Compendium;

use App\Models\Compendium\Compendium;
use App\Models\User;

class CompendiumPolicy
{
    /**
     * Determine whether the user can view any models.
     */
    public function viewAny(User $user): bool
    {
        return $user->can('compendia.view');
    }

    /**
     * Determine whether the user can view the model.
     */
    public function view(User $user, Compendium $compendium): bool
    {
        return $user->can('compendia.view')
            || $user->is($compendium->creator);
    }

    /**
     * Determine whether the user can create models.
     */
    public function create(User $user): bool
    {
        return $user->can('compendia.create');
    }

    /**
     * Determine whether the user can update the model.
     */
    public function update(User $user, Compendium $compendium): bool
    {
        return $user->can("compendia.update")
            || $user->is($compendium->creator);
    }

    /**
     * Determine whether the user can delete the model.
     */
    public function delete(User $user, Compendium $compendium): bool
    {
        return $user->can('compendia.delete')
            || $user->is($compendium->creator);
    }

    /**
     * Determine whether the user can restore the model.
     */
    public function restore(User $user, Compendium $compendium): bool
    {
        return $user->can('compendia.restore')
            || $user->is($compendium->creator);
    }

    /**
     * Determine whether the user can permanently delete the model.
     */
    public function forceDelete(User $user, Compendium $compendium): bool
    {
        return $user->can('compendia.forceDelete')
            || $user->can("compendia.forceDelete.$compendium->id");
    }
}
