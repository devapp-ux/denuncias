@extends('universidade.layout')

@section('titulo', 'Acesso institucional')

@section('content')
    <section class="ur-login-card">
        <h1>Acesso institucional</h1>
        <p class="ur-page-description">Área reservada aos provedores e à administração universitária.</p>
        <form method="POST" action="{{ route('universidade.login.store') }}">
            @csrf
            <div class="ur-field">
                <label for="email">E-mail institucional</label>
                <input id="email" type="email" name="email" value="{{ old('email') }}" autocomplete="username" required autofocus>
            </div>
            <div class="ur-field">
                <label for="password">Palavra-passe</label>
                <input id="password" type="password" name="password" autocomplete="current-password" required>
            </div>
            <label class="ur-checkbox-line"><input type="checkbox" name="remember" value="1"> Manter sessão iniciada</label>
            <p><button class="ur-btn-primary" type="submit">Iniciar sessão</button></p>
        </form>
        <a href="{{ route('home') }}">Voltar ao canal público</a>
    </section>
@endsection
