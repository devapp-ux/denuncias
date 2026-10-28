<?php

namespace App\Http\Controllers;

use App\Models\Manifestacao;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;

class AcompanhamentoController extends Controller
{
    public function index()
    {
        return view('acompanhamento');
    }

    // Consultar manifestação usando as credenciais impressas no comprovativo.
    public function consultar(Request $request)
    {
        $dados = $request->validate([
            'codigo' => 'required|string|max:32',
            'senha' => 'required|string|max:32',
        ]);

        $codigo = strtoupper(trim($dados['codigo']));
        $manifestacao = Manifestacao::with(['evidencias', 'encaminhamentos', 'mensagens', 'provedor'])
            ->where('codigo', $codigo)
            ->first();

        if (! $manifestacao || ! $this->senhaValida($manifestacao, $dados['senha'])) {
            return $this->credenciaisInvalidas();
        }

        return response()->json([
            'ok' => true,
            'manifestacao' => [
                'codigo' => $manifestacao->codigo,
                'tipo' => ucfirst($manifestacao->tipo),
                'data' => $manifestacao->created_at->format('d/m/Y'),
                'estado' => $manifestacao->estado,
                'estado_label' => $this->estadoLabel($manifestacao->estado),
                'estado_badge' => $this->estadoBadge($manifestacao->estado),
                'versao' => $manifestacao->updated_at->toISOString(),
            ],
            'timeline' => $this->timeline($manifestacao),
            'mensagens' => $manifestacao->mensagens->map(fn ($m) => [
                'id' => $m->id,
                'autor' => $m->autor,
                'mensagem' => $m->mensagem,
                'data' => $m->created_at->format('d/m/Y H:i'),
            ]),
            'evidencias' => $manifestacao->evidencias->map(fn ($e) => [
                'nome' => $e->nome_original,
                'tamanho' => $e->tamanho ? round($e->tamanho / 1024).' KB' : '',
                'url' => asset('storage/'.$e->caminho),
            ]),
        ]);
    }

    public function sincronizar(Request $request)
    {
        $dados = $request->validate([
            'codigo' => 'required|string|max:32',
            'senha' => 'required|string|max:32',
            'versao' => 'nullable|string|max:40',
            'mensagem_apos' => 'nullable|integer|min:0',
        ]);

        $manifestacao = Manifestacao::where('codigo', strtoupper(trim($dados['codigo'])))->first();
        if (! $manifestacao || ! $this->senhaValida($manifestacao, $dados['senha'])) {
            return $this->credenciaisInvalidas();
        }

        $ultimaMensagemId = (int) $manifestacao->mensagens()->max('id');
        $versao = $manifestacao->updated_at->toISOString();
        $mensagemApos = (int) ($dados['mensagem_apos'] ?? 0);

        if (
            hash_equals((string) ($dados['versao'] ?? ''), $versao)
            && $mensagemApos >= $ultimaMensagemId
        ) {
            return response()->noContent();
        }

        $manifestacao->load('encaminhamentos');

        return response()->json([
            'ok' => true,
            'manifestacao' => [
                'estado' => $manifestacao->estado,
                'estado_label' => $this->estadoLabel($manifestacao->estado),
                'estado_badge' => $this->estadoBadge($manifestacao->estado),
                'versao' => $versao,
            ],
            'timeline' => $this->timeline($manifestacao),
            'mensagens' => $manifestacao->mensagens()
                ->where('id', '>', $mensagemApos)
                ->get()
                ->map(fn ($mensagem) => [
                    'id' => $mensagem->id,
                    'autor' => $mensagem->autor,
                    'mensagem' => $mensagem->mensagem,
                    'data' => $mensagem->created_at->format('d/m/Y H:i'),
                ]),
            'ultima_mensagem_id' => $ultimaMensagemId,
        ]);
    }

    // Enviar mensagem
    public function mensagem(Request $request, string $codigo)
    {
        $dados = $request->validate([
            'mensagem' => 'required|string|max:500',
            'senha' => 'required|string|max:32',
        ]);

        $manifestacao = Manifestacao::where('codigo', strtoupper(trim($codigo)))->first();

        if (! $manifestacao || ! $this->senhaValida($manifestacao, $dados['senha'])) {
            return $this->credenciaisInvalidas();
        }

        $mensagem = $manifestacao->mensagens()->create([
            'autor' => 'utilizador',
            'mensagem' => $dados['mensagem'],
        ]);

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

    private function senhaValida(Manifestacao $manifestacao, string $senha): bool
    {
        if ($manifestacao->senha_acompanhamento_hash) {
            return Hash::check(strtoupper(trim($senha)), $manifestacao->senha_acompanhamento_hash);
        }

        // Registos anteriores à criação da senha continuam acessíveis com o código nos dois campos.
        return hash_equals(strtoupper($manifestacao->codigo), strtoupper(trim($senha)));
    }

    private function credenciaisInvalidas()
    {
        return response()->json([
            'ok' => false,
            'mensagem' => 'Código ou senha inválidos. Verifique os dados no comprovativo.',
        ], 404);
    }

    // Funções auxiliares para gerar labels e badges de estado, e construir a timeline da manifestação
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

    private function estadoBadge(string $estado): string
    {
        return match ($estado) {
            'respondida', 'arquivada' => 'ur-status-badge--respondida',
            'em_analise' => 'ur-status-badge--analise',
            default => 'ur-status-badge--pendente',
        };
    }

    private function timeline(Manifestacao $m): array
    {
        $passos = [[
            'estado' => 'pendente',
            'titulo' => 'Manifestação submetida',
            'desc' => 'A sua manifestação foi recebida com sucesso e encontra-se registada no sistema.',
            'data' => $m->created_at->format('d/m/Y H:i'),
        ]];

        foreach ($m->encaminhamentos->sortBy('created_at') as $encaminhamento) {
            $estado = $encaminhamento->estado_novo;
            $passos[] = [
                'estado' => $estado,
                'titulo' => $encaminhamento->estado_anterior === $estado
                    ? 'Acompanhamento atualizado'
                    : $this->estadoLabel($estado),
                'desc' => match ($estado) {
                    'encaminhada' => 'A manifestação foi encaminhada para o serviço competente.',
                    'em_analise' => 'A situação está a ser analisada pelo serviço competente.',
                    'respondida' => 'Foi registada uma resposta para a sua manifestação.',
                    'arquivada' => 'O acompanhamento desta manifestação foi encerrado.',
                    default => 'O estado da manifestação foi atualizado.',
                },
                'data' => $encaminhamento->created_at->format('d/m/Y H:i'),
            ];
        }

        if ($m->encaminhamentos->isEmpty() && $m->estado !== 'pendente') {
            $estado = $m->estado;
            $passos[] = [
                'estado' => $estado,
                'titulo' => $this->estadoLabel($estado),
                'desc' => match ($estado) {
                    'encaminhada' => 'A manifestação foi encaminhada para o serviço competente.',
                    'em_analise' => 'A situação está a ser analisada pelo serviço competente.',
                    'respondida' => 'Foi registada uma resposta para a sua manifestação.',
                    'arquivada' => 'O acompanhamento desta manifestação foi encerrado.',
                    default => 'O estado da manifestação foi atualizado.',
                },
                'data' => ($m->encaminhada_em ?? $m->updated_at)->format('d/m/Y H:i'),
            ];
        }

        $passos[count($passos) - 1]['estado'] = 'active';

        return array_map(function ($passo, $indice) use ($passos): array {
            if ($indice < count($passos) - 1) {
                $passo['estado'] = 'completed';
            }

            return $passo;
        }, $passos, array_keys($passos));
    }
}
