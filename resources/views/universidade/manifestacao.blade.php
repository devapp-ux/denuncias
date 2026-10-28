@extends('universidade.layout')

@section('titulo', $manifestacao->codigo)

@section('content')
    @php
        $estados = [
            'pendente' => 'Pendente',
            'encaminhada' => 'Encaminhada',
            'em_analise' => 'Em análise',
            'respondida' => 'Respondida',
            'arquivada' => 'Arquivada',
        ];
        $detalhe = $manifestacao->detalhe;
        $campos = match ($manifestacao->tipo) {
            'denuncia' => [
                'assunto' => 'Assunto',
                'categoria' => 'Categoria',
                'outra_categoria' => 'Outra categoria',
                'unidade_organica' => 'Unidade orgânica',
                'local' => 'Local',
                'data_ocorrencia' => 'Data da ocorrência',
                'continua' => 'A situação mantém-se?',
                'pessoas_envolvidas' => 'Há pessoas envolvidas?',
                'identificacao_pessoas' => 'Identificação de pessoas envolvidas',
                'testemunhas' => 'Há testemunhas?',
                'identificacao_testemunhas' => 'Identificação de testemunhas',
                'descricao' => 'Descrição',
            ],
            'reclamacao' => [
                'assunto' => 'Assunto',
                'categoria' => 'Categoria',
                'outra_categoria' => 'Outra categoria',
                'area_servico' => 'Área ou serviço',
                'local' => 'Local',
                'data_ocorrencia' => 'Data da ocorrência',
                'continua' => 'A situação mantém-se?',
                'descricao' => 'Descrição',
                'impacto' => 'Impacto',
                'pretensao' => 'Pretensão',
            ],
            default => [
                'titulo' => 'Título',
                'area_relacionada' => 'Área relacionada',
                'outra_area' => 'Outra área',
                'sugestao' => 'Sugestão',
                'justificacao' => 'Justificação',
            ],
        };
    @endphp

    <p class="ur-muted"><a href="{{ route('universidade.dashboard') }}">← Voltar à caixa de entrada</a></p>
    <header class="ur-page-heading ur-detail-heading">
        <div>
            <h1>{{ $manifestacao->codigo }}</h1>
            <p class="ur-page-description">
                {{ ['denuncia' => 'Denúncia', 'reclamacao' => 'Reclamação', 'sugestao' => 'Sugestão'][$manifestacao->tipo] ?? ucfirst($manifestacao->tipo) }}
                · Recebida em {{ $manifestacao->created_at->format('d/m/Y H:i') }}
                · <span id="manifestacao-provedor">{{ $manifestacao->provedor?->nome ?? 'Sem serviço atribuído' }}</span>
            </p>
        </div>
        <span class="ur-status ur-status--{{ $manifestacao->estado }}" id="manifestacao-estado">{{ $estados[$manifestacao->estado] ?? ucfirst($manifestacao->estado) }}</span>
    </header>

    @if ($mensagensPorLer > 0)
        <div class="ur-alert--success" role="status">
            Existem {{ $mensagensPorLer }} mensagem(ns) nova(s) da pessoa que submeteu a manifestação. As mensagens foram marcadas como lidas ao abrir este processo.
        </div>
    @endif

    <div class="ur-detail-grid">
        <div>
            <section class="ur-panel">
                <h2>Conteúdo do processo</h2>
                <div class="ur-detail-section">
                    <h3>Identificação</h3>
                    @if ($manifestacao->anonimo)
                        <p class="ur-detail-row"><strong>Modo:</strong> Anónimo</p>
                    @else
                        <p class="ur-detail-row"><strong>Nome:</strong> {{ $manifestacao->nome }}</p>
                        <p class="ur-detail-row"><strong>E-mail:</strong> {{ $manifestacao->email ?: 'Não indicado' }}</p>
                        <p class="ur-detail-row"><strong>Contacto:</strong> {{ $manifestacao->celular ?: 'Não indicado' }}</p>
                    @endif
                </div>
                @if ($detalhe)
                    <div class="ur-detail-section">
                        <h3>Informação submetida</h3>
                        @foreach ($campos as $campo => $rotulo)
                            @php($valor = $detalhe->{$campo})
                            @if (filled($valor))
                                <p class="ur-detail-row">
                                    <strong>{{ $rotulo }}:</strong><br>
                                    @if ($valor instanceof \Illuminate\Support\Carbon)
                                        {{ $valor->format('d/m/Y') }}
                                    @else
                                        {{ str_replace('_', ' ', (string) $valor) }}
                                    @endif
                                </p>
                            @endif
                        @endforeach
                    </div>
                @endif
                <details class="ur-disclosure">
                    <summary>Ficheiros anexos ({{ $manifestacao->evidencias->count() }})</summary>
                    @if ($manifestacao->evidencias->isNotEmpty())
                        <ul>
                            @foreach ($manifestacao->evidencias as $evidencia)
                                <li>
                                    <a href="{{ asset('storage/'.$evidencia->caminho) }}" target="_blank" rel="noopener">
                                        {{ $evidencia->nome_original }}
                                    </a>
                                    @if ($evidencia->tamanho)
                                        <span class="ur-muted">· {{ number_format($evidencia->tamanho / 1024, 0, ',', '.') }} KB</span>
                                    @endif
                                </li>
                            @endforeach
                        </ul>
                    @else
                        <p class="ur-muted">Não foram anexados ficheiros.</p>
                    @endif
                </details>
            </section>

            <section class="ur-panel" id="conversa">
                <h2>Conversa com o manifestante</h2>
                <p class="ur-muted">As mensagens enviadas pela instituição ficam visíveis no acompanhamento público desta manifestação. Não inclua notas internas nesta conversa.</p>
                <div class="ur-chat" id="manifestacao-chat"
                    data-sync-url="{{ route('universidade.manifestacoes.sincronizar', $manifestacao) }}"
                    data-dashboard-url="{{ route('universidade.dashboard') }}"
                    data-versao="{{ $versao }}"
                    data-mensagem-apos="{{ $manifestacao->mensagens->max('id') ?? 0 }}"
                    data-evento-apos="{{ $manifestacao->encaminhamentos->max('id') ?? 0 }}"
                    aria-live="polite">
                    <span class="ur-live-status" data-live-status role="status">Atualizações automáticas ativas</span>
                    @forelse ($manifestacao->mensagens as $mensagem)
                        <article class="ur-chat-message ur-chat-message--{{ $mensagem->autor }}" data-mensagem-id="{{ $mensagem->id }}">
                            {{ $mensagem->mensagem }}
                            <small>
                                {{ $mensagem->autor === 'instituicao' ? 'Universidade' : 'Manifestante' }}
                                · {{ $mensagem->created_at->format('d/m/Y H:i') }}
                            </small>
                        </article>
                    @empty
                        <p class="ur-muted">Ainda não existem mensagens nesta conversa.</p>
                    @endforelse
                </div>
                <form class="ur-chat-form" id="responder-manifestacao" method="POST" action="{{ route('universidade.manifestacoes.mensagens.guardar', $manifestacao) }}">
                    @csrf
                    <div>
                        <label for="mensagem">Nova mensagem</label>
                        <textarea id="mensagem" name="mensagem" maxlength="2000" required placeholder="Escreva uma resposta para o manifestante"></textarea>
                    </div>
                    <div><button class="ur-btn-primary" type="submit">Enviar mensagem</button></div>
                </form>
            </section>

            <section class="ur-panel">
                <h2>Histórico do processo</h2>
                <ol class="ur-timeline" id="manifestacao-historico">
                    <li>
                        <strong>Manifestação submetida</strong>
                        <div class="ur-muted">{{ $manifestacao->created_at->format('d/m/Y H:i') }} · Estado inicial: Pendente</div>
                    </li>
                    @forelse ($manifestacao->encaminhamentos->sortBy('created_at') as $evento)
                        <li data-evento-id="{{ $evento->id }}">
                            <strong>{{ $estados[$evento->estado_novo] ?? ucfirst($evento->estado_novo) }}</strong>
                            @if ($evento->estado_anterior && $evento->estado_anterior !== $evento->estado_novo)
                                <span class="ur-muted"> · Estado anterior: {{ $estados[$evento->estado_anterior] ?? $evento->estado_anterior }}</span>
                            @endif
                            <div class="ur-muted">
                                {{ $evento->created_at->format('d/m/Y H:i') }}
                                · {{ $evento->user?->name ?? 'Sistema' }}
                                @if ($evento->provedor) · {{ $evento->provedor->nome }} @endif
                            </div>
                            @if ($evento->observacao)<p>{{ $evento->observacao }}</p>@endif
                        </li>
                    @empty
                        <li class="ur-muted">Ainda não há atualizações internas.</li>
                    @endforelse
                </ol>
            </section>
        </div>

        <aside>
            <section class="ur-panel">
                <h2>Tratar manifestação</h2>
                <form id="atualizar-manifestacao" method="POST" action="{{ route('universidade.manifestacoes.atualizar', $manifestacao) }}">
                    @csrf
                    @method('PATCH')
                    <div class="ur-field">
                        <label for="estado">Estado</label>
                        <select id="estado" name="estado" required>
                            @foreach ($estados as $valor => $rotulo)
                                <option value="{{ $valor }}" @selected(old('estado', $manifestacao->estado) === $valor)>{{ $rotulo }}</option>
                            @endforeach
                        </select>
                    </div>
                    @if (auth()->user()->isAdmin())
                        <div class="ur-field">
                            <label for="provedor_id">Serviço responsável</label>
                            <select id="provedor_id" name="provedor_id" required>
                                @foreach ($provedores as $provedor)
                                    <option value="{{ $provedor->id }}" @selected(old('provedor_id', $manifestacao->provedor_id) == $provedor->id)>
                                        {{ $provedor->nome }}{{ $provedor->ativo ? '' : ' (inativo)' }}
                                    </option>
                                @endforeach
                            </select>
                        </div>
                    @endif
                    <div class="ur-field">
                        <label for="observacao">Nota interna</label>
                        <textarea id="observacao" name="observacao" maxlength="2000" placeholder="Só a equipa universitária poderá consultar esta nota">{{ old('observacao') }}</textarea>
                        <small class="ur-muted">As notas internas não são enviadas ao manifestante.</small>
                    </div>
                    <button class="ur-btn-primary" type="submit">Guardar alterações</button>
                </form>
            </section>
        </aside>
    </div>
@endsection

@push('scripts')
    <script src="{{ asset('js/universidade-manifestacao.js') }}" defer></script>
@endpush
