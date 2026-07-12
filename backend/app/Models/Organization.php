<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Organization extends Model
{
    use HasFactory;
    protected $fillable = [
        'name', 'slug', 'tagline', 'about', 'logo_url', 'website',
        'instagram', 'email', 'phone', 'address',
    ];

    public function events(): HasMany
    {
        return $this->hasMany(Event::class);
    }
}
