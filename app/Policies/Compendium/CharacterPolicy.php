<?php

namespace App\Policies\Compendium;

use App\Models\Compendium\Character;
use App\Models\User;

class CharacterPolicy
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
    public function view(User $user, Character $character): bool
    {
        return $user->is($character->compendium->creator)
            || $user->can("characters.view.$character->id");
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
    public function update(User $user, Character $character): bool
    {
        return $user->is($character->compendium->creator)
            || $user->can("characters.update.$character->id");
    }

    /**
     * Determine whether the user can delete the model.
     */
    public function delete(User $user, Character $character): bool
    {
        return $user->is($character->compendium->creator);
    }
}
