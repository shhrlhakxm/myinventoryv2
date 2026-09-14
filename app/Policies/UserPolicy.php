<?php

namespace App\Policies;

use App\Models\User;

class UserPolicy
{
    public function viewAny(User $actor): bool
    {
        return $actor->isAdmin();
    }

    public function create(User $actor): bool
    {
        return $actor->isAdmin();
    }

    public function delete(User $actor, User $target): bool
    {
        if ($target->isSuperAdmin()) {
            return false;
        }

        if ($actor->id === $target->id) {
            return false;
        }

        if ($actor->isSuperAdmin()) {
            return true;
        }

        return $actor->isAdmin() && $target->isStaff();
    }

    public function deleteOwnAccount(User $actor, User $target): bool
    {
        return $actor->id === $target->id && ! $target->isSuperAdmin();
    }

    public function updateRole(User $actor, User $target): bool
    {
        if ($target->isSuperAdmin()) {
            return false;
        }

        if ($actor->id === $target->id) {
            return false;
        }

        return $actor->isAdmin();
    }
}
