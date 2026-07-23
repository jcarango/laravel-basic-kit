<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\User;
use Illuminate\Support\Facades\Hash;
use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;
use Illuminate\Support\Str;

class UserRolePermissionSeeder extends Seeder
{
    public function run(): void
    {
        // 🔄 Limpia la caché de permisos
        app()[\Spatie\Permission\PermissionRegistrar::class]->forgetCachedPermissions();

        // 🧩 Módulos y acciones definidas
        $modules = [
            'usuarios'    => ['ver', 'crear', 'editar', 'eliminar'],
            'roles'       => ['ver', 'crear', 'editar', 'eliminar', 'asignar'],
            'permisos'    => ['ver', 'crear', 'editar', 'eliminar'],
            'dashboard'   => ['ver'],
            'sufragantes' => ['ver', 'crear', 'editar', 'eliminar'],
            'candidatos'  => ['ver', 'crear', 'editar', 'eliminar'],
            'eventos'     => ['ver', 'crear', 'editar', 'eliminar'],
            'e14conteos'  => ['ver', 'crear', 'editar', 'eliminar'],
        ];

        // 🔐 Crear todos los permisos
        $allPermissions = collect();
        foreach ($modules as $module => $actions) {
            foreach ($actions as $action) {
                $permName = "{$module}.{$action}";
                $perm = Permission::firstOrCreate(['name' => $permName]);
                $allPermissions->push($perm);
            }
        }

        // 🧱 Crear roles
        $roles = [
            'super-admin' => [],
            'coordinador' => [
                'usuarios.ver', 'usuarios.crear', 'usuarios.editar',
                'sufragantes.ver', 'sufragantes.crear', 'sufragantes.editar', 'sufragantes.eliminar',
                'dashboard.ver',
                'e14conteos.ver', 'e14conteos.crear', 'e14conteos.editar',
            ],
            'lider'       => [
                'sufragantes.ver', 'sufragantes.crear', 'sufragantes.editar',
                'dashboard.ver',
                'e14conteos.ver',
            ],
            'digitador'   => [
                'sufragantes.ver', 'sufragantes.crear',
                'dashboard.ver',
                'e14conteos.ver', 'e14conteos.crear', 'e14conteos.editar',
            ],
            'testigo-electoral' => [
                'dashboard.ver',
                'e14conteos.ver', 'e14conteos.crear', 'e14conteos.editar',
            ],
        ];

        foreach ($roles as $roleName => $permissions) {
            $role = Role::firstOrCreate(['name' => $roleName]);

            if ($roleName === 'super-admin') {
                $role->syncPermissions(Permission::all());
            } else {
                $role->syncPermissions(Permission::whereIn('name', $permissions)->get());
            }
        }

        // 👤 Crear usuarios
        $defaultUsers = [
            [
                'name'     => 'Juan',
                'lastname' => 'Arango',
                'email'    => 'jcarango98@gmail.com',
                'password' => Hash::make('Albion21'),
                'role'     => 'super-admin',
            ],
            [
                'name'     => 'Coordinador',
                'lastname' => 'Demo',
                'email'    => 'coordinador@example.com',
                'password' => Hash::make('password'),
                'role'     => 'coordinador',
            ],
            [
                'name'     => 'Lider',
                'lastname' => 'Demo',
                'email'    => 'lider@example.com',
                'password' => Hash::make('password'),
                'role'     => 'lider',
            ],
            [
                'name'     => 'Digitador',
                'lastname' => 'Demo',
                'email'    => 'digitador@example.com',
                'password' => Hash::make('password'),
                'role'     => 'digitador',
            ],
        ];

        foreach ($defaultUsers as $data) {
            $user = User::firstOrCreate(
                ['email' => $data['email']],
                [
                    'name'     => $data['name'],
                    'lastname' => $data['lastname'],
                    'password' => $data['password'],
                    'phone'    => '3000000000',
                    'is_active'=> true,
                ]
            );

            // Evitar duplicar roles si ya lo tiene
            if (!$user->hasRole($data['role'])) {
                $user->assignRole($data['role']);
            }
        }
    }
}
