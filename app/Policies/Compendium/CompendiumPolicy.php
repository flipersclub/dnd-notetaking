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
        return $user->hasPermissionTo('compendia.view');
    }

    /**
     * Determine whether the user can view the model.
     */
    public function view(User $user, Compendium $compendium): bool
    {
        return $user->hasPermissionTo("compendia.view")
            || $user->hasPermissionTo("compendia.view.$compendium->id")
            || $user->is($compendium->creator);
    }

    /**
     * Determine whether the user can create models.
     */
    public function create(User $user): bool
    {
        return $user->hasPermissionTo('compendia.create');
    }

    /**
     * Determine whether the user can update the model.
     */
    public function update(User $user, Compendium $compendium): bool
    {
        return $user->hasPermissionTo("compendia.update")
            || $user->hasPermissionTo("compendia.update.$compendium->id")
            || $user->is($compendium->creator);
    }

    /**
     * Determine whether the user can delete the model.
     */
    public function delete(User $user, Compendium $compendium): bool
    {
        return $user->hasPermissionTo("compendia.delete.$compendium->id")
            || $user->is($compendium->creator);
    }
}
