<?php

namespace App\Policies;

use App\Models\Medal;
use App\Models\User;
use App\Policies\Concerns\ScopedByEvent;

class MedalPolicy
{
    use ScopedByEvent;

    public function viewAny(User $user): bool { return true; }
    public function view(User $user, Medal $record): bool { return $this->canAccess($user, $record); }
    public function create(User $user): bool { return true; }
    public function update(User $user, Medal $record): bool { return $this->canAccess($user, $record); }
    public function delete(User $user, Medal $record): bool { return $this->canAccess($user, $record); }
}
