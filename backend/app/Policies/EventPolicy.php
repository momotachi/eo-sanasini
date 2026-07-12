<?php

namespace App\Policies;

use App\Models\Event;
use App\Models\User;
use App\Policies\Concerns\ScopedByEvent;

/**
 * Policy untuk Event — STAF hanya bisa kelola event yang di-assign.
 * SUPER_ADMIN & ADMIN: full akses semua event.
 */
class EventPolicy
{
    use ScopedByEvent;

    public function viewAny(User $user): bool
    {
        return true; // semua role bisa lihat list event
    }

    public function view(User $user, Event $event): bool
    {
        return $this->canAccess($user, $event);
    }

    public function create(User $user): bool
    {
        // hanya SUPER_ADMIN & ADMIN yang bisa create event baru
        return $user->isAdmin();
    }

    public function update(User $user, Event $event): bool
    {
        return $this->canAccess($user, $event);
    }

    public function delete(User $user, Event $event): bool
    {
        // delete hanya admin ke atas
        return $user->isAdmin();
    }

    public function restore(User $user, Event $event): bool
    {
        return $user->isAdmin();
    }

    public function forceDelete(User $user, Event $event): bool
    {
        return $user->isSuperAdmin();
    }
}
