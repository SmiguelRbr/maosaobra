<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Notificacao extends Model
{
    protected $table = 'notificacoes';

    protected $fillable = [
        'orcamento_id',
        'remetente_id',
        'destinatario_id',
        'descricao',
        'titulo'
    ];

    public function orcamento()
    {
        return $this->belongsTo(Orcamento::class);
    }

    public function remetente()
    {
        return $this->belongsTo(User::class, 'remetente_id');
    }

    public function destinatario()
    {
        return $this->belongsTo(User::class, 'destinatario_id');
    }
}
