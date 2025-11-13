<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Status extends Model
{
    protected $fillable = [
        'descricao'
    ];

    public function orcamentos()
    {
        return $this->hasMany(Orcamento::class);
    }
}
