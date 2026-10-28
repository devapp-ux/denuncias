@php
    $estados = [
        'pendente' => 'Pendentes',
        'encaminhada' => 'Encaminhadas',
        'em_analise' => 'Em análise',
        'respondida' => 'Respondidas',
        'arquivada' => 'Arquivadas',
    ];
@endphp
<nav class="ur-summary-grid" aria-label="Filtrar por estado">
    <a class="ur-summary-item {{ empty($filtros['estado']) ? 'is-selected' : '' }}" href="{{ route('universidade.dashboard', request()->only('tipo', 'busca')) }}">
        Todas<strong>{{ array_sum($contagens->all()) }}</strong>
    </a>
    @foreach ($estados as $estado => $rotulo)
        <a class="ur-summary-item {{ ($filtros['estado'] ?? null) === $estado ? 'is-selected' : '' }}" href="{{ route('universidade.dashboard', request()->only('tipo', 'busca') + ['estado' => $estado]) }}">
            {{ $rotulo }}<strong>{{ $contagens[$estado] ?? 0 }}</strong>
        </a>
    @endforeach
</nav>

<section class="ur-panel">
    <div class="ur-page-heading">
        <div>
            <h2>Caixa de entrada</h2>
            <p class="ur-muted">{{ $manifestacoes->total() }} manifestação(ões)</p>
        </div>
        <span class="ur-live-status" data-live-status role="status">Atualizações automáticas ativas</span>
    </div>
    <div class="ur-table-wrap">
        <table class="ur-table">
            <thead>
                <tr>
                    <th>Referência</th>
                    <th>Tipo</th>
                    @if (auth()->user()->isAdmin()) <th>Serviço</th> @endif
                    <th>Estado</th>
                    <th>Recebida</th>
                    <th>Conversa</th>
                    <th></th>
                </tr>
            </thead>
            <tbody>
            @forelse ($manifestacoes as $manifestacao)
                <tr>
                    <td><strong>{{ $manifestacao->codigo }}</strong></td>
                    <td>{{ ['denuncia' => 'Denúncia', 'reclamacao' => 'Reclamação', 'sugestao' => 'Sugestão'][$manifestacao->tipo] ?? ucfirst($manifestacao->tipo) }}</td>
                    @if (auth()->user()->isAdmin()) <td>{{ $manifestacao->provedor?->nome ?? 'Sem serviço' }}</td> @endif
                    <td>
                        <span class="ur-status ur-status--{{ $manifestacao->estado }}">
                            {{ ['pendente' => 'Pendente', 'encaminhada' => 'Encaminhada', 'em_analise' => 'Em análise', 'respondida' => 'Respondida', 'arquivada' => 'Arquivada'][$manifestacao->estado] ?? ucfirst($manifestacao->estado) }}
                        </span>
                    </td>
                    <td>{{ $manifestacao->created_at->format('d/m/Y H:i') }}</td>
                    <td>
                        @if ($manifestacao->mensagens_por_ler_count)
                            <span class="ur-unread">{{ $manifestacao->mensagens_por_ler_count }} por ler</span>
                        @else
                            <span class="ur-muted">Sem mensagens novas</span>
                        @endif
                    </td>
                    <td>
                        <a class="ur-btn-secondary" href="{{ route('universidade.manifestacoes.show', $manifestacao) }}">Abrir processo</a>
                        <a class="ur-muted" href="{{ route('universidade.manifestacoes.show', $manifestacao) }}#conversa">Conversa</a>
                    </td>
                </tr>
            @empty
                <tr><td colspan="{{ auth()->user()->isAdmin() ? 7 : 6 }}" class="ur-muted">Não foram encontradas manifestações para estes filtros.</td></tr>
            @endforelse
            </tbody>
        </table>
    </div>
    @if ($manifestacoes->hasPages())
        <nav class="ur-pagination" aria-label="Paginação das manifestações">
            <p class="ur-pagination-summary">
                A mostrar {{ $manifestacoes->firstItem() }}–{{ $manifestacoes->lastItem() }}
                de {{ $manifestacoes->total() }} resultados
            </p>
            <div class="ur-pagination-controls">
                @if ($manifestacoes->onFirstPage())
                    <span class="ur-page-link is-disabled" aria-disabled="true">Anterior</span>
                @else
                    <a class="ur-page-link" href="{{ $manifestacoes->previousPageUrl() }}" rel="prev">Anterior</a>
                @endif
                <span class="ur-page-current">Página {{ $manifestacoes->currentPage() }} de {{ $manifestacoes->lastPage() }}</span>
                @if ($manifestacoes->hasMorePages())
                    <a class="ur-page-link" href="{{ $manifestacoes->nextPageUrl() }}" rel="next">Seguinte</a>
                @else
                    <span class="ur-page-link is-disabled" aria-disabled="true">Seguinte</span>
                @endif
            </div>
        </nav>
    @endif
</section>
