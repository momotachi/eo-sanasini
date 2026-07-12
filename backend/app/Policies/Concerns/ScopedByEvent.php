<?php

namespace App\Policies\Concerns;

use App\Models\User;
use Illuminate\Database\Eloquent\Model;

/**
 * Trait untuk Policy yang scope akses STAF berdasarkan event assignment.
 *
 * Kontrak model yang pakai trait ini harus implement method getEventId(): ?int
 * yang return event_id dari record (langsung atau via relasi).
 */
trait ScopedByEvent
{
    /**
     * Super Admin & Admin: selalu boleh.
     * STAF: hanya kalau event dari record ada di assignment-nya.
     */
    protected function canAccess(User $user, Model $record): bool
    {
        if ($user->isAdmin()) {
            return true;
        }

        // ambil event_id dari record
        $eventId = $this->resolveEventId($record);
        if ($eventId === null) {
            return false;
        }

        return $user->canManageEvent($eventId);
    }

    /**
     * Resolve event_id dari berbagai bentuk model.
     * Model mungkin punya kolom event_id langsung, atau via relasi division/event.
     */
    protected function resolveEventId(Model $record): ?int
    {
        // kolom event_id langsung
        if (isset($record->event_id)) {
            return (int) $record->event_id;
        }

        // via relasi event
        if (method_exists($record, 'event') && $record->event) {
            return (int) $record->event->id;
        }

        // via relasi division -> event (mis. Match, Participant, Medal)
        if (method_exists($record, 'division') && $record->division) {
            return (int) $record->division->event_id;
        }

        return null;
    }
}
