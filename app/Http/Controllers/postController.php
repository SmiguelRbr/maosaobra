<?php

namespace App\Http\Controllers;

use App\Models\Post;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class postController extends Controller
{
    public function store(Request $request)
    {
        $request->validate([
            'descricao'   => 'required|string|max:1000',
            'imagens.*'   => 'nullable|image|mimes:jpeg,png,jpg,gif,webp|max:2048',
        ]);

        $post = Post::create([
            'user_id'   => Auth::id(), // pega o usuário logado pelo token
            'descricao' => $request->descricao,
            'curtidas'  => 0,
            'data'      => now(),
        ]);

        if ($request->hasFile('imagens')) {
            foreach ($request->file('imagens') as $imagem) {
                $path = $imagem->store('posts', 'public');

                $post->images()->create([
                    'path' => $path,
                    'orcamento_id' => null,
                ]);
            }
        }

        return response()->json($post->load(['images', 'user']), 201);
    }


    public function index()
    {
        // Busca todos os posts com usuário e imagens
        $posts = Post::with(['user', 'images'])->get();

        return response()->json($posts, 200);
    }

    public function getPostsByUser($id)
    {
        // Busca todos os posts do usuário com as imagens
        $posts = Post::with(['images', 'user'])
            ->where('user_id', $id)
            ->get();

        // Retorna sempre uma lista (vazia se não houver posts)
        return response()->json($posts, 200);
    }
}


// sexo xd