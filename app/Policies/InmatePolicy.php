<?php

namespace App\Policies;

use App\Models\Inmate;
use App\Models\User;
use Illuminate\Auth\Access\Response;

class InmatePolicy
{
    /**
     * Determine whether the user can view any models.
     */
    public function viewAny(User $user): bool
    {
        return true; //$user->user_type === 'prison_admin';
    }

    /**
     * Determine whether the user can view the model.
     */
    public function view(User $user, Inmate $inmate): bool
    {
        return in_array($user->user_type, ['prison_admin', 'officer']);
    }

    /**
     * Determine whether the user can create models.
     */
    public function create(User $user): bool
    {
        return in_array($user->user_type, ['prison_admin', 'officer']);
    }

    /**
     * Determine whether the user can update the model.
     */
    public function update(User $user, Inmate $inmate): bool
    {
        return $user->user_type === 'prison_admin';
    }

    /**
     * Determine whether the user can delete the model.
     */
    public function delete(User $user, Inmate $inmate): bool
    {
        return $user->user_type === 'prison_admin';
    }

    /**
     * Determine whether the user can restore the model.
     */
    public function restore(User $user, Inmate $inmate): bool
    {
        return $user->user_type === 'prison_admin';
    }

    /**
     * Determine whether the user can permanently delete the model.
     */
    public function forceDelete(User $user, Inmate $inmate): bool
    {
        return $user->user_type === 'prison_admin';
    }
}
