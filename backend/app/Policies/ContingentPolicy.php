<?php

namespace App\Policies;

use App\Models\Contingent;
use App\Models\User;
use App\Policies\Concerns\ScopedByEvent;

class ContingentPolicy
{
    use ScopedByEvent;

    public function viewAny(User $user): bool { return true; }
    public function view(User $user, Contingent $record): bool { return $this->canAccess($user, $record); }
    public function create(User $user): bool { return true; }
    public function update(User $user, Contingent $record): bool { return $this->canAccess($user, $record); }
    public function delete(User $user, Contingent $record): bool { return $this->canAccess($user, $record); }
}
