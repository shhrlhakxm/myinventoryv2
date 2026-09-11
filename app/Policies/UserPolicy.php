<?php

namespace App\Policies;

use App\Models\User;
use Illuminate\Auth\Access\Response;

class UserPolicy
{

    public function before(User $user, $ability)
    {
        if ($user->isSuperAdmin()) {
            return true;
        }

        return null;
    }
    /**
     * Determine whether the user can view any models.
     */
    public function viewAny(User $actor): bool
    {
        return $actor()->isAdmin();
    }

    /**
     * Determine whether the user can view the model.
     */
    public function view(User $user, User $model): bool
    {
        return false;
    }

    /**
     * Determine whether the user can create models.
     */
    public function create(User $user): bool
    {
        return false;
    }

    /**
     * Determine whether the user can update the model.
     */
    public function updateRole(User $actor, User $target): bool
    {
        if ($target->isSuperAdmin()) {
            return false; // role superadmin immutable via UI
        }

        if ($actor->id === $target->id && !$actor->isSuperAdmin()) {
            return false; // admin tak boleh turunkan diri sendiri jadi staff
        }

        return $actor->isAdmin();
    }

    /**
     * Determine whether the user can delete the model.
     */
    public function delete(User $actor, User $target): bool
    {
        if ($target->isSuperAdmin()) {
            return false; // tak boleh delete superadmin, walau siapa pun actor
        }

        if ($actor->id === $target->id) {
            return false; // jangan benarkan admin delete diri sendiri
        }

        if ($actor->isSuperAdmin()) {
            return true; // dah settle oleh before(), tapi explicit untuk kejelasan
        }

        // admin biasa cuma boleh delete staff, bukan admin lain
        return $target->isStaff();
    }

    /**
     * Determine whether the user can restore the model.
     */
    public function restore(User $user, User $model): bool
    {
        return false;
    }

    /**
     * Determine whether the user can permanently delete the model.
     */
    public function forceDelete(User $user, User $model): bool
    {
        return false;
    }
}
