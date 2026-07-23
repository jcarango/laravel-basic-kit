<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\User;
use App\Models\Suffragan;
use App\Models\Event;
use Illuminate\Support\Facades\Hash;
use Faker\Factory as Faker;

class DemoDataSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $faker = Faker::create('es_CO');
        
        // 1. Crear Líderes de Muestra
        $leaders = [];
        for ($i = 1; $i <= 3; $i++) {
            $user = User::firstOrCreate(
                ['email' => "lider{$i}@demo.com"],
                [
                    'name' => "Líder {$i}",
                    'password' => Hash::make('password123'), // password123
                    'monthly_goal' => rand(20, 100),
                ]
            );
            
            // Asignar el rol lider si existe, usamos syncRoles
            if (\Spatie\Permission\Models\Role::where('name', 'lider')->exists()) {
                $user->assignRole('lider');
            }
            $leaders[] = $user;
        }

        // 2. Crear Evento de Muestra
        $event = Event::firstOrCreate(
            ['name' => 'Gran Asamblea General de la Campaña'],
            [
                'color' => '#1d4ed8', // blue-700
                'description' => 'Reunión principal para presentar métricas de crecimiento y organizar la estrategia del Día D.',
                'starts_at' => now()->addDays(2)->setTime(14, 0),
                'ends_at' => now()->addDays(2)->setTime(18, 0),
            ]
        );

        // 3. Crear Sufragantes de Muestra con ubicaciones en Medellín
        // Medellín bounds approx: Lat 6.21 to 6.28, Lng -75.60 to -75.54
        $voterTypes = ['Duro', 'Blando', 'Opinión'];
        $docTypes = ['CC', 'CE', 'TI'];
        
        foreach ($leaders as $leader) {
            // Cada líder tiene entre 10 y 25 sufragantes
            $numSuffragans = rand(10, 25);
            
            for ($j = 0; $j < $numSuffragans; $j++) {
                $lat = $faker->randomFloat(6, 6.21, 6.28);
                $lng = $faker->randomFloat(6, -75.60, -75.54);
                
                $suffragan = Suffragan::create([
                    'name' => $faker->firstName,
                    'lastname' => $faker->lastName,
                    'phone' => '3' . $faker->numerify('#########'), // 10 digits starting with 3
                    'documentationtype' => $faker->randomElement($docTypes),
                    'documentationnumber' => $faker->unique()->numerify('##########'), // 10 dígitos únicos
                    'email' => $faker->unique()->safeEmail,
                    'address' => "Calle " . rand(1, 100) . " #" . rand(1, 100) . "-" . rand(1, 100) . ", Medellín",
                    'voter_type' => $faker->randomElement($voterTypes),
                    'latitude' => $lat,
                    'longitude' => $lng,
                    'user_id' => $leader->id, // Asignado a este líder
                    'profession' => $faker->jobTitle,
                ]);

                // 4. Asignar asistencia al azar al evento creado (30% de probabilidad)
                if (rand(1, 100) <= 30) {
                    $event->suffragans()->syncWithoutDetaching([
                        $suffragan->id => ['attended_at' => now()->subMinutes(rand(1, 1440))]
                    ]);
                }
            }
        }
        
        $this->command->info('Datos de muestra generados exitosamente.');
    }
}
