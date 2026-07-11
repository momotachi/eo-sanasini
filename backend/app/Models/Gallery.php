<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Gallery extends Model
{
    public const UPDATED_AT = null;
    protected $fillable = ['event_id', 'url', 'caption', 'uploaded_at'];
    protected $casts = ['uploaded_at' => 'datetime'];
    public function event(): BelongsTo { return $this->belongsTo(Event::class); }
}
