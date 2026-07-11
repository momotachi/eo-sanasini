<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Medal extends Model
{
    protected $fillable = [
        'event_id', 'division_id', 'participant_id', 'contingent_id',
        'type', 'discipline',
    ];

    public function event(): BelongsTo { return $this->belongsTo(Event::class); }
    public function division(): BelongsTo { return $this->belongsTo(Division::class); }
    public function participant(): BelongsTo { return $this->belongsTo(Participant::class); }
    public function contingent(): BelongsTo { return $this->belongsTo(Contingent::class); }
}
