<?php

namespace App\Policies;

use App\Models${rel};
use App\Models\User;
use App\Policies\Concerns\ScopedByEvent;

class ItineraryItemPolicy
{
    use ScopedByEvent;

    public function viewAny(User $user): bool { return true; }
    public function view(User $user, ItineraryItem $record): bool { return $this->canAccess($user, $record); }
    public function create(User $user): bool { return true; }
    public function update(User $user, ItineraryItem $record): bool { return $this->canAccess($user, $record); }
    public function delete(User $user, ItineraryItem $record): bool { return $this->canAccess($user, $record); }
}
