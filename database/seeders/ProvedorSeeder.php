<?php

namespace Database\Seeders;

use App\Models\Provedor;
use Illuminate\Database\Seeder;

class ProvedorSeeder extends Seeder
{
    public function run(): void
    {
        $dados = [
            [
                'id' => 1,
                'nome' => 'Gabinete do Vice-Reitor',
                'email' => config('services.provedores.gabinete_vice_reitor_email'),
            ],
            [
                'id' => 2,
                'nome' => 'Provedor do Estudante',
                'email' => config('services.provedores.provedor_estudante_email'),
            ],
            [
                'id' => 3,
                'nome' => 'Outro',
                'email' => config('services.provedores.outro_email'),
            ],
        ];

        foreach ($dados as $p) {
            $email = trim((string) $p['email']);
            unset($p['email']);

            $atributos = $p + ['ativo' => true];

            if ($email !== '') {
                $atributos['email'] = $email;
            }

            Provedor::updateOrCreate(['id' => $p['id']], $atributos);
        }
    }
}
