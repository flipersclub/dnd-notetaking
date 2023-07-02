<?php

namespace App\Policies;

use App\Models\Campaign;
use App\Models\User;
use Illuminate\Auth\Access\Response;

class CampaignPolicy
{
    /**
     * Determine whether the user can view any models.
     */
    public function viewAny(User $user): bool
    {
        return $user->can('campaigns.view');
    }

    /**
     * Determine whether the user can view the model.
     */
    public function view(User $user, Campaign $campaign): bool
    {
        return $user->can('campaigns.view')
            || $user->can("campaigns.view.$campaign->id")
            || $user->id === $campaign->game_master_id;
    }

    /**
     * Determine whether the user can create models.
     */
    public function create(User $user): bool
    {
        return $user->can('campaigns.create');
    }

    /**
     * Determine whether the user can update the model.
     */
    public function update(User $user, Campaign $campaign): bool
    {
        return $user->can("campaigns.update")
            || $user->can("campaigns.update.$campaign->id")
            || $user->id === $campaign->game_master_id;
    }

    /**
     * Determine whether the user can delete the model.
     */
    public function delete(User $user, Campaign $campaign): bool
    {
        return $user->can('campaigns.delete')
            || $user->can("campaigns.delete.$campaign->id")
            || $user->id === $campaign->game_master_id;
    }

    /**
     * Determine whether the user can restore the model.
     */
    public function restore(User $user, Campaign $campaign): bool
    {
        return $user->can('campaigns.restore')
            || $user->can("campaigns.restore.$campaign->id")
            || $user->id === $campaign->game_master_id;
    }

    /**
     * Determine whether the user can permanently delete the model.
     */
    public function forceDelete(User $user, Campaign $campaign): bool
    {
        return $user->can('campaigns.forceDelete')
            || $user->can("campaigns.forceDelete.$campaign->id")
            || $user->id === $campaign->game_master_id;
    }
}
