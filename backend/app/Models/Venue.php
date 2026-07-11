<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Venue extends Model
{
    protected $fillable = ['event_id', 'name', 'area'];

    public function event(): BelongsTo { return $this->belongsTo(Event::class); }
    public function matches(): HasMany { return $this->hasMany(MatchModel::class); }
}
