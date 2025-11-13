<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('notificacoes', function (Blueprint $table) {
            $table->id();
            $table->foreignId('orcamento_id')->constrained('orcamentos')->onDelete('cascade');
            $table->foreignId('remetente_id')->constrained('users')->onDelete('cascade'); // quem envia
            $table->foreignId('destinatario_id')->constrained('users')->onDelete('cascade'); // quem recebe
            $table->text('descricao'); // texto pronto, ex: "aceitou o orçamento"
            $table->string('titulo');
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('notificacoes');
    }
};
