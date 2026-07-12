<?php

namespace App\Policies;

use App\Models\Participant;
use App\Models\User;
use App\Policies\Concerns\ScopedByEvent;

class ParticipantPolicy
{
    use ScopedByEvent;

    public function viewAny(User $user): bool { return true; }
    public function view(User $user, Participant $record): bool { return $this->canAccess($user, $record); }
    public function create(User $user): bool { return true; }
    public function update(User $user, Participant $record): bool { return $this->canAccess($user, $record); }
    public function delete(User $user, Participant $record): bool { return $this->canAccess($user, $record); }
}
