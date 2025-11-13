<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Imagem extends Model
{

    protected $fillable = [
        'path',
        'post_id',
        'orcamento_id',
        'tipo_imagem_id',
    ];

    public function post(){
        return $this->belongsTo(Post::class);
    }

    public function orcamento(){
        return $this->belongsTo(Orcamento::class);
    }


    protected $table = 'imagens';
}
