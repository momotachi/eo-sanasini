<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

/**
 * Match model — namanya MatchModel karena "Match" reserved word di PHP.
 * Tabel tetap `matches`.
 */
class MatchModel extends Model
{
    protected $table = 'matches';

    protected $fillable = [
        'division_id', 'round', 'bracket_position', 'group_label',
        'participant_a_id', 'participant_b_id',
        'score_a', 'score_b', 'winner_id',
        'status', 'venue_id', 'scheduled_at', 'notes',
    ];

    protected $casts = [
        'score_a' => 'array',
        'score_b' => 'array',
        'scheduled_at' => 'datetime',
    ];

    public function division(): BelongsTo { return $this->belongsTo(Division::class); }
    public function participantA(): BelongsTo { return $this->belongsTo(Participant::class, 'participant_a_id'); }
    public function participantB(): BelongsTo { return $this->belongsTo(Participant::class, 'participant_b_id'); }
    public function winner(): BelongsTo { return $this->belongsTo(Participant::class, 'winner_id'); }
    public function venue(): BelongsTo { return $this->belongsTo(Venue::class); }
}
