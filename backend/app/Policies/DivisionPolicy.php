<?php

namespace App\Policies;

use App\Models\Division;
use App\Models\User;
use App\Policies\Concerns\ScopedByEvent;

class DivisionPolicy
{
    use ScopedByEvent;

    public function viewAny(User $user): bool { return true; }
    public function view(User $user, Division $record): bool { return $this->canAccess($user, $record); }
    public function create(User $user): bool { return true; }
    public function update(User $user, Division $record): bool { return $this->canAccess($user, $record); }
    public function delete(User $user, Division $record): bool { return $this->canAccess($user, $record); }
}
