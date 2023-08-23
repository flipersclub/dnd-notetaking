<?php

namespace App\Policies;

use App\Models\Compendium\Quest;
use App\Models\User;

class QuestPolicy
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
    public function view(User $user, Quest $quest): bool
    {
        return $user->is($quest->compendium->creator)
            || $user->is($quest->campaign->gameMaster)
            || $user->can("quests.view.$quest->id");
    }

    /**
     * Determine whether the user can create models.
     */
    public function create(User $user): bool
    {
        return $user->compendia()->exists()
            || $user->campaigns()->exists();
    }

    /**
     * Determine whether the user can update the model.
     */
    public function update(User $user, Quest $quest): bool
    {
        return $user->is($quest->compendium->creator)
            || $user->is($quest->campaign->gameMaster)
            || $user->can("quests.update.$quest->id");
    }

    /**
     * Determine whether the user can delete the model.
     */
    public function delete(User $user, Quest $quest): bool
    {
        return $user->is($quest->compendium->creator)
            || $user->is($quest->campaign->gameMaster);
    }

}
