<?php

namespace App\Policies;

use App\Models\ScheduleItem;
use App\Models\User;
use App\Policies\Concerns\ScopedByEvent;

class ScheduleItemPolicy
{
    use ScopedByEvent;

    public function viewAny(User $user): bool { return true; }
    public function view(User $user, ScheduleItem $record): bool { return $this->canAccess($user, $record); }
    public function create(User $user): bool { return true; }
    public function update(User $user, ScheduleItem $record): bool { return $this->canAccess($user, $record); }
    public function delete(User $user, ScheduleItem $record): bool { return $this->canAccess($user, $record); }
}
