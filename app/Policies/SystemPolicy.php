<?php

namespace App\Policies;

use App\Models\System;
use App\Models\User;
use Illuminate\Auth\Access\Response;

class SystemPolicy
{
    /**
     * Determine whether the user can view any models.
     */
    public function viewAny(User $user): bool
    {
        return $user->can('systems.view');
    }

    /**
     * Determine whether the user can view the model.
     */
    public function view(User $user, System $system): bool
    {
        return $user->can('systems.view')
            || $user->can("systems.view.$system->id");
    }

    /**
     * Determine whether the user can create models.
     */
    public function create(User $user): bool
    {
        return $user->can('systems.create');
    }

    /**
     * Determine whether the user can update the model.
     */
    public function update(User $user, System $system): bool
    {
        return $user->can("systems.update")
            || $user->can("systems.update.$system->id");
    }

    /**
     * Determine whether the user can delete the model.
     */
    public function delete(User $user, System $system): bool
    {
        return $user->can('systems.delete')
            || $user->can("systems.delete.$system->id");
    }

    /**
     * Determine whether the user can restore the model.
     */
    public function restore(User $user, System $system): bool
    {
        return $user->can('systems.restore')
            || $user->can("systems.restore.$system->id");
    }

    /**
     * Determine whether the user can permanently delete the model.
     */
    public function forceDelete(User $user, System $system): bool
    {
        return $user->can('systems.forceDelete')
            || $user->can("systems.forceDelete.$system->id");
    }
}
