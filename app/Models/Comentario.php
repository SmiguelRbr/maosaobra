<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Comentario extends Model
{
    protected $fillable = [
        'descricao',
        'data',
        'post_id'
    ];

    public function post(){
        return $this->belongsTo(Post::class);
    }
}
