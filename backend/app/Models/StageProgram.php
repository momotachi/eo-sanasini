<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class StageProgram extends Model
{
    protected $fillable = ['event_id', 'time', 'title', 'performer', 'stage', 'description'];
    protected $casts = ['time' => 'datetime'];
    public function event(): BelongsTo { return $this->belongsTo(Event::class); }
}
