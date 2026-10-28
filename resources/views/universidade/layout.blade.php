<!DOCTYPE html>
<html lang="pt-PT">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('titulo', 'Gestão de manifestações') | Universidade Rovuma</title>
    <link rel="icon" type="image/x-icon" href="{{ asset('img/ur_con.jpeg') }}">
    <link rel="stylesheet" href="{{ asset('css/style.css') }}">
    <link rel="stylesheet" href="{{ asset('css/universidade.css') }}">
</head>
<body class="ur-universidade">
    <header class="ur-header">
        <div class="ur-header-container">
            <a class="ur-header-brand" href="{{ route('universidade.dashboard') }}">
                <img class="ur-header-logo" src="{{ asset('img/ur_con.jpeg') }}" alt="">
                <span class="ur-header-title">
                    <span class="ur-header-name">Universidade Rovuma</span>
                    <span class="ur-header-subtitle">Gestão de manifestações</span>
                </span>
            </a>
            @auth
                <nav class="ur-nav" aria-label="Navegação institucional">
                    <ul class="ur-nav-menu">
                        <li><a class="ur-nav-link" href="{{ route('universidade.dashboard') }}">Painel</a></li>
                        @if (auth()->user()->isAdmin())
                            <li><a class="ur-nav-link" href="{{ route('universidade.administracao.index') }}">Administração</a></li>
                        @endif
                    </ul>
                    <span class="ur-user-name">{{ auth()->user()->name }}</span>
                    <form method="POST" action="{{ route('universidade.logout') }}">
                        @csrf
                        <button class="ur-btn-logout" type="submit">Terminar sessão</button>
                    </form>
                </nav>
            @endauth
        </div>
    </header>

    <main class="ur-universidade-main">
        <div class="ur-universidade-container">
            @if (session('sucesso'))
                <div class="ur-alert ur-alert--success" role="status">{{ session('sucesso') }}</div>
            @endif
            @if ($errors->any())
                <div class="ur-alert ur-alert--error" role="alert">
                    <strong>Não foi possível concluir a operação:</strong>
                    <ul>@foreach ($errors->all() as $erro)<li>{{ $erro }}</li>@endforeach</ul>
                </div>
            @endif
            @yield('content')
        </div>
    </main>
    @stack('scripts')
</body>
</html>
