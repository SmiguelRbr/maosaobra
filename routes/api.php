<?php

use App\Http\Controllers\api\auth\AuthController;
use App\Http\Controllers\getController;
use App\Http\Controllers\notificacaoController;
use App\Http\Controllers\orcamentoController;
use App\Http\Controllers\postController;
use Illuminate\Foundation\Auth\EmailVerificationRequest;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AvaliacaoController;

Route::prefix('auth')->group(function () {
    Route::post('/register', [AuthController::class, 'register']);
    Route::post('/login', [AuthController::class, 'login']);
});

Route::post('/email/verification-notification', function (Request $request) {
    if ($request->user()->hasVerifiedEmail()) {
        return response()->json([
            'message' => 'Email já verificado.'
        ], 400);
    }

    $request->user()->sendEmailVerificationNotification();

    return response()->json([
        'message' => 'Link de verificação enviado!'
    ]);
})->middleware(['auth:api', 'throttle:6,1'])
    ->name('verification.send');


Route::get('/email/verify/{id}/{hash}', function (EmailVerificationRequest $request) {
    $request->fulfill();

    return response()->json([
        'message' => 'Email verificado com sucesso!'
    ]);
})->middleware(['auth:api', 'signed'])->name('verification.verify');



// Rotas protegidas por JWT
Route::middleware('auth:api')->group(function () {

    // Autenticação
    Route::post('/logout', [AuthController::class, 'logout']);
    Route::post('/refresh', [AuthController::class, 'refresh']);

    // Usuários
    Route::put('/users/{user}/role', [AuthController::class, 'updateRole']);
    Route::post('/users/{user}/prestador', [AuthController::class, 'cadastroPrestador']);
    Route::put('/users/{user}/especialidades', [AuthController::class, 'updateEspecialidade']);

    // Endereços
    Route::post('/addresses', [AuthController::class, 'cadastrarEndereco']);
    Route::get('/addresses', [AuthController::class, 'getByUser']);

    // Orçamentos
    Route::post('/orcamentos', [OrcamentoController::class, 'store']);
    Route::get('/orcamentos/cliente/{clienteId}', [orcamentoController::class, 'getByCliente']);
    Route::get('/orcamentos/prestador/{prestadorId}', [orcamentoController::class, 'getByPrestador']);
    Route::patch('/orcamentos/{id}/visita', [OrcamentoController::class, 'updateVisita']);
    Route::patch('/orcamentos/{id}/status', [OrcamentoController::class, 'updateStatus']);
    Route::patch('/orcamentos/{id}/valor', [OrcamentoController::class, 'atualizarValorJustificativa']);

    //Avaliações
    Route::post('/avaliacoes', [AvaliacaoController::class, 'store']);
    Route::get('/avaliacoes', [AvaliacaoController::class, 'index']);
    Route::get('/avaliacoes/{id}', [AvaliacaoController::class, 'show']);
    Route::get('/prestadores/{id}/avaliacoes', [AvaliacaoController::class, 'avaliacoesPrestador']);

    //Notificações
    Route::post('/notificacoes', [NotificacaoController::class, 'store']);

    // Posts
    Route::post('/posts', [postController::class, 'store']);
    Route::get('/posts', [postController::class, 'index']);

    // Buscar posts de um usuário específico
    Route::get('/users/{id}/posts', [postController::class, 'getPostsByUser']);
});

Route::get('/notificacoes/destinatario/{userId}', [NotificacaoController::class, 'getByDestinatario']);
Route::get('/potsuser', [getController::class, 'buscarPostid']);
Route::get('/buscarPrestador/{city?}/{especialidade?}', [getController::class, 'buscarPrestadores']);



//buscar especialidade por id
Route::get('/users/{users}/especialidades', [getController::class, 'buscarEspecialidade']);


// Rotas públicas
Route::get('/userTypes', [getController::class, 'getUserTypes']);
Route::get('/specialties', [getController::class, 'indexEspecialidades']);
Route::get('/statuses', [getController::class, 'indexStatuses']);
