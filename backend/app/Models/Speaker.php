<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Speaker extends Model
{
    protected $fillable = ['event_id', 'name', 'title', 'bio', 'photo_url', 'email'];
    public function event(): BelongsTo { return $this->belongsTo(Event::class); }
    public function sessions(): HasMany { return $this->hasMany(EventSession::class); }
}
