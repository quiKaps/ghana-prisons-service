<?php

namespace App\Policies;

use App\Models\Cell;
use App\Models\User;
use Illuminate\Auth\Access\Response;

class CellPolicy
{
    /**
     * Determine whether the user can view any models.
     */
    public function viewAny(User $user): bool
    {
        return $user->user_type === 'prison_admin';
    }

    /**
     * Determine whether the user can view the model.
     */
    public function view(User $user, Cell $cell): bool
    {
        return $user->user_type === 'prison_admin';
    }

    /**
     * Determine whether the user can create models.
     */
    public function create(User $user): bool
    {
        return $user->user_type === 'prison_admin';
    }

    /**
     * Determine whether the user can update the model.
     */
    public function update(User $user, Cell $cell): bool
    {
        return $user->user_type === 'prison_admin';
    }

    /**
     * Determine whether the user can delete the model.
     */
    public function delete(User $user, Cell $cell): bool
    {
        return $user->user_type === 'prison_admin';
    }

    /**
     * Determine whether the user can restore the model.
     */
    public function restore(User $user, Cell $cell): bool
    {
        return $user->user_type === 'prison_admin';
    }

    /**
     * Determine whether the user can permanently delete the model.
     */
    public function forceDelete(User $user, Cell $cell): bool
    {
        return $user->user_type === 'prison_admin';
    }
}
