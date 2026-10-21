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
        // Crear el Agente de Soporte principal
        User::factory()->create([
            'name' => 'Agente de Soporte',
            'email' => 'admin@smartticket.com',
            'password' => Hash::make('password'),
        ]);
    }
}
