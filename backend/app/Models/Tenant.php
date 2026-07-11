<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Tenant extends Model
{
    protected $fillable = ['event_id', 'name', 'category', 'description', 'logo_url', 'contact_name', 'contact_phone', 'booth_number'];
    public function event(): BelongsTo { return $this->belongsTo(Event::class); }
}
