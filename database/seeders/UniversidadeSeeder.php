<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class UniversidadeSeeder extends Seeder
{
    public function run(): void
    {
        $email = trim((string) config('services.universidade_admin.email'));
        $password = (string) config('services.universidade_admin.password');
        $name = trim((string) config('services.universidade_admin.name'));

        if ($email === '' || $password === '') {
            $this->command?->warn('Administrador não criado: configure UNIVERSIDADE_ADMIN_EMAIL e UNIVERSIDADE_ADMIN_PASSWORD.');

            return;
        }

        User::updateOrCreate(
            ['email' => $email],
            [
                'name' => $name !== '' ? $name : 'Administrador',
                'password' => Hash::make($password),
                'role_id' => 'admin',
                'provedor_id' => null,
                'ativo' => true,
            ],
        );

        $this->command?->info("Administrador institucional configurado: {$email}");
    }
}
