<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Address extends Model
{
    protected $fillable = [
        'zipCode',
        'state',
        'city',
        'district',
        'street',
        'number',
        'complement',
        'user_id',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function orcamento()
    {
        return $this->belongsTo(Orcamento::class);
    }
}
