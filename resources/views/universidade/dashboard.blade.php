@extends('universidade.layout')

@section('titulo', 'Painel')

@section('content')
    <header class="ur-page-heading">
        <div>
            <h1>Manifestações</h1>
            <p class="ur-page-description">
                @if (auth()->user()->isAdmin())
                    Caixa de entrada centralizada de todos os serviços.
                @else
                    Caixa de entrada de {{ auth()->user()->provedor->nome }}.
                @endif
            </p>
        </div>
        @if (auth()->user()->isAdmin())
            <a class="ur-btn-secondary" href="{{ route('universidade.administracao.index') }}">Gerir sistema</a>
        @endif
    </header>

    <section class="ur-panel" aria-label="Pesquisa e filtros">
        <form class="ur-filter-grid" method="GET" action="{{ route('universidade.dashboard') }}">
            <div>
                <label for="busca">Referência da manifestação</label>
                <input id="busca" name="busca" value="{{ $filtros['busca'] ?? '' }}" placeholder="Ex.: MAN-2026D0001">
            </div>
            <div>
                <label for="tipo">Tipo</label>
                <select id="tipo" name="tipo">
                    <option value="">Todos os tipos</option>
                    @foreach (['denuncia' => 'Denúncia', 'reclamacao' => 'Reclamação', 'sugestao' => 'Sugestão'] as $valor => $rotulo)
                        <option value="{{ $valor }}" @selected(($filtros['tipo'] ?? '') === $valor)>{{ $rotulo }}</option>
                    @endforeach
                </select>
            </div>
            <input type="hidden" name="estado" value="{{ $filtros['estado'] ?? '' }}">
            <button class="ur-btn-primary" type="submit">Pesquisar</button>
            @if (count($filtros))
                <a class="ur-btn-secondary" href="{{ route('universidade.dashboard') }}">Limpar filtros</a>
            @endif
        </form>
    </section>

    <div id="dashboard-live"
        data-sync-url="{{ route('universidade.dashboard.sincronizar') }}"
        data-versao="{{ $versao }}">
        @include('universidade.partials.dashboard-live')
    </div>
@endsection

@push('scripts')
    <script src="{{ asset('js/universidade-dashboard.js') }}" defer></script>
@endpush
