<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Orcamento extends Model
{
    protected $fillable = [
        'cliente_id',
        'prestador_id',
        'address_id',
        'descricao',
        'visita',
        'data',
        'valor',
        'justificativa',
        'observacoes',
        'avaliacao',
        'feedback',
        'status_id',
    ];

    protected $casts = [
        'visita' => 'boolean',
        'data' => 'date',
        'valor' => 'decimal:2',
    ];


    public function cliente()
    {
        return $this->belongsTo(User::class, 'cliente_id');
    }

    public function prestador()
    {
        return $this->belongsTo(User::class, 'prestador_id');
    }

    public function imagens()
    {
        return $this->hasMany(Imagem::class);
    }

    public function notificacoes()
    {
        return $this->hasMany(Notificacao::class);
    }

    public function status()
    {
        return $this->belongsTo(Status::class);
    }

    public function endereco()
    {
        return $this->belongsTo(Address::class, 'address_id');
    }

    public function avaliacao()
    {
        return $this->hasOne(Avaliacao::class);
    }
}
