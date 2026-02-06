<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        // 1. Crear al Administrador Principal
        User::create([
            'name' => 'Administrador', // O tu nombre
            'email' => 'admin@admin.com', // El correo con el que entrarás
            'password' => Hash::make('password'), // Tu contraseña inicial
            'role' => 'admin',
            'is_active' => true,
            'wallet_balance' => 0,
        ]);

        // 2. Crear un Cobrador de Prueba
        User::create([
            'name' => 'Cobrador Prueba',
            'email' => 'cobrador@test.com',
            'password' => Hash::make('password'),
            'role' => 'collector',
            'is_active' => true,
            'wallet_balance' => 0,
        ]);
    }
}
