<?php

namespace App\Http\Controllers;

use App\Models\Imagem;
use App\Models\Orcamento;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class orcamentoController extends Controller
{
    public function getByPrestador($prestadorId)
    {
        $orcamentos = Orcamento::with(['cliente', 'prestador', 'imagens', 'endereco', 'status'])
            ->where('prestador_id', $prestadorId)
            ->get();

        return response()->json($orcamentos);
    }

    public function getByCliente($clienteId)
    {
        $orcamentos = Orcamento::with(['cliente', 'prestador', 'imagens', 'endereco', 'status'])
            ->where('cliente_id', $clienteId)
            ->get();

        return response()->json($orcamentos);
    }

    public function updateVisita(Request $request, $id)
    {
        $orcamento = Orcamento::findOrFail($id);

        $request->validate([
            'data' => 'required|date',
            'visita' => 'sometimes|boolean',
        ]);

        $orcamento->update($request->only(['data', 'visita']));

        return response()->json($orcamento->load(['cliente', 'prestador', 'imagens']));
    }

    public function updateStatus(Request $request, $id)
    {
        $orcamento = Orcamento::findOrFail($id);

        $request->validate([
            'status_id' => 'required|exists:statuses,id',
        ]);

        $orcamento->update(['status_id' => $request->status_id]);

        return response()->json($orcamento->load(['cliente', 'prestador', 'imagens', 'status']));
    }

    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'cliente_id'    => 'required|exists:users,id',
            'prestador_id'  => 'required|exists:users,id',
            'address_id'    => 'required|exists:addresses,id',
            'descricao'     => 'nullable|string',
            'visita'        => 'required|boolean',
            'data'          => 'nullable|date',
            'observacoes'   => 'nullable|string',
            'avaliacao'     => 'nullable|integer|min:1|max:5',
            'feedback'      => 'nullable|string|max:1000',
            'imagens.*'     => 'nullable|image|mimes:jpeg,png,jpg,gif,webp|max:2048',
        ], [
            'cliente_id.required'   => 'O campo cliente é obrigatório.',
            'prestador_id.required' => 'O campo prestador é obrigatório.',
            'visita.required'       => 'Informe se há disponibilidade para visita.',
            'address_id.required'   => 'O endereço do orçamento é obrigatório.',
            'address_id.exists'     => 'O endereço informado não existe.'
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'errors' => $validator->errors()
            ], 422);
        }

        $orcamento = Orcamento::create([
            'cliente_id'   => $request->cliente_id,
            'prestador_id' => $request->prestador_id,
            'descricao'    => $request->descricao,
            'visita'       => $request->visita,
            'data'         => $request->data,
            'observacoes'  => $request->observacoes,
            'address_id'  => $request->address_id,
            'status_id' => $request->status_id
        ]);

        if ($request->hasFile('imagens')) {
            foreach ($request->file('imagens') as $imagem) {
                $path = $imagem->store('orcamentos', 'public');

                $orcamento->imagens()->create([
                    'path' => $path,
                    'post_id' => null,
                ]);
            }
        }

        return response()->json([
            'success' => true,
            'message' => 'Orçamento criado com sucesso.',
            'orcamento' => $orcamento->load(['cliente', 'prestador', 'imagens', 'endereco', 'status'])
        ], 201);
    }

    public function atualizarValorJustificativa(Request $request, $id)
    {
        $orcamento = Orcamento::find($id);

        if (!$orcamento) {
            return response()->json([
                'success' => false,
                'message' => 'Orçamento não encontrado'
            ], 404);
        }

        $validator = Validator::make($request->all(), [
            'valor' => 'required|numeric|min:0',
            'justificativa' => 'required|string'
        ], [
            'valor.required' => 'O valor é obrigatório',
            'valor.numeric' => 'O valor deve ser um número',
            'valor.min' => 'O valor não pode ser negativo',
            'justificativa.required' => 'A justificativa é obrigatória',
            'justificativa.string' => 'A justificativa deve ser um texto'
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'errors' => $validator->errors()
            ], 422);
        }

        $orcamento->update([
            'valor' => $request->valor,
            'justificativa' => $request->justificativa
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Valor e justificativa atualizados com sucesso',
            'data' => $orcamento
        ], 200);
    }
}
