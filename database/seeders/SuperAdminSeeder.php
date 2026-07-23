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
            'name' => 'Juan Arango',
            'email' => 'jcarango98@gmail.com',
            'password' => bcrypt('Albion21'),
            'email_verified_at' => now(),
        ];

        $superAdmin = User::updateOrCreate(
            ['id' => 1],
            $userData
        );

        if (!$superAdmin->hasRole('super-admin')) {
            $superAdmin->assignRole($role);
        }
    }
}