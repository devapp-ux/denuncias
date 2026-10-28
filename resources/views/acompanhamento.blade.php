{{-- resources/views/acompanhamento.blade.php --}}

<!DOCTYPE html>
<html lang="pt-PT">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <meta name="description" content="Acompanhar manifestação - Portal de Manifestações da UniRovuma">

    <title>Acompanhar Manifestação | Portal de Manifestações</title>

    <link rel="icon" type="image/x-icon" href="{{ asset('img/ur_con.jpeg') }}">

    <link rel="stylesheet" href="{{ asset('css/manifestacao.css') }}">

    <style>
        .ur-body-blur {
            filter: blur(8px);
            pointer-events: none;
            user-select: none;
        }

        #colar-credenciais[hidden] {
            display: none !important;
        }

        .ur-modal-overlay--blur {
            background-color: rgba(15, 23, 42, 0.75);
            backdrop-filter: blur(4px);
        }

        .ur-modal--center {
            display: flex;
            align-items: center;
            justify-content: center;
            min-height: 100vh;
            padding: 2rem;
        }

        .ur-loading-overlay {
            position: fixed;
            inset: 0;
            background-color: rgba(255, 255, 255, 0.95);
            display: none;
            align-items: center;
            justify-content: center;
            flex-direction: column;
            z-index: 2000;
        }

        .ur-loading-overlay.active {
            display: flex;
        }

        .ur-spinner {
            width: 64px;
            height: 64px;
            border: 4px solid var(--ur-gray-200);
            border-top-color: var(--ur-primary);
            border-radius: 50%;
            animation: spin 1s linear infinite;
        }

        @keyframes spin {
            to { transform: rotate(360deg); }
        }

        .ur-loading-text {
            margin-top: 1.5rem;
            color: var(--ur-gray-600);
            font-size: 1rem;
        }

        /* Layout principal do acompanhamento */
        .ur-acompanhamento-layout {
            display: grid;
            grid-template-columns: 1fr 380px;
            gap: 2rem;
            margin-top: 2rem;
        }

        @media (max-width: 1024px) {
            .ur-acompanhamento-layout {
                grid-template-columns: 1fr;
            }
        }

        /* Timeline */
        .ur-timeline {
            position: relative;
            padding-left: 2rem;
        }

        .ur-timeline::before {
            content: '';
            position: absolute;
            left: 0.5rem;
            top: 0;
            bottom: 0;
            width: 2px;
            background-color: var(--ur-gray-200);
        }

        .ur-timeline-item {
            position: relative;
            padding-bottom: 2rem;
        }

        .ur-timeline-item:last-child {
            padding-bottom: 0;
        }

        .ur-timeline-dot {
            position: absolute;
            left: -1.75rem;
            width: 1rem;
            height: 1rem;
            border-radius: 50%;
            background-color: var(--ur-gray-300);
            border: 2px solid var(--ur-white);
            box-shadow: 0 0 0 2px var(--ur-gray-300);
        }

        .ur-timeline-item.active .ur-timeline-dot {
            background-color: var(--ur-primary);
            box-shadow: 0 0 0 2px var(--ur-primary);
        }

        .ur-timeline-item.completed .ur-timeline-dot {
            background-color: var(--ur-success);
            box-shadow: 0 0 0 2px var(--ur-success);
        }

        .ur-timeline-content {
            background-color: var(--ur-white);
            border: 1px solid var(--ur-gray-200);
            border-radius: 0.75rem;
            padding: 1.25rem;
        }

        .ur-timeline-item.active .ur-timeline-content {
            border-color: var(--ur-primary);
            box-shadow: 0 0 0 1px var(--ur-primary);
        }

        .ur-timeline-date {
            font-size: 0.75rem;
            color: var(--ur-gray-500);
            margin-bottom: 0.25rem;
        }

        .ur-timeline-title {
            font-weight: 600;
            color: var(--ur-gray-900);
            margin-bottom: 0.5rem;
        }

        .ur-timeline-desc {
            font-size: 0.875rem;
            color: var(--ur-gray-600);
            line-height: 1.5;
        }

        /* Mini Chat */
        .ur-chat-container {
            background-color: var(--ur-white);
            border: 1px solid var(--ur-gray-200);
            border-radius: 0.75rem;
            height: 600px;
            display: flex;
            flex-direction: column;
        }

        .ur-chat-header {
            padding: 1rem 1.25rem;
            border-bottom: 1px solid var(--ur-gray-200);
            display: flex;
            align-items: center;
            gap: 0.75rem;
        }

        .ur-chat-status {
            width: 8px;
            height: 8px;
            border-radius: 50%;
            background-color: var(--ur-success);
        }

        .ur-chat-title {
            font-weight: 600;
            color: var(--ur-gray-900);
            font-size: 0.875rem;
        }

        .ur-chat-subtitle {
            font-size: 0.75rem;
            color: var(--ur-gray-500);
        }

        .ur-chat-messages {
            flex: 1;
            overflow-y: auto;
            padding: 1.25rem;
            display: flex;
            flex-direction: column;
            gap: 1rem;
        }

        .ur-chat-message {
            max-width: 85%;
            padding: 0.75rem 1rem;
            border-radius: 0.75rem;
            font-size: 0.875rem;
            line-height: 1.5;
        }

        .ur-chat-message--received {
            align-self: flex-start;
            background-color: var(--ur-gray-100);
            color: var(--ur-gray-800);
            border-bottom-left-radius: 0.25rem;
        }

        .ur-chat-message--sent {
            align-self: flex-end;
            background-color: var(--ur-primary);
            color: var(--ur-white);
            border-bottom-right-radius: 0.25rem;
        }

        .ur-chat-message-time {
            font-size: 0.6875rem;
            opacity: 0.7;
            margin-top: 0.25rem;
        }

        .ur-chat-input-area {
            padding: 1rem 1.25rem;
            border-top: 1px solid var(--ur-gray-200);
            display: flex;
            gap: 0.75rem;
        }

        .ur-chat-input {
            flex: 1;
            padding: 0.625rem 1rem;
            border: 1px solid var(--ur-gray-300);
            border-radius: 1.5rem;
            font-size: 0.875rem;
            outline: none;
            transition: border-color 0.2s;
        }

        .ur-chat-input:focus {
            border-color: var(--ur-primary);
        }

        .ur-chat-send {
            width: 40px;
            height: 40px;
            border-radius: 50%;
            background-color: var(--ur-primary);
            color: var(--ur-white);
            border: none;
            cursor: pointer;
            display: flex;
            align-items: center;
            justify-content: center;
            transition: background-color 0.2s;
        }

        .ur-chat-send:hover {
            background-color: var(--ur-primary-dark);
        }

        .ur-chat-send:disabled {
            opacity: 0.5;
            cursor: not-allowed;
        }

        /* Info card */
        .ur-info-card {
            background-color: var(--ur-white);
            border: 1px solid var(--ur-gray-200);
            border-radius: 0.75rem;
            padding: 1.5rem;
            margin-bottom: 1.5rem;
        }

        .ur-info-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
            gap: 1.5rem;
        }

        .ur-info-item {
            display: flex;
            flex-direction: column;
            gap: 0.25rem;
        }

        .ur-info-label {
            font-size: 0.75rem;
            color: var(--ur-gray-500);
            text-transform: uppercase;
            letter-spacing: 0.05em;
        }

        .ur-info-value {
            font-weight: 600;
            color: var(--ur-gray-900);
            font-size: 0.9375rem;
        }

        .ur-status-badge {
            display: inline-flex;
            align-items: center;
            gap: 0.5rem;
            padding: 0.375rem 0.875rem;
            border-radius: 9999px;
            font-size: 0.875rem;
            font-weight: 500;
        }

        .ur-status-badge--analise {
            background-color: #fef3c7;
            color: #92400e;
        }

        .ur-status-badge--respondida {
            background-color: #dcfce7;
            color: #14532d;
        }

        .ur-status-badge--pendente {
            background-color: #dbeafe;
            color: #1e3a8a;
        }

        /* Código input estilizado */
        .ur-codigo-input {
            font-size: 1.5rem;
            letter-spacing: 0.5rem;
            text-align: center;
            font-weight: 600;
            text-transform: uppercase;
        }

        @media (max-width: 480px) {
            .ur-header-container {
                padding: 0.625rem 0.75rem;
                gap: 0.5rem;
            }

            .ur-header-logo {
                width: 38px;
                height: 38px;
            }

            .ur-header-brand {
                min-width: 0;
                gap: 0.5rem;
            }

            .ur-header-title {
                min-width: 0;
            }

            .ur-header-name {
                font-size: 1rem;
            }

            .ur-header-subtitle {
                font-size: 0.625rem;
                white-space: normal;
                line-height: 1.2;
            }

            .ur-nav {
                display: flex;
                position: static;
                flex-direction: row;
                transform: none;
                padding: 0;
                box-shadow: none;
                background: transparent;
                align-items: center;
            }

            .ur-menu-toggle {
                display: none;
            }

            .ur-btn-logout {
                min-height: 42px;
                padding: 0.5rem;
                font-size: 0;
            }

            .ur-btn-logout svg {
                width: 20px;
                height: 20px;
            }

            .ur-main {
                padding-top: 64px;
            }

            .ur-container {
                padding: 0.75rem;
            }

            .ur-page-title {
                align-items: flex-start;
                gap: 0.5rem;
                font-size: 1.4rem;
            }

            .ur-page-title-icon {
                width: 40px;
                height: 40px;
                flex: 0 0 40px;
            }

            .ur-page-description {
                font-size: 0.95rem;
            }

            .ur-info-grid {
                grid-template-columns: minmax(0, 1fr);
            }

            .ur-info-card {
                padding: 1rem;
            }

            .ur-chat-container {
                height: min(70vh, 560px);
                min-height: 380px;
            }

            .ur-chat-input-area {
                gap: 0.5rem;
                padding: 0.75rem;
            }

            .ur-chat-input {
                min-width: 0;
                font-size: 16px;
            }

            .ur-timeline {
                padding-left: 1.75rem;
            }

            .ur-codigo-input {
                font-size: 1rem;
                letter-spacing: 0.12rem;
            }

            .ur-modal-overlay {
                align-items: center;
                padding: 0.5rem;
            }

            .ur-modal-overlay .ur-modal {
                width: 100% !important;
                max-width: 480px !important;
                max-height: calc(100dvh - 1rem);
                overflow-y: auto;
            }

            .ur-modal-header,
            .ur-modal-body {
                padding: 1rem;
            }

            .ur-form-input {
                font-size: 16px;
            }
        }
    </style>
</head>

<body>
    <!-- Skip link -->
    <a href="#conteudo-principal" class="ur-skip-link">Saltar para o conteúdo principal</a>

    <header class="ur-header">
        <div class="ur-header-container">
            <div class="ur-header-brand">
                <img src="{{ asset('img/ur_con.jpeg') }}" alt="Logótipo da UniRovuma" class="ur-header-logo">
                <div class="ur-header-title">
                    <span class="ur-header-name">UniRovuma</span>
                    <span class="ur-header-subtitle">Portal de Manifestações</span>
                </div>
            </div>

            <nav class="ur-nav">
                <button class="ur-btn-logout">
                    <svg xmlns="http://www.w3.org/2000/svg" width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                        <path d="M9 21H5a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2h4"></path>
                        <polyline points="16 17 21 12 16 7"></polyline>
                        <line x1="21" y1="12" x2="9" y2="12"></line>
                    </svg>
                    Sair
                </button>
            </nav>

            <button class="ur-menu-toggle" aria-label="Abrir menu">
                <span class="ur-menu-bar"></span>
                <span class="ur-menu-bar"></span>
                <span class="ur-menu-bar"></span>
            </button>
        </div>
    </header>

    <!-- Loading Overlay -->
    <div id="loading-overlay" class="ur-loading-overlay">
        <div class="ur-spinner"></div>
        <p class="ur-loading-text">A carregar informações...</p>
    </div>

    <!-- Modal Inicial: Inserir Código -->
    <div id="modal-codigo" class="ur-modal-overlay ur-modal-overlay--blur active" role="dialog" aria-modal="true" aria-labelledby="modal-codigo-titulo"
        data-consulta-url="{{ route('acompanhamento.consultar') }}"
        data-sincronizar-url="{{ route('acompanhamento.sincronizar') }}"
        data-mensagem-url="{{ route('acompanhamento.mensagem', ['codigo' => '__codigo__']) }}">
        <div class="ur-modal" style="max-width: 480px; width: 100%;">
            <div class="ur-modal-header" style="text-align: center;">
                <div style="width: 64px; height: 64px; background-color: #dbeafe; border-radius: 50%; display: flex; align-items: center; justify-content: center; margin: 0 auto 1rem; color: #1e40af;">
                    <svg xmlns="http://www.w3.org/2000/svg" width="32" height="32" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                        <circle cx="12" cy="12" r="10"></circle>
                        <polyline points="12 6 12 12 16 14"></polyline>
                    </svg>
                </div>
                <h3 class="ur-modal-title" id="modal-codigo-titulo" style="font-size: 1.5rem;">Acompanhar Manifestação</h3>
                <p style="color: var(--ur-gray-600); margin-top: 0.5rem; font-size: 0.875rem;">
                    Introduza o código e a senha de acompanhamento que constam no comprovativo em PDF.
                </p>
            </div>

            <div class="ur-modal-body">
                <div class="ur-form-group" style="margin-bottom: 1.5rem;">
                    <label for="codigo-manifestacao" class="ur-form-label" style="text-align: center; display: block;">Código de acompanhamento</label>
                    <input
                        type="text"
                        id="codigo-manifestacao"
                        class="ur-form-input ur-codigo-input"
                        placeholder="MAN-2026D0001"
                        maxlength="13"
                        autocomplete="off"
                    >
                    <label for="senha-manifestacao" class="ur-form-label" style="text-align: center; display: block; margin-top: 1rem;">Senha de acompanhamento</label>
                    <input
                        type="password"
                        id="senha-manifestacao"
                        class="ur-form-input"
                        placeholder="Introduza a senha do comprovativo"
                        maxlength="10"
                        required
                        autocomplete="off"
                    >
                    <button type="button" id="colar-credenciais" class="ur-btn ur-btn-secondary" style="width:100%;margin-top:.75rem;" hidden>
                        Colar
                    </button>
                    <span id="estado-colagem" role="status" aria-live="polite" style="display:block;text-align:center;font-size:.8rem;margin-top:.5rem;"></span>
                    <span class="ur-error-message" id="error-codigo" style="text-align: center; justify-content: center;">
                        <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                            <circle cx="12" cy="12" r="10"></circle>
                            <line x1="15" y1="9" x2="9" y2="15"></line>
                            <line x1="9" y1="9" x2="15" y2="15"></line>
                        </svg>
                        <span class="ur-error-texto">Por favor, insira o código de acompanhamento</span>
                    </span>
                    
                    <p style="text-align: center; font-size: 0.75rem; color: var(--ur-gray-500); margin-top: 0.75rem;">
                        Copie o código e a senha do comprovativo e cole-os num dos campos para os preencher automaticamente.
                    </p>
                </div>

                <button type="button" class="ur-btn ur-btn-primary" onclick="consultarManifestacao()" style="width: 100%;">
                    <svg xmlns="http://www.w3.org/2000/svg" width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" style="margin-right: 0.5rem;">
                        <circle cx="11" cy="11" r="8"></circle>
                        <line x1="21" y1="21" x2="16.65" y2="16.65"></line>
                    </svg>
                    Consultar
                </button>
            </div>
        </div>
    </div>

    <!-- Conteúdo Principal (inicialmente desfocado) -->
    <main id="conteudo-principal" class="ur-main ur-body-blur">
        <div class="ur-container">

            <div class="ur-page-header">
                <h1 class="ur-page-title">
                    <div class="ur-page-title-icon" style="background-color: #dbeafe; color: #1e40af;">
                        <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                            <circle cx="12" cy="12" r="10"></circle>
                            <polyline points="12 6 12 12 16 14"></polyline>
                        </svg>
                    </div>
                    Acompanhar Manifestação
                </h1>
                <p class="ur-page-description">
                    Consulte o estado e o histórico de desenvolvimento da sua manifestação. Utilize o espaço de chat para comunicar com a instituição.
                </p>
            </div>

            <!-- Informações da Manifestação -->
            <div class="ur-info-card">
                <div class="ur-info-grid">
                    <div class="ur-info-item">
                        <span class="ur-info-label">Código</span>
                        <span class="ur-info-value" id="info-codigo">-</span>
                    </div>
                    <div class="ur-info-item">
                        <span class="ur-info-label">Tipo</span>
                        <span class="ur-info-value" id="info-tipo">-</span>
                    </div>
                    <div class="ur-info-item">
                        <span class="ur-info-label">Data de submissão</span>
                        <span class="ur-info-value" id="info-data">-</span>
                    </div>
                    <div class="ur-info-item">
                        <span class="ur-info-label">Estado actual</span>
                        <span class="ur-status-badge ur-status-badge--pendente" id="info-status">
                            <span style="width: 6px; height: 6px; background-color: currentColor; border-radius: 50%;"></span>
                            -
                        </span>
                    </div>
                </div>
            </div>

            <!-- Layout Principal: Timeline + Chat -->
            <div class="ur-acompanhamento-layout">

                <!-- Coluna da Esquerda: Timeline -->
                <div class="ur-timeline-column">
                    <h2 class="ur-section-title" style="margin-bottom: 1.5rem; font-size: 1.125rem;">
                        <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" style="width: 20px; height: 20px; vertical-align: middle; margin-right: 0.5rem;">
                            <path d="M12 20v-6M6 20V10M18 20V4"></path>
                        </svg>
                        Decurso da Manifestação
                    </h2>

                    <div class="ur-timeline" id="timeline-container">
                        <!-- O JavaScript preenche aqui -->
                    </div>

                    <!-- Documentos/Anexos da manifestação -->
                    <div style="margin-top: 2rem;">
                        <h3 style="font-size: 1rem; font-weight: 600; color: var(--ur-gray-900); margin-bottom: 1rem;">
                            Documentos associados
                        </h3>
                        <div id="documentos-container" style="background-color: var(--ur-white); border: 1px solid var(--ur-gray-200); border-radius: 0.75rem; padding: 1rem;">
                            <!-- O JavaScript preenche aqui -->
                        </div>
                    </div>
                </div>

                <!-- Coluna da Direita: Mini Chat -->
                <div class="ur-chat-column">
                    <div class="ur-chat-container">
                        <div class="ur-chat-header">
                            <div class="ur-chat-status"></div>
                            <div>
                                <div class="ur-chat-title">Chat com a instituição</div>
                                <div class="ur-chat-subtitle">
                                    Tempo de resposta: até 48 horas úteis ·
                                    <span id="acompanhamento-sync-status" role="status">Atualizações automáticas ativas</span>
                                </div>
                            </div>
                        </div>

                        <div class="ur-chat-messages" id="chat-messages">
                            <!-- O JavaScript preenche aqui -->
                        </div>

                        <div class="ur-chat-input-area">
                            <input
                                type="text"
                                class="ur-chat-input"
                                id="chat-input"
                                placeholder="Escreva a sua mensagem..."
                                maxlength="500"
                            >
                            <button class="ur-chat-send" id="btn-enviar" onclick="enviarMensagem()">
                                <svg xmlns="http://www.w3.org/2000/svg" width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                    <line x1="22" y1="2" x2="11" y2="13"></line>
                                    <polygon points="22 2 15 22 11 13 2 9 22 2"></polygon>
                                </svg>
                            </button>
                        </div>
                    </div>
                </div>

            </div>
        </div>
    </main>

    <!-- JavaScript -->
    <script src="{{ asset('js/acompanhamento.js') }}"></script>
</body>

</html>