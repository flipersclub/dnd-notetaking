<?php

namespace App\Providers;

use App\Models\Campaign;
use App\Models\Compendium\Calendar\Calendar;
use App\Models\Compendium\Calendar\Event;
use App\Models\Compendium\Calendar\Month;
use App\Models\Compendium\Calendar\Weekday;
use App\Models\Compendium\Character;
use App\Models\Compendium\Compendium;
use App\Models\Compendium\Concept;
use App\Models\Compendium\Currency;
use App\Models\Compendium\Deity;
use App\Models\Compendium\Encounter;
use App\Models\Compendium\Faction;
use App\Models\Compendium\Item;
use App\Models\Compendium\Language;
use App\Models\Compendium\Location\Location;
use App\Models\Compendium\NaturalResource;
use App\Models\Compendium\Pantheon;
use App\Models\Compendium\Plane;
use App\Models\Compendium\Quest;
use App\Models\Compendium\Religion;
use App\Models\Compendium\Species;
use App\Models\Compendium\Spell;
use App\Models\Compendium\Story;
use App\Models\Image\Image;
use App\Models\Note;
use App\Models\Notebook;
use App\Models\Session;
use App\Models\System;
use App\Models\User;
use App\Policies\CampaignPolicy;
use App\Policies\Compendium\Calendar\CalendarPolicy;
use App\Policies\Compendium\Calendar\EventPolicy;
use App\Policies\Compendium\Calendar\MonthPolicy;
use App\Policies\Compendium\Calendar\WeekdayPolicy;
use App\Policies\Compendium\CharacterPolicy;
use App\Policies\Compendium\CompendiumPolicy;
use App\Policies\Compendium\ConceptPolicy;
use App\Policies\Compendium\CurrencyPolicy;
use App\Policies\Compendium\DeityPolicy;
use App\Policies\Compendium\FactionPolicy;
use App\Policies\Compendium\ItemPolicy;
use App\Policies\Compendium\LanguagePolicy;
use App\Policies\Compendium\Location\LocationPolicy;
use App\Policies\Compendium\NaturalResourcePolicy;
use App\Policies\Compendium\PantheonPolicy;
use App\Policies\Compendium\PlanePolicy;
use App\Policies\Compendium\ReligionPolicy;
use App\Policies\Compendium\SpeciesPolicy;
use App\Policies\Compendium\SpellPolicy;
use App\Policies\Compendium\StoryPolicy;
use App\Policies\EncounterPolicy;
use App\Policies\ImagePolicy;
use App\Policies\NotebookPolicy;
use App\Policies\NotePolicy;
use App\Policies\QuestPolicy;
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
        Image::class => ImagePolicy::class,
        // Campaign
        Campaign::class => CampaignPolicy::class,
        Session::class => SessionPolicy::class,
        Encounter::class => EncounterPolicy::class,
        Quest::class => QuestPolicy::class,
        // Compendium
        Compendium::class => CompendiumPolicy::class,
        Location::class => LocationPolicy::class,
        Character::class => CharacterPolicy::class,
        Concept::class => ConceptPolicy::class,
        Currency::class => CurrencyPolicy::class,
        Deity::class => DeityPolicy::class,
        Faction::class => FactionPolicy::class,
        Item::class => ItemPolicy::class,
        Language::class => LanguagePolicy::class,
        NaturalResource::class => NaturalResourcePolicy::class,
        Pantheon::class => PantheonPolicy::class,
        Plane::class => PlanePolicy::class,
        Religion::class => ReligionPolicy::class,
        Species::class => SpeciesPolicy::class,
        Spell::class => SpellPolicy::class,
        Story::class => StoryPolicy::class,
        // Calendar
        Calendar::class => CalendarPolicy::class,
        Event::class => EventPolicy::class,
        Month::class => MonthPolicy::class,
        Weekday::class => WeekdayPolicy::class,
        // Other
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
