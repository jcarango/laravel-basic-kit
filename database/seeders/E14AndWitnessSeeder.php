<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\User;
use App\Models\Suffragan;
use App\Models\Divipol;
use App\Models\E14conteo;
use App\Models\Candidate;
use Faker\Factory as Faker;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\DB;

class E14AndWitnessSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $faker = Faker::create('es_CO');
        
        $this->command->info('Creando testigos electorales y E-14 (conteos)...');

        // Check if there is at least one candidate, if not create one
        $candidate = Candidate::first() ?? Candidate::create([
            'name' => 'Candidato',
            'lastname' => 'Demo',
            'slogan' => 'Por un mejor futuro',
            'email' => 'candidatodemo@example.com',
            'phone' => '3000000000',
            'address' => 'Medellín',
            'doc_type' => 'CC',
            'doc_number' => '1234567890',
        ]);

        // Asegurarnos de tener un Usuario para asociar
        $user = User::first() ?? User::create([
            'name' => 'Admin Lider',
            'email' => 'lider1@demo.com',
            'password' => Hash::make('password123'),
        ]);

        // Crear Testigos Electorales (Suffragans con is_witness = true)
        $witnesses = [];
        for ($i = 0; $i < 5; $i++) {
            $witness = Suffragan::create([
                'name' => $faker->firstName,
                'lastname' => $faker->lastName,
                'phone' => '3' . $faker->numerify('#########'),
                'documentationtype' => 'CC',
                'documentationnumber' => $faker->unique()->numerify('##########'),
                'email' => $faker->unique()->safeEmail,
                'address' => "Calle " . rand(1, 100) . " #" . rand(1, 100) . "-" . rand(1, 100) . ", Medellín",
                'voter_type' => 'Duro', // O 'Testigo Electoral' si lo añadiste a los tipos
                'is_witness' => true,
                'user_id' => $user->id,
                'latitude' => $faker->randomFloat(6, 6.21, 6.28),
                'longitude' => $faker->randomFloat(6, -75.60, -75.54),
            ]);
            $witnesses[] = $witness;
        }

        // Recuperar un puesto de votacion (Divipol) cualquiera, o crear uno
        $divipol = Divipol::first() ?? Divipol::create([
            'cod_departamento' => '05',
            'departamento' => 'ANTIOQUIA',
            'cod_municipio' => '001',
            'municipio' => 'MEDELLIN',
            'cod_zona' => '01',
            'cod_puesto' => '01',
            'nom_puesto' => 'INSTITUCION EDUCATIVA DEMO',
            'direccion' => 'CALLE DEMO',
            'mujeres' => 100,
            'hombres' => 100,
            'total' => 200,
            'mesas' => 5
        ]);

        // Crear E-14 (E14conteo)
        foreach ($witnesses as $witness) {
            $totalMesa = rand(50, 100);
            $votosCandidato = rand(10, $totalMesa - 5);
            $votosBlanco = rand(1, 5);
            $votosNulos = rand(1, 3);
            $votosNoMarcados = $totalMesa - $votosCandidato - $votosBlanco - $votosNulos;

            $e14 = E14conteo::create([
                'divipol_id' => $divipol->id,
                'mesa' => rand(1, $divipol->mesas),
                'user_id' => $user->id,
                'total_sufragantes_e11' => $totalMesa,
                'total_votos_urna' => $totalMesa,
                'total_votos_incinerados' => rand(0, 5),
                'votos_en_blanco' => $votosBlanco,
                'votos_nulos' => $votosNulos,
                'votos_no_marcados' => $votosNoMarcados,
                'hubo_reconteo' => false,
            ]);

            // Asociar los votos del candidato a esta mesa en la tabla intermedia candidate_e14conteo
            // El Resource usa la relacion candidates() con el pivot
            DB::table('candidate_e14conteo')->insert([
                'candidate_id' => $candidate->id,
                'e14conteo_id' => $e14->id,
                'votos' => $votosCandidato,
                'created_at' => now(),
                'updated_at' => now(),
            ]);
        }

        $this->command->info('Testigos Electorales y reportes E-14 han sido generados.');
    }
}
