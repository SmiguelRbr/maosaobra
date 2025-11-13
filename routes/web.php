<?php

use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Route;

Route::get('/download', function () {
    
  $caminhoArquivo = storage_path('app/apk/app-release.apk');
    $nomeDownload = 'app-release.apk'; // Verifique se o nome aqui termina com .apk

    // Documentação:
    // 1. Verificamos se o arquivo existe.
    if (!File::exists($caminhoArquivo)) {
        abort(404, 'Arquivo não encontrado.');
    }

    // Documentação:
    // 2. ESTA É A CORREÇÃO:
    // Definimos o 'MIME Type' (o tipo do arquivo) correto para APK.
    // Isso força o navegador a entender que é um pacote Android
    // e não um arquivo ZIP genérico.
    $headers = [
        'Content-Type' => 'application/vnd.android.package-archive',
    ];

    // Documentação:
    // 3. Adicionamos o array $headers como terceiro argumento.
    return response()->download($caminhoArquivo, $nomeDownload, $headers);

});