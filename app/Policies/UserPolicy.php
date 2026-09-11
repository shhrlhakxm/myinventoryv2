<?php

namespace App\Policies;

use App\Models\User;

class UserPolicy
{
    /**
     * Superadmin can do anything — this shortcut runs BEFORE
     * any other method in this policy (Laravel's "before" hook).
     */
    public function before(User $actor, string $ability): ?bool
    {
        if ($actor->isSuperAdmin()) {
            return true;
        }

        return null; // fall through to the specific method below
    }

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
            return false; // superadmin can never be deleted
        }

        if ($actor->id === $target->id) {
            return false; // prevent self-deletion
        }

        if ($actor->isSuperAdmin()) {
            return true; // already handled by before(), explicit for clarity
        }

        return $target->isStaff(); // regular admin can only delete staff
    }

    public function updateRole(User $actor, User $target): bool
    {
        if ($target->isSuperAdmin()) {
            return false; // superadmin role is immutable via UI
        }

        if ($actor->id === $target->id && !$actor->isSuperAdmin()) {
            return false; // admin can't demote themselves
        }

        return $actor->isAdmin();
    }
}