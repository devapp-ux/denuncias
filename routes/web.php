<?php

use App\Http\Controllers\AcompanhamentoController;
use App\Http\Controllers\ManifestacaoController;
use App\Http\Controllers\UniversidadeAdministracaoController;
use App\Http\Controllers\UniversidadeAuthController;
use App\Http\Controllers\UniversidadeController;
use Illuminate\Support\Facades\Route;

// Telas de perfil
Route::get('/', fn () => view('home'))->name('home');
Route::get('dash1', fn () => view('acompanhamento'));
Route::get('carta', [ManifestacaoController::class, 'carta'])->name('manifestacao.carta');
Route::get('carta/pdf', [ManifestacaoController::class, 'baixarCartaPdf'])->name('manifestacao.carta.pdf');
// Route::get('dash2', fn () => view('dashboard2'));

// ---- Formulários (GET) ----
Route::get('man1', [ManifestacaoController::class, 'createDenuncia'])->name('manifestacao.denuncia.create');
Route::get('man2', [ManifestacaoController::class, 'createReclamacao'])->name('manifestacao.reclamacao.create');
Route::get('man3', [ManifestacaoController::class, 'createSugestao'])->name('manifestacao.sugestao.create');

// ---- Submissões (POST) ----
Route::post('man1/denuncia', [ManifestacaoController::class, 'storeDenuncia'])->name('manifestacao.denuncia.store');
Route::post('man2/reclamacao', [ManifestacaoController::class, 'storeReclamacao'])->name('manifestacao.reclamacao.store');
Route::post('man3/sugestao', [ManifestacaoController::class, 'storeSugestao'])->name('manifestacao.sugestao.store');

Route::middleware('guest')->group(function () {
    Route::get('universidade/login', [UniversidadeAuthController::class, 'create'])->name('universidade.login');
    Route::post('universidade/login', [UniversidadeAuthController::class, 'store'])
        ->middleware('throttle:6,1')
        ->name('universidade.login.store');
});

// ---- Rotas protegidas por autenticação ----
Route::prefix('universidade')->name('universidade.')->middleware('auth')->group(function () {
    Route::post('logout', [UniversidadeAuthController::class, 'destroy'])->name('logout');
    Route::get('/', [UniversidadeController::class, 'index'])->name('dashboard');
    Route::get('actualizacoes-painel', [UniversidadeController::class, 'sincronizarPainel'])->name('dashboard.sincronizar');
    Route::get('manifestacoes/{manifestacao}', [UniversidadeController::class, 'show'])->name('manifestacoes.show');
    Route::get('manifestacoes/{manifestacao}/actualizacoes', [UniversidadeController::class, 'sincronizarManifestacao'])->name('manifestacoes.sincronizar');
    Route::patch('manifestacoes/{manifestacao}', [UniversidadeController::class, 'atualizar'])->name('manifestacoes.atualizar');
    Route::post('manifestacoes/{manifestacao}/mensagens', [UniversidadeController::class, 'enviarMensagem'])->name('manifestacoes.mensagens.guardar');

    Route::prefix('administracao')->name('administracao.')->group(function () {
        Route::get('/', [UniversidadeAdministracaoController::class, 'index'])->name('index');
        Route::post('utilizadores', [UniversidadeAdministracaoController::class, 'guardarUtilizador'])->name('utilizadores.guardar');
        Route::patch('utilizadores/{user}', [UniversidadeAdministracaoController::class, 'atualizarUtilizador'])->name('utilizadores.atualizar');
        Route::post('provedores', [UniversidadeAdministracaoController::class, 'guardarProvedor'])->name('provedores.guardar');
        Route::patch('provedores/{provedor}', [UniversidadeAdministracaoController::class, 'atualizarProvedor'])->name('provedores.atualizar');
    });
});

// ---- Rotas de acompanhamento ----
Route::get('acompanhamento', [AcompanhamentoController::class, 'index'])->name('acompanhamento.index');
Route::post('acompanhamento/consultar', [AcompanhamentoController::class, 'consultar'])->name('acompanhamento.consultar');
Route::post('acompanhamento/sincronizar', [AcompanhamentoController::class, 'sincronizar'])->name('acompanhamento.sincronizar');
Route::post('acompanhamento/{codigo}/mensagem', [AcompanhamentoController::class, 'mensagem'])->name('acompanhamento.mensagem');
