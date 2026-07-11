<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class TicketType extends Model
{
    protected $fillable = ['event_id', 'name', 'price', 'currency', 'quota', 'sold_count', 'description', 'sale_start', 'sale_end'];
    protected $casts = ['price' => 'decimal:2', 'sale_start' => 'datetime', 'sale_end' => 'datetime'];
    public function event(): BelongsTo { return $this->belongsTo(Event::class); }
    public function participants(): HasMany { return $this->hasMany(Participant::class); }
}
