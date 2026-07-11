<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class EventConfig extends Model
{
    protected $fillable = [
        'event_id', 'registration_type', 'bronze_per_division',
        'age_categories', 'disciplines', 'extra_config',
    ];

    protected $casts = [
        'age_categories' => 'array',
        'disciplines' => 'array',
        'extra_config' => 'array',
    ];

    public function event(): BelongsTo
    {
        return $this->belongsTo(Event::class);
    }
}
