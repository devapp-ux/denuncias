<?php

namespace App\Http\Controllers;

use App\Models\Denuncia;
use App\Models\Evidencia;
use App\Models\Manifestacao;
use App\Models\OutboxEmail;
use App\Models\Provedor;
use App\Models\Reclamacao;
use App\Models\Sugestao;
use App\Services\OutboxEmailProcessor;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;
use Illuminate\Validation\Rule;
use RuntimeException;

class ManifestacaoController extends Controller
{
    // Funções para criar as views de cada tipo de manifestação
    public function createDenuncia()
    {
        return view('manifestacao1', $this->dadosForm());
    }

    public function createReclamacao()
    {
        return view('manifestacao2', $this->dadosForm());
    }

    public function createSugestao()
    {
        return view('manifestacao3', $this->dadosForm());
    }

    private function dadosForm(): array
    {
        return ['provedores' => Provedor::where('ativo', true)->orderBy('nome')->get()];
    }

    public function carta(Request $request)
    {
        $manifestacaoId = $request->session()->get('manifestacao_id');
        $senhaAcompanhamento = $request->session()->get('senha_acompanhamento');
        abort_unless($manifestacaoId && $senhaAcompanhamento, 404);

        $request->session()->reflash();

        return view('mancarta', $this->dadosCarta((int) $manifestacaoId, $senhaAcompanhamento) + [
            'pdfDownload' => false,
        ]);
    }

    public function baixarCartaPdf(Request $request)
    {
        $manifestacaoId = $request->session()->get('manifestacao_id');
        $senhaAcompanhamento = $request->session()->get('senha_acompanhamento');
        abort_unless($manifestacaoId && $senhaAcompanhamento, 404);

        $dados = $this->dadosCarta((int) $manifestacaoId, $senhaAcompanhamento);
        $logoPath = public_path('img/ur_con.jpeg');
        abort_unless(is_file($logoPath), 500, 'O logótipo institucional necessário ao comprovativo não foi encontrado.');

        $dados['pdfDownload'] = true;
        $dados['logoPdf'] = 'data:image/jpeg;base64,'.base64_encode(file_get_contents($logoPath));

        return Pdf::loadView('mancarta', $dados)
            ->setPaper('a4')
            ->download('comprovativo-'.$dados['manifestacao']->codigo.'.pdf', [
                'Cache-Control' => 'private, no-store, max-age=0',
            ]);
    }

    private function dadosCarta(int $manifestacaoId, string $senhaAcompanhamento): array
    {
        $manifestacao = Manifestacao::with([
            'denuncia',
            'reclamacao',
            'sugestao',
            'provedor',
            'evidencias',
        ])->findOrFail($manifestacaoId);

        return [
            'manifestacao' => $manifestacao,
            'detalhe' => $manifestacao->detalhe,
            'provedor' => $manifestacao->provedor,
            'geradoEm' => now(),
            'senhaAcompanhamento' => $senhaAcompanhamento,
        ];
    }

    // Regra de validação comum para todos os tipos de manifestação
    private function regrasComuns(): array
    {
        return [
            'provedor_id' => [
                'required',
                Rule::exists('provedores', 'id')
                    ->where(fn ($query) => $query->where('ativo', true)),
            ],
            'confidencialidade' => 'required|accepted',
            'confirmacao' => 'required|accepted',
            'tipo_identificacao' => 'required|in:identificado,anonimo',
            'nome' => 'required_if:tipo_identificacao,identificado|nullable|string|max:255',
            'email' => 'required_if:tipo_identificacao,identificado|nullable|email|max:255',
            'celular' => 'nullable|string|max:30',
            'evidencias' => 'nullable|array|max:7',
            'evidencias.*' => 'file|max:20480|mimes:jpg,jpeg,png,webp,heic,heif,pdf,doc,docx,xls,xlsx,ppt,pptx,mp4,mp3',
        ];
    }

    /* post de manifestação
    Funções auxiliares para criar manifestação, guardar evidências e gerar código sequencial.
    */
    private function criarManifestacao(array $dados, string $tipo, string $senhaAcompanhamento): Manifestacao
    {
        $anonimo = $dados['tipo_identificacao'] === 'anonimo';

        $manifestacao = Manifestacao::create([
            'codigo' => $this->gerarCodigo($tipo),
            'tipo' => $tipo,
            'provedor_id' => $dados['provedor_id'],
            'anonimo' => $anonimo,
            'nome' => $anonimo ? null : $dados['nome'],
            'email' => $anonimo ? null : $dados['email'],
            'celular' => $anonimo ? null : ($dados['celular'] ?? null),
            'senha_acompanhamento_hash' => Hash::make($senhaAcompanhamento),
            'confidencialidade' => true,
            'confirmacao' => true,
            'estado' => 'pendente',
        ]);

        $provedor = Provedor::findOrFail($manifestacao->provedor_id);
        $emailProvedor = trim((string) $provedor->email);

        OutboxEmail::create([
            'manifestacao_id' => $manifestacao->id,
            'destinatario' => $emailProvedor !== '' ? $emailProvedor : null,
            'payload' => [
                'provedor' => $provedor->nome,
                'codigo' => $manifestacao->codigo,
                'tipo' => $manifestacao->tipo,
                'submetida_em' => $manifestacao->created_at->toIso8601String(),
            ],
        ]);

        return $manifestacao;
    }

    // Função para guardar evidências associadas a uma manifestação
    private function guardarEvidencias(Request $request, Manifestacao $manifestacao): void
    {
        if (! $request->hasFile('evidencias')) {
            return;
        }

        foreach ($request->file('evidencias') as $arquivo) {
            $nomeOriginal = $arquivo->getClientOriginalName();
            $nomeArquivo = Str::uuid().'.'.$arquivo->extension();
            $caminho = $arquivo->storeAs('evidencias/'.$manifestacao->codigo, $nomeArquivo, 'public');

            if (! $caminho) {
                throw new RuntimeException('Não foi possível guardar um dos ficheiros anexados.');
            }

            Evidencia::create([
                'manifestacao_id' => $manifestacao->id,
                'nome_original' => $nomeOriginal,
                'nome_arquivo' => $nomeArquivo,
                'caminho' => $caminho,
                'tipo' => $arquivo->getMimeType(),
                'tamanho' => $arquivo->getSize(),
            ]);
        }
    }

    // Redireciona para o comprovativo sem colocar o código na URL ou enviá-lo por e-mail.
    private function respostaSucesso(Manifestacao $manifestacao, string $msg, string $senhaAcompanhamento)
    {
        $outboxEmail = OutboxEmail::query()
            ->where('manifestacao_id', $manifestacao->id)
            ->firstOrFail();

        app(OutboxEmailProcessor::class)->processar($outboxEmail->id);

        return redirect()
            ->route('manifestacao.carta')
            ->with('manifestacao_id', $manifestacao->id)
            ->with('senha_acompanhamento', $senhaAcompanhamento)
            ->with('sucesso', $msg);
    }

    private function gerarSenhaAcompanhamento(): string
    {
        $caracteres = '23456789ABCDEFGHJKLMNPQRSTUVWXYZ';
        $senha = '';

        for ($i = 0; $i < 10; $i++) {
            $senha .= $caracteres[random_int(0, strlen($caracteres) - 1)];
        }

        return $senha;
    }

    // post de denúncia
    public function storeDenuncia(Request $request)
    {
        $dados = $request->validate($this->regrasComuns() + [
            'assunto' => 'required|string|max:255',
            'categoria' => 'required|string|max:255',
            'outra_categoria' => 'nullable|string|max:255',
            'local' => 'nullable|string|max:255',
            'data_ocorrencia' => 'nullable|date',
            'continua' => 'nullable|in:sim,nao,nao_sei',
            'unidade_organica' => 'required|string|max:255',
            'descricao' => 'required|string|max:5000',
            'pessoas_envolvidas' => 'nullable|in:sim,nao,nao_sei',
            'identificacao_pessoas' => 'nullable|string',
            'testemunhas' => 'nullable|in:sim,nao,nao_sei',
            'identificacao_testemunhas' => 'nullable|string',
        ]);

        $senhaAcompanhamento = $this->gerarSenhaAcompanhamento();

        $manifestacao = DB::transaction(function () use ($request, $dados, $senhaAcompanhamento) {
            $manifestacao = $this->criarManifestacao($dados, 'denuncia', $senhaAcompanhamento);

            Denuncia::create([
                'manifestacao_id' => $manifestacao->id,
                'assunto' => $dados['assunto'],
                'categoria' => $dados['categoria'],
                'outra_categoria' => $dados['outra_categoria'] ?? null,
                'local' => $dados['local'] ?? null,
                'data_ocorrencia' => $dados['data_ocorrencia'] ?? null,
                'continua' => $dados['continua'] ?? null,
                'unidade_organica' => $dados['unidade_organica'],
                'descricao' => $dados['descricao'],
                'pessoas_envolvidas' => $dados['pessoas_envolvidas'] ?? null,
                'identificacao_pessoas' => $dados['identificacao_pessoas'] ?? null,
                'testemunhas' => $dados['testemunhas'] ?? null,
                'identificacao_testemunhas' => $dados['identificacao_testemunhas'] ?? null,
            ]);

            $this->guardarEvidencias($request, $manifestacao);

            return $manifestacao;
        });

        return $this->respostaSucesso($manifestacao, 'Denúncia submetida com sucesso.', $senhaAcompanhamento);
    }

    // post de reclamação
    public function storeReclamacao(Request $request)
    {
        $dados = $request->validate($this->regrasComuns() + [
            'assunto' => 'required|string|max:255',
            'categoria' => 'required|string|max:255',
            'outra_categoria' => 'nullable|string|max:255',
            'area_servico' => 'required|string|max:255',
            'local' => 'nullable|string|max:255',
            'data_ocorrencia' => 'nullable|date',
            'continua' => 'nullable|in:sim,nao,nao_sei',
            'descricao' => 'required|string|max:5000',
            'impacto' => 'nullable|string|max:5000',
            'pretensao' => 'nullable|string|max:5000',
        ]);

        $senhaAcompanhamento = $this->gerarSenhaAcompanhamento();

        $manifestacao = DB::transaction(function () use ($request, $dados, $senhaAcompanhamento) {
            $manifestacao = $this->criarManifestacao($dados, 'reclamacao', $senhaAcompanhamento);

            Reclamacao::create([
                'manifestacao_id' => $manifestacao->id,
                'assunto' => $dados['assunto'],
                'categoria' => $dados['categoria'],
                'outra_categoria' => $dados['outra_categoria'] ?? null,
                'area_servico' => $dados['area_servico'],
                'local' => $dados['local'] ?? null,
                'data_ocorrencia' => $dados['data_ocorrencia'] ?? null,
                'continua' => $dados['continua'] ?? null,
                'descricao' => $dados['descricao'],
                'impacto' => $dados['impacto'] ?? null,
                'pretensao' => $dados['pretensao'] ?? null,
            ]);

            $this->guardarEvidencias($request, $manifestacao);

            return $manifestacao;
        });

        return $this->respostaSucesso($manifestacao, 'Reclamação submetida com sucesso.', $senhaAcompanhamento);
    }

    // post de sugestão
    public function storeSugestao(Request $request)
    {
        $dados = $request->validate($this->regrasComuns() + [
            'titulo' => 'required|string|max:255',
            'area_relacionada' => 'required|string|max:255',
            'outra_area' => 'nullable|string|max:255',
            'sugestao' => 'required|string|max:5000',
            'justificacao' => 'nullable|string|max:5000',
        ]);

        $senhaAcompanhamento = $this->gerarSenhaAcompanhamento();

        $manifestacao = DB::transaction(function () use ($request, $dados, $senhaAcompanhamento) {
            $manifestacao = $this->criarManifestacao($dados, 'sugestao', $senhaAcompanhamento);

            Sugestao::create([
                'manifestacao_id' => $manifestacao->id,
                'titulo' => $dados['titulo'],
                'area_relacionada' => $dados['area_relacionada'],
                'outra_area' => $dados['outra_area'] ?? null,
                'sugestao' => $dados['sugestao'],
                'justificacao' => $dados['justificacao'] ?? null,
            ]);

            $this->guardarEvidencias($request, $manifestacao);

            return $manifestacao;
        });

        return $this->respostaSucesso($manifestacao, 'Sugestão submetida com sucesso.', $senhaAcompanhamento);
    }

    // Função para gerar código sequencial para cada tipo de manifestação
    private function gerarCodigo(string $tipo): string
    {
        $ano = now()->year;
        $prefixos = ['denuncia' => 'D', 'reclamacao' => 'R', 'sugestao' => 'S'];
        $prefixo = $prefixos[$tipo];

        $ultimo = Manifestacao::where('tipo', $tipo)
            ->whereYear('created_at', $ano)
            ->orderByDesc('id')
            ->first();

        $numero = $ultimo ? ((int) substr($ultimo->codigo, -4)) + 1 : 1;

        return 'MAN-'.$ano.$prefixo.str_pad($numero, 4, '0', STR_PAD_LEFT);
    }
}
