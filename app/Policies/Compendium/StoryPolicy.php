<?php

namespace App\Policies\Compendium;

use App\Models\Compendium\Story;
use App\Models\User;

class StoryPolicy
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
    public function view(User $user, Story $story): bool
    {
        return $user->is($story->compendium->creator)
            || $user->can("stories.view.$story->id");
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
    public function update(User $user, Story $story): bool
    {
        return $user->is($story->compendium->creator)
            || $user->can("stories.update.$story->id");
    }

    /**
     * Determine whether the user can delete the model.
     */
    public function delete(User $user, Story $story): bool
    {
        return $user->is($story->compendium->creator);
    }
}
