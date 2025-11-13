<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Post extends Model
{
    protected $fillable = [
        'descricao',
        'curtidas',
        'data',
        'user_id'
    ];

    public function user(){
        return $this->belongsTo(User::class);
    }

    public function images(){
        return $this->hasMany(Imagem::class);
    }

    public function comentarios(){
        return $this->hasMany(Comentario::class);
    }
}
