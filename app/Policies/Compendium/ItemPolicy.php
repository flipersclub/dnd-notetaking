<?php

namespace App\Policies\Compendium;

use App\Models\Compendium\Item;
use App\Models\User;

class ItemPolicy
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
    public function view(User $user, Item $item): bool
    {
        return $user->is($item->compendium->creator)
            || $user->can("items.view.$item->id");
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
    public function update(User $user, Item $item): bool
    {
        return $user->is($item->compendium->creator)
            || $user->can("items.update.$item->id");
    }

    /**
     * Determine whether the user can delete the model.
     */
    public function delete(User $user, Item $item): bool
    {
        return $user->is($item->compendium->creator);
    }
}
