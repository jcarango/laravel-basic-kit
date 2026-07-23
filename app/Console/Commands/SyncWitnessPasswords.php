<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\User;
use App\Models\Suffragan;
use Illuminate\Support\Facades\Hash;
use Spatie\Permission\Models\Role;

class SyncWitnessPasswords extends Command
{
    protected $signature = 'witness:sync-passwords';
    protected $description = 'Set witness passwords to their phone numbers and create user accounts for suffragan witnesses';

    public function handle()
    {
        // 1. Buscar en la tabla de Sufragantes a todos los marcados como testigos
        $suffragans = Suffragan::where('is_witness', true)->get();

        if ($suffragans->isEmpty()) {
            $this->info('No se encontraron Testigos en la tabla de sufragantes.');
        } else {
            foreach ($suffragans as $suffragan) {
                if (!$suffragan->phone) {
                    $this->warn("El sufragante {$suffragan->name} no tiene número de teléfono. Saltando...");
                    continue;
                }

                // Limpiar el teléfono para usarlo como login
                $cleanPhone = preg_replace('/[^0-9]/', '', $suffragan->phone);
                
                // Buscar si ya tiene usuario vinculado
                $user = $suffragan->user;

                if (!$user) {
                    // Si no tiene vínculo, buscar por teléfono o email para evitar duplicados
                    $user = User::where('phone', $cleanPhone)->first();
                    if (!$user && $suffragan->email) {
                        $user = User::where('email', $suffragan->email)->first();
                    }

                    if (!$user) {
                        // Crear el usuario si no existe
                        $user = User::create([
                            'name' => $suffragan->name,
                            'lastname' => $suffragan->lastname ?? '',
                            'email' => $suffragan->email ?? "{$cleanPhone}@testigo.com",
                            'phone' => $cleanPhone,
                            'password' => Hash::make($cleanPhone),
                            'is_active' => true,
                        ]);
                        $this->info("Cuenta de usuario CREADA para: {$suffragan->name} ({$cleanPhone})");
                    }

                    // Vincular el sufragante con el nuevo o existente usuario
                    $suffragan->user_id = $user->id;
                    $suffragan->save();
                }

                // 2. Asegurar que tenga el rol de testigo electoral
                if (!$user->hasAnyRole(['Testigo', 'Testigo Electoral', 'testigo-electoral'])) {
                    $user->assignRole('testigo-electoral');
                    $this->info("Rol asignado a: {$user->name}");
                }

                // 3. Sincronizar contraseña y teléfono limpio
                $user->update([
                    'phone' => $cleanPhone,
                    'password' => Hash::make($cleanPhone),
                ]);

                $this->info("Acceso sincronizado para: {$user->name} (Cel: {$cleanPhone})");
            }
        }

        // 4. Sincronizar cualquier usuario que ya tenga el rol aunque no sea sufragante
        $usersWithRole = User::role(['Testigo', 'Testigo Electoral', 'testigo-electoral'])->get();
        foreach ($usersWithRole as $u) {
            if ($u->phone) {
                $clean = preg_replace('/[^0-9]/', '', $u->phone);
                $u->password = Hash::make($clean);
                $u->save();
            }
        }

        $this->info('Sincronización masiva de testigos completada con éxito.');
    }
}
