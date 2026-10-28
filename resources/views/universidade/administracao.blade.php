@extends('universidade.layout')

@section('titulo', 'Administração')

@section('content')
    <div class="ur-admin-page">
        <header class="ur-page-heading">
            <div>
                <span class="ur-admin-eyebrow">Gestão do sistema</span>
                <h1>Administração</h1>
                <p class="ur-page-description">Gira os acessos da equipa e os serviços da Universidade num só lugar.</p>
            </div>
            <a class="ur-btn-secondary" href="{{ route('universidade.dashboard') }}">Voltar ao painel</a>
        </header>

        <nav class="ur-summary-grid ur-admin-summary" aria-label="Resumo da administração">
            <div class="ur-summary-item"><span>Contas registadas</span><strong>{{ $utilizadores->count() }}</strong></div>
            <div class="ur-summary-item"><span>Contas ativas</span><strong>{{ $utilizadores->where('ativo', true)->count() }}</strong></div>
            <div class="ur-summary-item"><span>Serviços</span><strong>{{ $provedores->count() }}</strong></div>
            <div class="ur-summary-item"><span>Serviços ativos</span><strong>{{ $provedores->where('ativo', true)->count() }}</strong></div>
        </nav>

        <section class="ur-panel ur-admin-panel">
            <div class="ur-admin-section-heading">
                <div>
                    <h2>Contas de acesso</h2>
                    <p class="ur-muted">Crie e gira os perfis da equipa. O registo público está desativado.</p>
                </div>
                <span class="ur-admin-count">{{ $utilizadores->count() }} {{ $utilizadores->count() === 1 ? 'conta' : 'contas' }}</span>
            </div>

        <details class="ur-disclosure ur-admin-create">
            <summary><span class="ur-admin-plus" aria-hidden="true">+</span><span><strong>Criar nova conta</strong><small>Adicionar um administrador ou um provedor</small></span></summary>
            <form class="ur-form-grid" method="POST" action="{{ route('universidade.administracao.utilizadores.guardar') }}">
                @csrf
                <div class="ur-field"><label for="novo-name">Nome completo</label><input id="novo-name" name="name" value="{{ old('name') }}" required></div>
                <div class="ur-field"><label for="novo-email">E-mail</label><input id="novo-email" type="email" name="email" value="{{ old('email') }}" required></div>
                <div class="ur-field">
                    <label for="novo-role">Perfil de acesso</label>
                    <select id="novo-role" name="role_id" required>
                        <option value="provedor">Provedor</option>
                        <option value="admin">Administrador</option>
                    </select>
                </div>
                <div class="ur-field">
                    <label for="novo-provedor">Serviço</label>
                    <select id="novo-provedor" name="provedor_id">
                        <option value="">Selecione o serviço</option>
                        @foreach ($provedores->where('ativo', true) as $provedor)
                            <option value="{{ $provedor->id }}">{{ $provedor->nome }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="ur-field"><label for="nova-password">Palavra-passe inicial</label><input id="nova-password" type="password" name="password" minlength="12" autocomplete="new-password" required></div>
                <div class="ur-field"><label for="nova-password-confirmation">Confirmar palavra-passe</label><input id="nova-password-confirmation" type="password" name="password_confirmation" minlength="12" autocomplete="new-password" required></div>
                <div><button class="ur-btn-primary" type="submit">Criar conta</button></div>
            </form>
        </details>

        <div class="ur-admin-list">
            @foreach ($utilizadores as $utilizador)
                <article class="ur-admin-row">
                    <div>
                        <strong>{{ $utilizador->name }}</strong>
                        <div class="ur-muted">{{ $utilizador->email }}</div>
                    </div>
                    <span class="ur-status">{{ $utilizador->role_id === 'admin' ? 'Administrador' : 'Provedor' }}{{ $utilizador->provedor ? ' · '.$utilizador->provedor->nome : '' }}</span>
                    <span class="ur-status {{ $utilizador->ativo ? 'ur-status--respondida' : 'ur-status--arquivada' }}">{{ $utilizador->ativo ? 'Ativa' : 'Inativa' }}</span>
                    <details class="ur-admin-edit">
                        <summary>Editar conta</summary>
                        <form class="ur-form-grid" method="POST" action="{{ route('universidade.administracao.utilizadores.atualizar', $utilizador) }}">
                            @csrf
                            @method('PATCH')
                            <div class="ur-field"><label for="nome-{{ $utilizador->id }}">Nome completo</label><input id="nome-{{ $utilizador->id }}" name="name" value="{{ $utilizador->name }}" required></div>
                            <div class="ur-field"><label for="email-{{ $utilizador->id }}">E-mail</label><input id="email-{{ $utilizador->id }}" type="email" name="email" value="{{ $utilizador->email }}" required></div>
                            <div class="ur-field">
                                <label for="perfil-{{ $utilizador->id }}">Perfil de acesso</label>
                                <select id="perfil-{{ $utilizador->id }}" name="role_id" required>
                                    <option value="provedor" @selected($utilizador->role_id === 'provedor')>Provedor</option>
                                    <option value="admin" @selected($utilizador->role_id === 'admin')>Administrador</option>
                                </select>
                            </div>
                            <div class="ur-field">
                                <label for="servico-{{ $utilizador->id }}">Serviço</label>
                                <select id="servico-{{ $utilizador->id }}" name="provedor_id">
                                    <option value="">Sem serviço</option>
                                    @foreach ($provedores as $provedor)
                                        <option value="{{ $provedor->id }}" @selected($utilizador->provedor_id == $provedor->id)>{{ $provedor->nome }}{{ $provedor->ativo ? '' : ' (inativo)' }}</option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="ur-field"><label for="password-{{ $utilizador->id }}">Nova palavra-passe (opcional)</label><input id="password-{{ $utilizador->id }}" type="password" name="password" minlength="12" autocomplete="new-password"></div>
                            <div class="ur-field"><label for="confirmacao-{{ $utilizador->id }}">Confirmar palavra-passe</label><input id="confirmacao-{{ $utilizador->id }}" type="password" name="password_confirmation" minlength="12" autocomplete="new-password"></div>
                            <input type="hidden" name="ativo" value="0">
                            <label class="ur-checkbox-line"><input type="checkbox" name="ativo" value="1" @checked($utilizador->ativo)> Conta ativa</label>
                            <div><button class="ur-btn-primary" type="submit">Guardar conta</button></div>
                        </form>
                    </details>
                </article>
            @endforeach
        </div>
        </section>

        <section class="ur-panel ur-admin-panel">
            <div class="ur-admin-section-heading">
                <div>
                    <h2>Serviços provedores</h2>
                    <p class="ur-muted">Defina os serviços responsáveis por receber as manifestações.</p>
                </div>
                <span class="ur-admin-count">{{ $provedores->count() }} {{ $provedores->count() === 1 ? 'serviço' : 'serviços' }}</span>
            </div>

        <details class="ur-disclosure ur-admin-create">
            <summary><span class="ur-admin-plus" aria-hidden="true">+</span><span><strong>Adicionar novo serviço</strong><small>Registar um serviço provedor da Universidade</small></span></summary>
            <form class="ur-form-grid" method="POST" action="{{ route('universidade.administracao.provedores.guardar') }}">
                @csrf
                <div class="ur-field"><label for="provedor-nome">Nome do serviço</label><input id="provedor-nome" name="nome" required></div>
                <div class="ur-field"><label for="provedor-email">E-mail de notificação</label><input id="provedor-email" type="email" name="email"></div>
                <div><button class="ur-btn-primary" type="submit">Adicionar serviço</button></div>
            </form>
        </details>

        <div class="ur-admin-list">
            @foreach ($provedores as $provedor)
                <article class="ur-admin-row">
                    <div>
                        <strong>{{ $provedor->nome }}</strong>
                        <div class="ur-muted">{{ $provedor->email ?: 'Sem e-mail de notificação' }} · {{ $provedor->utilizadores_count }} conta(s)</div>
                    </div>
                    <span class="ur-status {{ $provedor->ativo ? 'ur-status--respondida' : 'ur-status--arquivada' }}">{{ $provedor->ativo ? 'Ativo' : 'Inativo' }}</span>
                    <details class="ur-admin-edit">
                        <summary>Editar serviço</summary>
                        <form class="ur-form-grid" method="POST" action="{{ route('universidade.administracao.provedores.atualizar', $provedor) }}">
                            @csrf
                            @method('PATCH')
                            <div class="ur-field"><label for="provedor-nome-{{ $provedor->id }}">Nome do serviço</label><input id="provedor-nome-{{ $provedor->id }}" name="nome" value="{{ $provedor->nome }}" required></div>
                            <div class="ur-field"><label for="provedor-email-{{ $provedor->id }}">E-mail de notificação</label><input id="provedor-email-{{ $provedor->id }}" type="email" name="email" value="{{ $provedor->email }}"></div>
                            <div class="ur-field">
                                <label for="provedor-ativo-{{ $provedor->id }}">Estado do serviço</label>
                                <select id="provedor-ativo-{{ $provedor->id }}" name="ativo">
                                    <option value="1" @selected($provedor->ativo)>Ativo</option>
                                    <option value="0" @selected(! $provedor->ativo)>Inativo</option>
                                </select>
                            </div>
                            <div><button class="ur-btn-primary" type="submit">Guardar serviço</button></div>
                        </form>
                    </details>
                </article>
            @endforeach
        </div>
        </section>
    </div>
@endsection
