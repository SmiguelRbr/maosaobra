<?php

namespace App\Http\Controllers;

use App\Models\Especialidade;
use App\Models\Post;
use App\Models\Status;
use App\Models\User;
use Illuminate\Http\Request;

class getController extends Controller
{
    public function getUserTypes()
    {
        $types = ['cliente', 'prestador', 'admin', 'superAdmin'];
        return response()->json($types);
    }

    public function indexEspecialidades()
    {
        $especialidades = Especialidade::all();
        return response()->json($especialidades);
    }

    public function indexStatuses()
    {
        $statuses = Status::all();
        return response()->json($statuses);
    }

    public function buscarEspecialidade($id)
    {
        $user = User::findOrFail($id);

        $especialidades = $user->especialidades;

        return response()->json([
            'specialties' => $especialidades
        ], 200);
    }

    public function buscarPrestadores($city = null, $especialidade = null)
    {
        $prestadores = User::where('role', 'prestador')
            // filtro por cidade
            ->when($city, function ($q, $city) {
                $q->whereHas('addresses', function ($query) use ($city) {
                    $query->where('city', $city);
                });
            })
            // filtro por especialidade (N:N)
            ->when($especialidade, function ($q, $especialidade) {
                $q->whereHas('especialidades', function ($query) use ($especialidade) {
                    $query->where('name', $especialidade);
                });
            })
            ->with(['addresses', 'especialidades'])
            ->get();

        return response()->json($prestadores);
    }


    public function buscarPostid(Post $post)
    {
        return response()->json($post);
    }
}
