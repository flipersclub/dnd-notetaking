<?php

namespace App\Policies;

use App\Models\Session;
use App\Models\User;
use Illuminate\Auth\Access\Response;

class SessionPolicy
{
    /**
     * Determine whether the user can view any models.
     */
    public function viewAny(User $user): bool
    {
        return $user->can('sessions.view');
    }

    /**
     * Determine whether the user can view the model.
     */
    public function view(User $user, Session $session): bool
    {
        return $user->can('sessions.view')
            || $user->can("sessions.view.$session->id")
            || $user->can('view', $session->campaign);
    }

    /**
     * Determine whether the user can create models.
     */
    public function create(User $user): bool
    {
        return $user->can('sessions.create')
            || $user->campaigns()->exists();
    }

    /**
     * Determine whether the user can update the model.
     */
    public function update(User $user, Session $session): bool
    {
        return $user->can("sessions.update")
            || $user->can("sessions.update.$session->id")
            || $user->can('update', $session->campaign);
    }

    /**
     * Determine whether the user can delete the model.
     */
    public function delete(User $user, Session $session): bool
    {
        return $user->can('sessions.delete')
            || $user->can("sessions.delete.$session->id")
            || $user->can('delete', $session->campaign);
    }

    /**
     * Determine whether the user can restore the model.
     */
    public function restore(User $user, Session $session): bool
    {
        return $user->can('sessions.restore')
            || $user->can("sessions.restore.$session->id")
            || $user->can('restore', $session->campaign);
    }

    /**
     * Determine whether the user can permanently delete the model.
     */
    public function forceDelete(User $user, Session $session): bool
    {
        return $user->can('sessions.forceDelete')
            || $user->can("sessions.forceDelete.$session->id")
            || $user->can('forceDelete', $session->campaign);
    }
}
