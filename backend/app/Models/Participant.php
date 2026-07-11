<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Participant extends Model
{
    protected $fillable = [
        'event_id', 'division_id', 'contingent_id', 'ticket_type_id',
        'name', 'gender', 'birth_date', 'email', 'phone',
        'job_title', 'id_doc_number', 'emergency_contact', 'document_url',
        'seed', 'status', 'meta',
    ];

    protected $casts = [
        'birth_date' => 'date',
        'meta' => 'array',
    ];

    public function event(): BelongsTo { return $this->belongsTo(Event::class); }
    public function division(): BelongsTo { return $this->belongsTo(Division::class); }
    public function contingent(): BelongsTo { return $this->belongsTo(Contingent::class); }
    public function ticketType(): BelongsTo { return $this->belongsTo(TicketType::class); }
    public function medals(): HasMany { return $this->hasMany(Medal::class); }
    public function certificates(): HasMany { return $this->hasMany(Certificate::class); }
}
