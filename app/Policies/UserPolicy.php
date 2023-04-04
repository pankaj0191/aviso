<?php

namespace App\Policies;

use App\User;
use App\Policies\Policy;
use Illuminate\Auth\Access\HandlesAuthorization;

class UserPolicy extends Policy
{
    use HandlesAuthorization;

    /**
     * The Permission key the Policy corresponds to.
     *
     * @var string
     */
    public static $key = 'users';

    /**
     * Determine whether the user can create models.
     *
     * @param  \App\User $user
     * @return mixed
     */
    public function create(User $user)
    {
        return $user->hasPermissionTo('Create ' . static::$key);
    }

    /**
     * Determine whether the user can delete the model.
     *
     * @param  \App\User $user
     * @return mixed
     */
    public function delete(User $user, $model)
    {
        if ($user->isSuperAdmin()) {
            return true;
        }

        if ($user->hasPermissionTo('Delete own ' . static::$key)) {
            return $user->id == $model->id;
        }

        return $user->hasPermissionTo('Delete ' . static::$key);
    }

    /**
     * Determine whether the user can permanently delete the model.
     *
     * @param  \App\User $user
     * @return mixed
     */
    public function forceDelete(User $user, $model)
    {
        if ($user->isSuperAdmin()) {
            return true;
        }

        if ($user->hasPermissionTo('ForceDelete own ' . static::$key)) {
            return $user->id == $model->id;
        }

        return $user->hasPermissionTo('ForceDelete ' . static::$key);
    }

    /**
     * Determine whether the user can restore the model.
     *
     * @param  \App\User $user
     * @return mixed
     */
    public function restore(User $user, $model)
    {
        if ($user->isSuperAdmin()) {
            return true;
        }

        if ($user->hasPermissionTo('Restore own ' . static::$key)) {
            return $user->id == $model->id;
        }

        return $user->hasPermissionTo('Restore ' . static::$key);
    }

    /**
     * Determine whether the user can update the model.
     *
     * @param  \App\User $user
     * @return mixed
     */
    public function update(User $user, $model)
    {
        if ($user->isSuperAdmin()) {
            return true;
        }

        if ($user->hasPermissionTo('Update own ' . static::$key)) {
            return $user->id == $model->id;
        }

        return $user->hasPermissionTo('Update ' . static::$key);
    }

    /**
     * Determine whether the user can view the model.
     *
     * @param  \App\User $user
     * @return mixed
     */
    public function view(User $user, $model)
    {
        if ($user->isSuperAdmin()) {
            return true;
        }

        if ($user->hasPermissionTo('Read own ' . static::$key)) {
            return $user->id == $model->id;
        }

        return $user->hasPermissionTo('Read ' . static::$key);
    }

    /**
     * @param User $user
     */
    public function viewAny(User $user)
    {
        return $user->hasPermissionTo('Browes ' . static::$key);
    }
}