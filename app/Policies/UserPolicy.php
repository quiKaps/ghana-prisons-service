<?php

namespace App\Policies;

use App\Models\User;
use Illuminate\Auth\Access\Response;

class UserPolicy
{
    /**
     * Determine whether the user can view any models.
     */
    public function viewAny(User $user): bool
    {
        return in_array($user->user_type, ['super_admin', 'hq_admin', 'prison_admin']);
    }

    /**
     * Determine whether the user can view the model.
     */
    public function view(User $user, User $model): bool
    {
        if ($user->user_type === 'officer') {
            return $user->id === $model->id;
        }

        return in_array($user->user_type, ['super_admin', 'hq_admin', 'prison_admin']);
    }

    /**
     * Determine whether the user can create models.
     */
    public function create(User $user): bool
    {
        return in_array($user->user_type, ['super_admin', 'prison_admin']);
    }

    /**
     * Determine whether the user can update the model.
     */
    public function update(User $user, User $model): bool
    {
        if ($user->user_type === 'officer') {
            return $user->id === $model->id;
        }

        return in_array($user->user_type, ['super_admin', 'prison_admin']);
    }

    /**
     * Determine whether the user can delete the model.
     */
    public function delete(User $user, User $model): bool
    {
        return in_array($user->user_type, ['super_admin', 'prison_admin']);
    }

    /**
     * Determine whether the user can restore the model.
     */
    public function restore(User $user, User $model): bool
    {
        return in_array($user->user_type, ['super_admin', 'prison_admin']);
    }

    /**
     * Determine whether the user can permanently delete the model.
     */
    public function forceDelete(User $user, User $model): bool
    {
        return in_array($user->user_type, ['super_admin', 'prison_admin']);
    }
}
