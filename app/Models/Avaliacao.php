<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Avaliacao extends Model
{
    protected $table = 'avaliacoes';
    
    protected $fillable = [
        'orcamento_id',
        'nota',
        'feedback'
    ];

    protected $casts = [
        'nota' => 'integer',
    ];

    // Relacionamento: avaliação pertence a um orçamento
    public function orcamento()
    {
        return $this->belongsTo(Orcamento::class);
    }
}