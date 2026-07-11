<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Division extends Model
{
    protected $fillable = [
        'event_id', 'discipline', 'age_category', 'gender', 'class_name',
        'format', 'scoring_config',
    ];

    protected $casts = ['scoring_config' => 'array'];

    public function event(): BelongsTo { return $this->belongsTo(Event::class); }
    public function participants(): HasMany { return $this->hasMany(Participant::class); }
    public function matches(): HasMany { return $this->hasMany(MatchModel::class); }
    public function medals(): HasMany { return $this->hasMany(Medal::class); }
}
