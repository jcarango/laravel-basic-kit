<?php

namespace App\Policies;

use App\Models\Suffragan;
use App\Models\User;
use Illuminate\Auth\Access\Response;

class SuffraganPolicy
{
    public function viewAny(User $user): bool
    {
        return $user->hasPermissionTo('sufragantes.ver');
    }

    public function view(User $user, Suffragan $suffragan): bool
    {
        return $user->hasPermissionTo('sufragantes.ver');
    }

    public function create(User $user): bool
    {
        return $user->hasPermissionTo('sufragantes.crear');
    }

    public function update(User $user, Suffragan $suffragan): bool
    {
        return $user->hasPermissionTo('sufragantes.editar');
    }

    public function delete(User $user, Suffragan $suffragan): bool
    {
        return $user->hasPermissionTo('sufragantes.eliminar');
    }

    public function restore(User $user, Suffragan $suffragan): bool
    {
        return $user->hasPermissionTo('sufragantes.eliminar');
    }

    public function forceDelete(User $user, Suffragan $suffragan): bool
    {
        return $user->hasPermissionTo('sufragantes.eliminar');
    }
}
