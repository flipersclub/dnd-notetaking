<?php

namespace App\Policies\Compendium;

use App\Models\Compendium\Spell;
use App\Models\User;

class SpellPolicy
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
    public function view(User $user, Spell $spell): bool
    {
        return $user->is($spell->compendium->creator)
            || $user->can("spells.view.$spell->id");
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
    public function update(User $user, Spell $spell): bool
    {
        return $user->is($spell->compendium->creator)
            || $user->can("spells.update.$spell->id");
    }

    /**
     * Determine whether the user can delete the model.
     */
    public function delete(User $user, Spell $spell): bool
    {
        return $user->is($spell->compendium->creator);
    }
}
