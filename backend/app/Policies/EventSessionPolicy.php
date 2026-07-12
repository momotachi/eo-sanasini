<?php

namespace App\Policies;

use App\Models${rel};
use App\Models\User;
use App\Policies\Concerns\ScopedByEvent;

class EventSessionPolicy
{
    use ScopedByEvent;

    public function viewAny(User $user): bool { return true; }
    public function view(User $user, EventSession $record): bool { return $this->canAccess($user, $record); }
    public function create(User $user): bool { return true; }
    public function update(User $user, EventSession $record): bool { return $this->canAccess($user, $record); }
    public function delete(User $user, EventSession $record): bool { return $this->canAccess($user, $record); }
}
