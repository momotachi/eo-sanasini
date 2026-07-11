<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Certificate extends Model
{
    protected $fillable = ['event_id', 'participant_id', 'template_url', 'issued_at', 'code'];
    protected $casts = ['issued_at' => 'datetime'];
    public function event(): BelongsTo { return $this->belongsTo(Event::class); }
    public function participant(): BelongsTo { return $this->belongsTo(Participant::class); }
}
