<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Models\Avaliacao;
use App\Models\Orcamento;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class AvaliacaoController extends Controller
{
    /**
     * Criar nova avaliação para um orçamento
     * POST /api/avaliacoes
     */
    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'orcamento_id' => 'required|exists:orcamentos,id',
            'nota' => 'required|integer|min:1|max:5',
            'feedback' => 'required|string'
        ], [
            'orcamento_id.required' => 'O orçamento é obrigatório',
            'orcamento_id.exists' => 'Orçamento não encontrado',
            'nota.required' => 'A nota é obrigatória',
            'nota.integer' => 'A nota deve ser um número',
            'nota.min' => 'A nota mínima é 1',
            'nota.max' => 'A nota máxima é 5',
            'feedback.required' => 'O feedback é obrigatório'
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'errors' => $validator->errors()
            ], 422);
        }

        // Verificar se o orçamento já possui avaliação
        $orcamento = Orcamento::find($request->orcamento_id);
        if ($orcamento->avaliacao) {
            return response()->json([
                'success' => false,
                'message' => 'Este orçamento já possui uma avaliação'
            ], 400);
        }

        $avaliacao = Avaliacao::create([
            'orcamento_id' => $request->orcamento_id,
            'nota' => $request->nota,
            'feedback' => $request->feedback
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Avaliação criada com sucesso',
            'data' => $avaliacao->load('orcamento')
        ], 201);
    }

    /**
     * Listar todas as avaliações
     * GET /api/avaliacoes
     */
    public function index()
    {
        $avaliacoes = Avaliacao::with('orcamento')->get();
        
        return response()->json([
            'success' => true,
            'data' => $avaliacoes
        ]);
    }

    /**
     * Buscar avaliação específica
     * GET /api/avaliacoes/{id}
     */
    public function show($id)
    {
        $avaliacao = Avaliacao::with('orcamento')->find($id);

        if (!$avaliacao) {
            return response()->json([
                'success' => false,
                'message' => 'Avaliação não encontrada'
            ], 404);
        }

        return response()->json([
            'success' => true,
            'data' => $avaliacao
        ]);
    }

    /**
     * Listar avaliações de um prestador específico
     * GET /api/prestadores/{id}/avaliacoes
     */
    public function avaliacoesPrestador($prestadorId)
    {
        $avaliacoes = Avaliacao::whereHas('orcamento', function($query) use ($prestadorId) {
            $query->where('prestador_id', $prestadorId);
        })
        ->with(['orcamento.cliente:id,name'])
        ->get()
        ->map(function($avaliacao) {
            return [
                'id' => $avaliacao->id,
                'nota' => $avaliacao->nota,
                'feedback' => $avaliacao->feedback,
                'cliente_nome' => $avaliacao->orcamento->cliente->name,
                'created_at' => $avaliacao->created_at,
                'updated_at' => $avaliacao->updated_at,
            ];
        });

        // Calcular média de avaliações
        $media = $avaliacoes->avg('nota');
        $total = $avaliacoes->count();

        return response()->json([
            'success' => true,
            'data' => [
                'avaliacoes' => $avaliacoes,
                'estatisticas' => [
                    'media' => round($media, 2),
                    'total' => $total
                ]
            ]
        ]);
    }
}
