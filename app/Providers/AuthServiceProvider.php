<?php

namespace App\Providers;

use App\Models\Campaign;
use App\Models\Compendium\Character;
use App\Models\Compendium\Compendium;
use App\Models\Compendium\Location\Location;
use App\Models\Note;
use App\Models\Notebook;
use App\Models\Session;
use App\Models\System;
use App\Models\User;
use App\Policies\CampaignPolicy;
use App\Policies\Compendium\CharacterPolicy;
use App\Policies\Compendium\CompendiumPolicy;
use App\Policies\Compendium\Location\LocationPolicy;
use App\Policies\NotebookPolicy;
use App\Policies\NotePolicy;
use App\Policies\SessionPolicy;
use App\Policies\SystemPolicy;
use Illuminate\Auth\Notifications\ResetPassword;
use Illuminate\Foundation\Support\Providers\AuthServiceProvider as ServiceProvider;
use Illuminate\Support\Facades\Gate;

class AuthServiceProvider extends ServiceProvider
{
    /**
     * The policy mappings for the application.
     *
     * @var array<class-string, class-string>
     */
    protected $policies = [
        System::class => SystemPolicy::class,
        Campaign::class => CampaignPolicy::class,
        Session::class => SessionPolicy::class,
        Compendium::class => CompendiumPolicy::class,
        Location::class => LocationPolicy::class,
        Character::class => CharacterPolicy::class,
        Notebook::class => NotebookPolicy::class,
        Note::class => NotePolicy::class,
    ];

    /**
     * Register any authentication / authorization services.
     */
    public function boot(): void
    {
        $this->registerPolicies();

        ResetPassword::createUrlUsing(function (object $notifiable, string $token) {
            return config('app.frontend_url') . "/password-reset/$token?email={$notifiable->getEmailForPasswordReset()}";
        });

        Gate::before(function (User $user) {
            if ($user->isAdministrator()) {
                return true;
            }
        });

        //
    }
}
