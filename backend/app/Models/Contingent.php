<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Contingent extends Model
{
    protected $fillable = [
        'event_id', 'name', 'type', 'logo_url', 'contact_name', 'contact_phone',
    ];

    public function event(): BelongsTo { return $this->belongsTo(Event::class); }
    public function participants(): HasMany { return $this->hasMany(Participant::class); }
    public function medals(): HasMany { return $this->hasMany(Medal::class); }
}
