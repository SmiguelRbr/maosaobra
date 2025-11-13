<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;

use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Tymon\JWTAuth\Contracts\JWTSubject;
use App\Models\Post;

class User extends Authenticatable implements MustVerifyEmail, JWTSubject
{
    /** @use HasFactory<\Database\Factories\UserFactory> */
    use HasFactory, Notifiable;

    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $fillable = [
        'name',
        'email',
        'password',
        'cpf',
        'role',
        'birthDate',
        'phone',
        'isActive',
        'especialidade',
        'cnpj',
        'experiencia',
        'imagePath'
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var list<string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * The attributes that should be appended to model's array form.
     *
     * @var array
     */
    protected $appends = ['media_avaliacao'];

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
        ];
    }

    public function getJWTIdentifier()
    {
        return $this->getKey();
    }

    /**
     * Return a key value array, containing any custom claims to be added to the JWT.
     *
     * @return array
     */
    public function getJWTCustomClaims()
    {
        return [
            'user' => [
                'id' => $this->id,
                'name' => $this->name,
                'email' => $this->email,
                'phone' => $this->phone,
                'cpf' => $this->cpf,
                'birthDate' => $this->birthDate,
                'role' => $this->role,
                'specialties' => $this->especialidades,
                'experiencia' => $this->experiencia,
                'imagePath' => $this->imagePath,
                'isActive' => $this->isActive,
                'addresses' => $this->addresses,
                'media_avaliacao' => $this->media_avaliacao
            ]
        ];
    }

    /**
     * Get the user's average rating as a provider.
     *
     * @return float|null
     */
    public function getMediaAvaliacaoAttribute()
    {
        if ($this->role !== 'prestador') {
            return null;
        }
        
        $media = Avaliacao::whereHas('orcamento', function($query) {
            $query->where('prestador_id', $this->id);
        })->avg('nota');
        
        return $media ? round($media, 2) : null;
    }

    public function addresses()
    {
        return $this->hasMany(Address::class);
    }

    public function posts()
    {
        return $this->hasMany(Post::class);
    }

    public function orcamentosComoCliente()
    {
        return $this->hasMany(Orcamento::class, 'cliente_id');
    }

    public function orcamentosComoPrestador()
    {
        return $this->hasMany(Orcamento::class, 'prestador_id');
    }

    public function especialidades()
    {
        return $this->belongsToMany(Especialidade::class, 'especialidade_user');
    }

    public function enderecos()
    {
        return $this->hasMany(Address::class, 'user_id');
    }

    public function avaliacoesComosPrestador()
    {
        return $this->hasManyThrough(
            Avaliacao::class,
            Orcamento::class,
            'prestador_id',
            'orcamento_id',
            'id',
            'id'
        );
    }

    public function show($id)
    {
        $prestador = User::with([
            'orcamentos.avaliacao' // Carrega os orçamentos com suas avaliações
        ])->find($id);
        
        if (!$prestador) {
            return response()->json(['success' => false, 'message' => 'Prestador não encontrado'], 404);
        }
        
        // Calcular estatísticas de avaliações
        $avaliacoes = $prestador->avaliacoesComosPrestador;
        $media = $avaliacoes->avg('nota');
        $total = $avaliacoes->count();
        
        return response()->json([
            'success' => true,
            'data' => $prestador,
            'avaliacoes_estatisticas' => [
                'media' => round($media, 2),
                'total' => $total
            ]
        ]);
    }
}