<?php

namespace Database\Seeders;

use App\Models\Especialidade;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class EspecialidadeSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $especialidades = [
            'Diarista',
            'Montador',
            'Designer',
            'Pedreiro',
            'Pintor',
            'Decorador',
            'Faxineiro',
            'Marceneiro',
            'Vidraceiro',
            'Limpeza de Piscina',
            'Desentupidor',
            'Eletricista',     // Adicionado
            'Encanador',       // Adicionado
            'Jardineiro',      // Adicionado
            'Outros',
        ];

        foreach ($especialidades as $nome) {
            Especialidade::create(['name' => $nome]);
        }
    }
}
