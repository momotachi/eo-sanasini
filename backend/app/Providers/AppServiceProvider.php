<?php

namespace App\Providers;

use App\Models\Contingent;
use App\Models\Division;
use App\Models\Event;
use App\Models\EventSession;
use App\Models\ItineraryItem;
use App\Models\MatchModel;
use App\Models\Medal;
use App\Models\Participant;
use App\Models\ScheduleItem;
use App\Models\Speaker;
use App\Models\StageProgram;
use App\Models\Tenant;
use App\Models\TicketType;
use App\Models\Venue;
use App\Observers\MatchModelObserver;
use App\Policies\ContingentPolicy;
use App\Policies\DivisionPolicy;
use App\Policies\EventPolicy;
use App\Policies\EventSessionPolicy;
use App\Policies\ItineraryItemPolicy;
use App\Policies\MatchModelPolicy;
use App\Policies\MedalPolicy;
use App\Policies\ParticipantPolicy;
use App\Policies\ScheduleItemPolicy;
use App\Policies\SpeakerPolicy;
use App\Policies\StageProgramPolicy;
use App\Policies\TenantPolicy;
use App\Policies\TicketTypePolicy;
use App\Policies\VenuePolicy;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        //
    }

    public function boot(): void
    {
        // Observer: auto-advance bracket + auto-medal
        MatchModel::observe(MatchModelObserver::class);

        // RBAC Policies — STAF hanya bisa kelola event yang di-assign.
        // SUPER_ADMIN & ADMIN: full akses semua event.
        Gate::policy(Event::class, EventPolicy::class);
        Gate::policy(Contingent::class, ContingentPolicy::class);
        Gate::policy(Division::class, DivisionPolicy::class);
        Gate::policy(Participant::class, ParticipantPolicy::class);
        Gate::policy(MatchModel::class, MatchModelPolicy::class);
        Gate::policy(Medal::class, MedalPolicy::class);
        Gate::policy(Venue::class, VenuePolicy::class);
        Gate::policy(ScheduleItem::class, ScheduleItemPolicy::class);
        Gate::policy(Tenant::class, TenantPolicy::class);
        Gate::policy(StageProgram::class, StageProgramPolicy::class);
        Gate::policy(Speaker::class, SpeakerPolicy::class);
        Gate::policy(EventSession::class, EventSessionPolicy::class);
        Gate::policy(TicketType::class, TicketTypePolicy::class);
        Gate::policy(ItineraryItem::class, ItineraryItemPolicy::class);
    }
}
