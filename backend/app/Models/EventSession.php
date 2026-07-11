<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class EventSession extends Model
{
    protected $table = 'event_sessions';
    protected $fillable = ['event_id', 'title', 'description', 'start_time', 'end_time', 'room', 'speaker_id', 'track', 'capacity'];
    protected $casts = ['start_time' => 'datetime', 'end_time' => 'datetime'];
    public function event(): BelongsTo { return $this->belongsTo(Event::class); }
    public function speaker(): BelongsTo { return $this->belongsTo(Speaker::class); }
}
