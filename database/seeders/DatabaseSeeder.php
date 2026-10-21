<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        // 1. Crear el usuario administrador (Directo para evitar errores en prod)
        $admin = \App\Models\User::create([
            'name' => 'Agente de Soporte',
            'email' => 'admin@smartticket.com',
            'password' => \Illuminate\Support\Facades\Hash::make('password'),
        ]);

        // 2. Generar 25 tickets variados
        $tickets = \App\Models\Ticket::factory(25)->create();

        // 3. Agregar notas aleatorias a algunos tickets
        foreach ($tickets as $ticket) {
            if (rand(0, 1)) { // 50% de probabilidad de tener notas
                \App\Models\TicketNote::factory(rand(1, 3))->create([
                    'ticket_id' => $ticket->id,
                    'user_id' => $admin->id,
                ]);
            }
        }
    }
}
