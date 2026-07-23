<?php

namespace App\Policies;

use App\Models\E14conteo;
use App\Models\User;
use Illuminate\Auth\Access\Response;

class E14conteoPolicy
{
    public function viewAny(User $user): bool
    {
        return $user->hasPermissionTo('e14conteos.ver');
    }

    public function view(User $user, E14conteo $e14conteo): bool
    {
        return $user->hasPermissionTo('e14conteos.ver');
    }

    public function create(User $user): bool
    {
        return $user->hasPermissionTo('e14conteos.crear');
    }

    public function update(User $user, E14conteo $e14conteo): bool
    {
        // Si es un testigo, solo puede editar lo propio
        if ($user->hasRole(['Testigo Electoral', 'testigo-electoral', 'Testigo'])) {
            return $user->id === $e14conteo->user_id && $user->hasPermissionTo('e14conteos.editar');
        }

        return $user->hasPermissionTo('e14conteos.editar');
    }

    public function delete(User $user, E14conteo $e14conteo): bool
    {
        return $user->hasPermissionTo('e14conteos.eliminar');
    }

    public function restore(User $user, E14conteo $e14conteo): bool
    {
        return $user->hasPermissionTo('e14conteos.eliminar');
    }

    public function forceDelete(User $user, E14conteo $e14conteo): bool
    {
        return $user->hasPermissionTo('e14conteos.eliminar');
    }
}
