<?php

namespace App\Http\Controllers;

use App\Models\Notificacao;
use Illuminate\Http\Request;

class notificacaoController extends Controller
{
    public function store(Request $request)
    {
        $request->validate([
            'orcamento_id'     => 'required|exists:orcamentos,id',
            'remetente_id'     => 'required|exists:users,id',
            'destinatario_id'  => 'required|exists:users,id',
            'descricao'        => 'required|string|max:1000',
            'titulo' => 'required|string'
        ]);

        $notificacao = Notificacao::create($request->only([
            'orcamento_id',
            'remetente_id',
            'destinatario_id',
            'descricao',
            'titulo'
        ]));

        return response()->json($notificacao, 201);
    }


    // Buscar notificações por usuário destinatário
    public function getByDestinatario($userId)
    {
        $notificacoes = Notificacao::with([
            'orcamento.cliente',
            'orcamento.prestador',
            'orcamento.endereco',
            'orcamento.status',
            'remetente',
            'destinatario'
        ])
            ->where('destinatario_id', $userId)
            ->orderBy('created_at', 'desc')
            ->get();

            

        return response()->json($notificacoes, 200);
    }
}
