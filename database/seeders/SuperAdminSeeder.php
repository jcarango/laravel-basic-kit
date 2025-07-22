<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Role;

class SuperAdminSeeder extends Seeder
{
    public function run(): void
    {
        $role = Role::firstOrCreate(['name' => 'Super Admin']);

        $userData = [
            'name' => 'Super Administrador',
            'email' => 'admin@sistema.com',
            'password' => bcrypt('Admin123!'),
            'email_verified_at' => now(),
        ];

        $superAdmin = User::updateOrCreate(
            ['id' => 1],
            $userData
        );

        if (!$superAdmin->hasRole('Super Admin')) {
            $superAdmin->assignRole($role);
        }
    }
}