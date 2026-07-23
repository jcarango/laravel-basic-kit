<?php

namespace App\Policies;

use App\Models\User;
use Illuminate\Auth\Access\Response;

class UserPolicy
{
    public function viewAny(User $user): bool
    {
        return $user->hasPermissionTo('usuarios.ver');
    }

    public function view(User $user, User $model): bool
    {
        return $user->hasPermissionTo('usuarios.ver');
    }

    public function create(User $user): bool
    {
        return $user->hasPermissionTo('usuarios.crear');
    }

    public function update(User $user, User $model): bool
    {
        return $user->hasPermissionTo('usuarios.editar');
    }

    public function delete(User $user, User $model): bool
    {
        // Don't allow deleting self or super admin
        if ($user->id === $model->id || $model->id === 1) {
            return false;
        }

        return $user->hasPermissionTo('usuarios.eliminar');
    }

    public function restore(User $user, User $model): bool
    {
        return $user->hasPermissionTo('usuarios.eliminar');
    }

    public function forceDelete(User $user, User $model): bool
    {
        return $user->hasPermissionTo('usuarios.eliminar');
    }
}
