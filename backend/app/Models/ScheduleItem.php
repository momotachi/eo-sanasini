<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ScheduleItem extends Model
{
    protected $fillable = ['event_id', 'time', 'title', 'venue_id', 'division', 'notes'];

    protected $casts = ['time' => 'datetime'];

    public function event(): BelongsTo { return $this->belongsTo(Event::class); }
    public function venue(): BelongsTo { return $this->belongsTo(Venue::class); }
}
