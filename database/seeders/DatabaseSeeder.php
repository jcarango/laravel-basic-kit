<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        // Llamar a los seeders en el orden adecuado
        $this->call([
            ColombiaLocationSeeder::class,
            SuperAdminSeeder::class,
        ]);
    }
}