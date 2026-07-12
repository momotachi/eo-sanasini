<?php

namespace App\Policies;

use App\Models\Venue;
use App\Models\User;
use App\Policies\Concerns\ScopedByEvent;

class VenuePolicy
{
    use ScopedByEvent;

    public function viewAny(User $user): bool { return true; }
    public function view(User $user, Venue $record): bool { return $this->canAccess($user, $record); }
    public function create(User $user): bool { return true; }
    public function update(User $user, Venue $record): bool { return $this->canAccess($user, $record); }
    public function delete(User $user, Venue $record): bool { return $this->canAccess($user, $record); }
}
