<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ItineraryItem extends Model
{
    protected $fillable = ['event_id', 'day', 'time', 'title', 'location', 'notes', 'transport_mode'];
    protected $casts = ['time' => 'datetime'];
    public function event(): BelongsTo { return $this->belongsTo(Event::class); }
}
