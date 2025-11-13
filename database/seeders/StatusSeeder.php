<?php

namespace Database\Seeders;

use App\Models\Status;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class StatusSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        Status::create(['descricao' => 'solicitado']);
        Status::create(['descricao' => 'cancelado']);
        Status::create(['descricao' => 'recusado']);
        Status::create(['descricao' => 'visita_agendada']);
        Status::create(['descricao' => 'nova_data_sugerida_cliente']);
        Status::create(['descricao' => 'nova_data_sugerida_prestador']);
        Status::create(['descricao' => 'visita_relizada']);
        Status::create(['descricao' => 'orçamento_aceito']);
        Status::create(['descricao' => 'servico_realizado']);
        Status::create(['descricao' => 'servico_finalizado']);
        Status::create(['descricao' => 'servico_avaliado']);
    }
}
