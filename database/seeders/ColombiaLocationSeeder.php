<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\Country;
use App\Models\State;
use App\Models\City;

class ColombiaLocationSeeder extends Seeder
{
    public function run(): void
    {
        $colombia = Country::create(['name' => 'Colombia']);

        $antioquia = State::create(['name' => 'Antioquia', 'country_id' => $colombia->id]);
        $cundinamarca = State::create(['name' => 'Cundinamarca', 'country_id' => $colombia->id]);
        $valle = State::create(['name' => 'Valle del Cauca', 'country_id' => $colombia->id]);

        City::insert([
            ['name' => 'Medellín', 'state_id' => $antioquia->id],
            ['name' => 'Envigado', 'state_id' => $antioquia->id],
            ['name' => 'Bello', 'state_id' => $antioquia->id],

            ['name' => 'Bogotá', 'state_id' => $cundinamarca->id],
            ['name' => 'Soacha', 'state_id' => $cundinamarca->id],

            ['name' => 'Cali', 'state_id' => $valle->id],
            ['name' => 'Palmira', 'state_id' => $valle->id],
        ]);
    }
}