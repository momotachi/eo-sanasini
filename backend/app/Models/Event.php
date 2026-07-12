<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasManyThrough;
use Illuminate\Database\Eloquent\Relations\HasOne;

class Event extends Model
{
    protected $fillable = [
        'organization_id', 'name', 'slug', 'type', 'category', 'modules',
        'status', 'description', 'poster_url', 'start_date', 'end_date',
        'venue', 'address', 'map_url',
        'contact_name', 'contact_phone', 'contact_email', 'is_public',
    ];

    protected $casts = [
        'modules' => 'array',
        'start_date' => 'datetime',
        'end_date' => 'datetime',
        'is_public' => 'boolean',
    ];

    public function organization(): BelongsTo
    {
        return $this->belongsTo(Organization::class);
    }

    public function config(): HasOne
    {
        return $this->hasOne(EventConfig::class);
    }

    public function contingents(): HasMany
    {
        return $this->hasMany(Contingent::class);
    }

    public function divisions(): HasMany
    {
        return $this->hasMany(Division::class);
    }

    public function venues(): HasMany
    {
        return $this->hasMany(Venue::class);
    }

    public function schedule(): HasMany
    {
        return $this->hasMany(ScheduleItem::class);
    }

    public function medals(): HasMany
    {
        return $this->hasMany(Medal::class);
    }

    /**
     * Match belongs to Division, Division belongs to Event.
     * Relasi hasManyThrough supaya RelationManager MatchesRelationManager bisa query match dari Event.
     */
    public function matches()
    {
        return $this->hasManyThrough(MatchModel::class, Division::class);
    }

    public function participants(): HasMany
    {
        return $this->hasMany(Participant::class);
    }

    // Multi-category relations
    public function staff(): BelongsToMany
    {
        return $this->belongsToMany(User::class, 'event_staff')->withPivot('assigned_at');
    }

    public function tenants(): HasMany { return $this->hasMany(Tenant::class); }
    public function stagePrograms(): HasMany { return $this->hasMany(StageProgram::class); }
    public function speakers(): HasMany { return $this->hasMany(Speaker::class); }
    public function sessions(): HasMany { return $this->hasMany(EventSession::class); }
    public function ticketTypes(): HasMany { return $this->hasMany(TicketType::class); }
    public function itinerary(): HasMany { return $this->hasMany(ItineraryItem::class); }
    public function gallery(): HasMany { return $this->hasMany(Gallery::class); }
    public function certificates(): HasMany { return $this->hasMany(Certificate::class); }
}
