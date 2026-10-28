<?php

namespace App\Http\Controllers;

use App\Models\Encaminhamento;
use App\Models\Manifestacao;
use App\Models\Provedor;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\Rule;
use Illuminate\View\View;

class UniversidadeController extends Controller
{
    public function index(Request $request): View
    {
        $user = $request->user();
        abort_unless($user->isAdmin() || $user->isProvedor(), 403);

        $filtros = $request->validate([
            'estado' => ['nullable', Rule::in(Manifestacao::ESTADOS)],
            'tipo' => ['nullable', Rule::in(['denuncia', 'reclamacao', 'sugestao'])],
            'busca' => 'nullable|string|max:80',
        ]);

        return view('universidade.dashboard', [
            ...$this->dadosPainel($request, $filtros),
            'versao' => $this->versaoPainel($request),
        ]);
    }

    public function sincronizarPainel(Request $request): JsonResponse|Response
    {
        $user = $request->user();
        abort_unless($user->isAdmin() || $user->isProvedor(), 403);

        $request->validate(['versao' => 'nullable|string|max:64']);
        $versao = $this->versaoPainel($request);
        if (hash_equals((string) $request->query('versao', ''), $versao)) {
            return response()->noContent();
        }

        $filtros = $request->validate([
            'estado' => ['nullable', Rule::in(Manifestacao::ESTADOS)],
            'tipo' => ['nullable', Rule::in(['denuncia', 'reclamacao', 'sugestao'])],
            'busca' => 'nullable|string|max:80',
        ]);
        $dados = $this->dadosPainel($request, $filtros);

        return response()->json([
            'versao' => $versao,
            'html' => view('universidade.partials.dashboard-live', $dados)->render(),
        ]);
    }

    public function show(Request $request, Manifestacao $manifestacao): View
    {
        $this->autorizarManifestacao($request, $manifestacao);

        $mensagensPorLer = $manifestacao->mensagens()
            ->where('autor', 'utilizador')
            ->where('lida', false)
            ->count();

        $mensagensMarcadas = $manifestacao->mensagens()
            ->where('autor', 'utilizador')
            ->where('lida', false)
            ->update(['lida' => true]);
        if ($mensagensMarcadas > 0) {
            $manifestacao->touch();
        }

        $manifestacao->load([
            'provedor',
            'denuncia',
            'reclamacao',
            'sugestao',
            'evidencias',
            'encaminhamentos.provedor',
            'encaminhamentos.user',
            'mensagens',
        ]);

        return view('universidade.manifestacao', [
            'manifestacao' => $manifestacao,
            'mensagensPorLer' => $mensagensPorLer,
            'versao' => $manifestacao->fresh()->updated_at->toISOString(),
            'provedores' => $request->user()->isAdmin()
                ? Provedor::query()
                    ->where('ativo', true)
                    ->orWhere('id', $manifestacao->provedor_id)
                    ->orderBy('nome')
                    ->get()
                : collect(),
        ]);
    }

    public function sincronizarManifestacao(Request $request, Manifestacao $manifestacao): JsonResponse|Response
    {
        $this->autorizarManifestacao($request, $manifestacao);

        $ultimaMensagemId = (int) $manifestacao->mensagens()->max('id');
        $ultimoEventoId = (int) $manifestacao->encaminhamentos()->max('id');
        $dados = $request->validate([
            'versao' => 'nullable|string|max:40',
            'mensagem_apos' => 'nullable|integer|min:0',
            'evento_apos' => 'nullable|integer|min:0',
        ]);
        $versao = $manifestacao->updated_at->toISOString();
        $mensagemApos = (int) ($dados['mensagem_apos'] ?? 0);
        $eventoApos = (int) ($dados['evento_apos'] ?? 0);

        if (
            hash_equals((string) ($dados['versao'] ?? ''), $versao)
            && $mensagemApos >= $ultimaMensagemId
            && $eventoApos >= $ultimoEventoId
        ) {
            return response()->noContent();
        }

        $mensagens = $manifestacao->mensagens()
            ->where('id', '>', $mensagemApos)
            ->get();
        $mensagensMarcadas = $manifestacao->mensagens()
            ->where('autor', 'utilizador')
            ->where('lida', false)
            ->update(['lida' => true]);
        if ($mensagensMarcadas > 0) {
            $manifestacao->touch();
            $versao = $manifestacao->fresh()->updated_at->toISOString();
        }

        $eventos = $manifestacao->encaminhamentos()
            ->with(['user', 'provedor'])
            ->where('id', '>', $eventoApos)
            ->orderBy('id')
            ->get();
        $manifestacao->load('provedor');

        return response()->json([
            'versao' => $versao,
            'estado' => $manifestacao->estado,
            'estado_label' => $this->estadoLabel($manifestacao->estado),
            'provedor' => $manifestacao->provedor?->nome ?? 'Sem serviço atribuído',
            'mensagens' => $mensagens->map(fn ($mensagem) => [
                'id' => $mensagem->id,
                'autor' => $mensagem->autor,
                'mensagem' => $mensagem->mensagem,
                'data' => $mensagem->created_at->format('d/m/Y H:i'),
            ])->values(),
            'eventos' => $eventos->map(fn ($evento) => [
                'id' => $evento->id,
                'estado' => $this->estadoLabel($evento->estado_novo),
                'estado_anterior' => $evento->estado_anterior
                    ? $this->estadoLabel($evento->estado_anterior)
                    : null,
                'data' => $evento->created_at->format('d/m/Y H:i'),
                'utilizador' => $evento->user?->name ?? 'Sistema',
                'provedor' => $evento->provedor?->nome,
                'observacao' => $evento->observacao,
            ])->values(),
            'ultima_mensagem_id' => $ultimaMensagemId,
            'ultimo_evento_id' => $ultimoEventoId,
        ]);
    }

    public function enviarMensagem(Request $request, Manifestacao $manifestacao): RedirectResponse|JsonResponse
    {
        $this->autorizarManifestacao($request, $manifestacao);

        $dados = $request->validate([
            'mensagem' => 'required|string|max:2000',
        ]);

        $mensagem = $manifestacao->mensagens()->create([
            'autor' => 'instituicao',
            'mensagem' => trim($dados['mensagem']),
        ]);

        if ($request->expectsJson()) {
            return response()->json([
                'ok' => true,
                'mensagem' => [
                    'id' => $mensagem->id,
                    'autor' => $mensagem->autor,
                    'mensagem' => $mensagem->mensagem,
                    'data' => $mensagem->created_at->format('d/m/Y H:i'),
                ],
            ]);
        }

        return redirect()
            ->route('universidade.manifestacoes.show', $manifestacao)
            ->with('sucesso', 'Mensagem enviada à pessoa que submeteu a manifestação.');
    }

    public function atualizar(Request $request, Manifestacao $manifestacao): RedirectResponse|JsonResponse
    {
        $this->autorizarManifestacao($request, $manifestacao);
        $user = $request->user();

        $dados = $request->validate([
            'estado' => ['required', Rule::in(Manifestacao::ESTADOS)],
            'observacao' => 'nullable|string|max:2000',
            'provedor_id' => [
                Rule::excludeIf(! $user->isAdmin()),
                'required',
                Rule::exists('provedores', 'id')->where(fn ($query) => $query
                    ->where('ativo', true)
                    ->orWhere('id', $manifestacao->provedor_id)),
            ],
        ]);

        $provedorAnterior = $manifestacao->provedor_id;
        $estadoAnterior = $manifestacao->estado;
        $novoProvedor = $user->isAdmin() ? (int) $dados['provedor_id'] : $provedorAnterior;
        $observacao = trim((string) ($dados['observacao'] ?? ''));

        if ($manifestacao->estado === $dados['estado'] && $provedorAnterior === $novoProvedor && $observacao === '') {
            return back()->withErrors(['observacao' => 'Indique uma alteração ou uma observação para registar no histórico.']);
        }

        DB::transaction(function () use ($manifestacao, $dados, $user, $provedorAnterior, $estadoAnterior, $novoProvedor, $observacao) {
            $manifestacao->update([
                'estado' => $dados['estado'],
                'provedor_id' => $novoProvedor,
                'encaminhada_em' => $manifestacao->encaminhada_em
                    ?? ($novoProvedor !== $provedorAnterior ? now() : null),
            ]);

            $detalhes = $observacao;
            if ($novoProvedor !== $provedorAnterior) {
                $nomeProvedor = Provedor::findOrFail($novoProvedor)->nome;
                $detalhes = trim("Encaminhada para {$nomeProvedor}. {$detalhes}");
            }

            Encaminhamento::create([
                'manifestacao_id' => $manifestacao->id,
                'provedor_id' => $novoProvedor,
                'estado_anterior' => $estadoAnterior,
                'estado_novo' => $dados['estado'],
                'observacao' => $detalhes !== '' ? $detalhes : null,
                'user_id' => $user->id,
            ]);
        });

        if ($request->expectsJson()) {
            $manifestacao->refresh()->load('provedor');

            return response()->json([
                'ok' => true,
                'estado' => $manifestacao->estado,
                'estado_label' => $this->estadoLabel($manifestacao->estado),
                'provedor' => $manifestacao->provedor?->nome ?? 'Sem serviço atribuído',
                'versao' => $manifestacao->updated_at->toISOString(),
            ]);
        }

        return redirect()
            ->route('universidade.manifestacoes.show', $manifestacao)
            ->with('sucesso', 'Manifestação atualizada e registada na timeline.');
    }

    private function autorizarManifestacao(Request $request, Manifestacao $manifestacao): void
    {
        $user = $request->user();
        abort_unless(
            $user->isAdmin() || ($user->isProvedor() && $manifestacao->provedor_id === $user->provedor_id),
            404,
        );
    }

    private function versaoPainel(Request $request): string
    {
        $user = $request->user();
        $manifestacoes = Manifestacao::query()
            ->when($user->isProvedor(), fn ($query) => $query->where('provedor_id', $user->provedor_id));

        $manifestacoesVersao = (clone $manifestacoes)
            ->selectRaw('COALESCE(MAX(id), 0) as id, COALESCE(MAX(updated_at), \'\') as atualizado')
            ->first();
        $mensagensVersao = DB::table('mensagens')
            ->join('manifestacoes', 'manifestacoes.id', '=', 'mensagens.manifestacao_id')
            ->when($user->isProvedor(), fn ($query) => $query->where('manifestacoes.provedor_id', $user->provedor_id))
            ->selectRaw('COALESCE(MAX(mensagens.id), 0) as id')
            ->first();

        return sha1(json_encode([
            $manifestacoesVersao->id,
            $manifestacoesVersao->atualizado,
            $mensagensVersao->id,
        ], JSON_THROW_ON_ERROR));
    }

    private function dadosPainel(Request $request, array $filtros): array
    {
        $user = $request->user();
        $query = Manifestacao::query()
            ->with('provedor')
            ->withCount([
                'mensagens as mensagens_por_ler_count' => fn ($query) => $query
                    ->where('autor', 'utilizador')
                    ->where('lida', false),
            ])
            ->when($user->isProvedor(), fn ($query) => $query->where('provedor_id', $user->provedor_id))
            ->when($filtros['estado'] ?? null, fn ($query, $estado) => $query->where('estado', $estado))
            ->when($filtros['tipo'] ?? null, fn ($query, $tipo) => $query->where('tipo', $tipo))
            ->when($filtros['busca'] ?? null, fn ($query, $busca) => $query->where('codigo', 'like', "%{$busca}%"))
            ->latest();
        $escopo = Manifestacao::query()
            ->when($user->isProvedor(), fn ($query) => $query->where('provedor_id', $user->provedor_id));

        return [
            'manifestacoes' => $query->paginate(15)->withQueryString(),
            'contagens' => (clone $escopo)
                ->selectRaw('estado, COUNT(*) as total')
                ->groupBy('estado')
                ->pluck('total', 'estado'),
            'filtros' => $filtros,
        ];
    }

    private function estadoLabel(string $estado): string
    {
        return [
            'pendente' => 'Pendente',
            'encaminhada' => 'Encaminhada',
            'em_analise' => 'Em análise',
            'respondida' => 'Respondida',
            'arquivada' => 'Arquivada',
        ][$estado] ?? ucfirst($estado);
    }
}
