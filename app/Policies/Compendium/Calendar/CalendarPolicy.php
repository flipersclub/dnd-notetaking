<?php

namespace App\Policies\Compendium\Calendar;

use App\Models\Compendium\Calendar\Calendar;
use App\Models\User;

class CalendarPolicy
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
    public function view(User $user, Calendar $calendar): bool
    {
        return $user->is($calendar->compendium->creator)
            || $user->can("calendars.view.$calendar->id");
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
    public function update(User $user, Calendar $calendar): bool
    {
        return $user->is($calendar->compendium->creator)
            || $user->can("calendars.update.$calendar->id");
    }

    /**
     * Determine whether the user can delete the model.
     */
    public function delete(User $user, Calendar $calendar): bool
    {
        return $user->is($calendar->compendium->creator);
    }

    /**
     * Determine whether the user can restore the model.
     */
    public function restore(User $user, Calendar $calendar): bool
    {
        //
    }

    /**
     * Determine whether the user can permanently delete the model.
     */
    public function forceDelete(User $user, Calendar $calendar): bool
    {
        //
    }
}
