<!DOCTYPE html>
<html lang="pt-MZ">

<head>

    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <meta
        name="description"
        content="Plataforma institucional de manifestacao de denúncias, reclamações e sugestões da UniRovuma"
    >

    <meta name="author" content="Fortunato da Silva Felix">
    <meta name="theme-color" content="#1e3a5f">

    <link
        rel="icon"
        type="image/x-icon"
        href="{{ asset('img/ur_con.jpeg') }}"
    >

    <title>Portal de Denuncias | UniRovuma</title>

    <!-- Estilo GLOBAL -->
    <link
        rel="stylesheet"
        href="{{ asset('css/style.css') }}"
    >
    <!-- Estilo DA PAGINA -->
    <link rel="stylesheet" href="{{ asset('css/home.css') }}">

</head>

<body>

    <!-- SKIP LINK -->
    <a href="#conteudo-principal" class="ur-skip-link">
        Saltar para o conteúdo principal
    </a>

    <!-- CABEÇALHO DA UNIROVUMA -->
    <header class="ur-header" role="banner">

        <div class="ur-header-container">

            <!-- Marca da UR -->
            <a href="#" class="ur-header-brand" aria-label="UniRovuma - Portal de Denuncias">
                <img
                    src="{{ asset('img/ur_con.jpeg') }}"
                    alt="Logótipo da UniRovuma"
                    class="ur-header-logo"
                    width="48"
                    height="48"
                >
                <div class="ur-header-title">
                    <span class="ur-header-name">Universidade Rovuma</span>
                    <span class="ur-header-subtitle">Portal de Denuncias</span>
                </div>
            </a>

            <!-- Navegação -->
            <nav id="navegacao-principal" class="ur-nav" role="navigation" aria-label="Navegação principal">

                <ul class="ur-nav-menu">
                    <li>
                        <a href="#inicio" class="ur-nav-link">Início</a>
                    </li>
                    <li>
                        <a href="#como-funciona" class="ur-nav-link">Como funciona</a>
                    </li>
                    <li>
                        <a href="#faq" class="ur-nav-link">Perguntas frequentes</a>
                    </li>
                </ul>
                
                
                <div class="ur-header-action">
                    <a href="{{ route('universidade.login') }}" class="ur-btn-track">
                        Acesso institucional
                    </a>
                    <a href="{{ url('dash1') }}" class="ur-btn-track">
                        <svg xmlns="http://www.w3.org/1000/svg" width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true">
                            <circle cx="12" cy="12" r="10"></circle>
                            <polyline points="12 6 12 12 16 14"></polyline>
                        </svg>
                        Codigo de Acompanhamento
                    </a>
                </div>

            </nav>

            <!-- Botao menu mobile -->
            <button
                class="ur-menu-toggle"
                aria-label="Abrir menu de navegação"
                aria-expanded="false"
                aria-controls="navegacao-principal"
            >
                <span class="ur-menu-bar"></span>
                <span class="ur-menu-bar"></span>
                <span class="ur-menu-bar"></span>
            </button>

        </div>

    </header>


    <main id="conteudo-principal">

        <!-- APRESENTACAO PRINCIPAL -->

        <section class="ur-hero" id="inicio" aria-labelledby="hero-titulo">

            <div class="ur-hero-pattern" aria-hidden="true"></div>

            <div class="ur-hero-container">

                <h1 class="ur-hero-title" id="hero-titulo">
                    A sua voz pode fazer a <span class="ur-hero-title-accent">diferença</span>
                </h1>

                <p class="ur-hero-description">
                    Utilize este canal para comunicar situações, apresentar reclamações e propor sugestões à Univercidade Rovuma. A sua participação contribui para a melhoria contínua da nossa instituição.
                </p>

                <div class="ur-hero-actions">
                    <a href="#tipos" class="ur-btn ur-btn-primary">
                        <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true">
                            <line x1="12" y1="5" x2="12" y2="19"></line>
                            <line x1="5" y1="12" x2="19" y2="12"></line>
                        </svg>
                        Nova manifestação
                    </a>
                    <a href="#acompanhar" class="ur-btn ur-btn-secondary">
                        <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true">
                            <circle cx="12" cy="12" r="10"></circle>
                            <polyline points="12 6 12 12 16 14"></polyline>
                        </svg>
                        Acompanhar manifestação
                    </a>
                </div>

            </div>

            <a href="#tipos" class="ur-hero-scroll" aria-label="Descer para tipos de manifestação">
                <span>Descobrir mais</span>
                <svg class="ur-hero-scroll-icon" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true">
                    <polyline points="6 9 12 15 18 9"></polyline>
                </svg>
            </a>

        </section>


        <!-- TIPOS DE MANIFESTAÇÃO -->

        <section class="ur-section ur-types" id="tipos" aria-labelledby="tipos-titulo">

            <div class="ur-types-container">

                <div class="ur-types-grid" role="list">

                    <!-- Cartão: Denúncia -->
                    <article class="ur-type-card" role="listitem">
                        <div class="ur-type-icon ur-type-icon--denuncia" aria-hidden="true">
                            <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                <path d="M10.29 3.86L1.82 18a2 2 0 0 0 1.71 3h16.94a2 2 0 0 0 1.71-3L13.71 3.86a2 2 0 0 0-3.42 0z"></path>
                                <line x1="12" y1="9" x2="12" y2="13"></line>
                                <line x1="12" y1="17" x2="12.01" y2="17"></line>
                            </svg>
                        </div>
                        <h3 class="ur-type-title">Denúncia</h3>
                        <p class="ur-type-description">
                            Comunique uma situação que considere irregular ou que deva ser analisada pela instituição. As denúncias são tratadas com a devida seriedade e confidencialidade.
                        </p>
                        <div class="ur-type-action">
                            <a href="man1" class="ur-btn-card">
                                Fazer denúncia
                                <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true">
                                    <line x1="5" y1="12" x2="19" y2="12"></line>
                                    <polyline points="12 5 19 12 12 19"></polyline>
                                </svg>
                            </a>
                        </div>
                    </article>

                    <!-- Cartão: Reclamação -->
                    <article class="ur-type-card" role="listitem">
                        <div class="ur-type-icon ur-type-icon--reclamacao" aria-hidden="true">
                            <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                <circle cx="12" cy="12" r="10"></circle>
                                <line x1="12" y1="8" x2="12" y2="12"></line>
                                <line x1="12" y1="16" x2="12.01" y2="16"></line>
                            </svg>
                        </div>
                        <h3 class="ur-type-title">Reclamação</h3>
                        <p class="ur-type-description">
                            Apresente uma reclamação sobre um serviço, situação ou experiência institucional. A sua opinião é fundamental para identificarmos oportunidades de melhoria.
                        </p>
                        <div class="ur-type-action">
                            <a href="man2" class="ur-btn-card">
                                Apresentar reclamação
                                <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true">
                                    <line x1="5" y1="12" x2="19" y2="12"></line>
                                    <polyline points="12 5 19 12 12 19"></polyline>
                                </svg>
                            </a>
                        </div>
                    </article>

                    <!-- Cartão: Sugestão -->
                    <article class="ur-type-card" role="listitem">
                        <div class="ur-type-icon ur-type-icon--sugestao" aria-hidden="true">
                            <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                <path d="M12 2L2 7l10 5 10-5-10-5zM2 17l10 5 10-5M2 12l10 5 10-5"></path>
                            </svg>
                        </div>
                        <h3 class="ur-type-title">Sugestão</h3>
                        <p class="ur-type-description">
                            Partilhe uma ideia ou proposta para melhorar a instituição. Valorizamos a criatividade e o empenho da nossa comunidade na construção de uma UniRovuma melhor.
                        </p>
                        <div class="ur-type-action">
                            <a href="man3" class="ur-btn-card">
                                Enviar sugestão
                                <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true">
                                    <line x1="5" y1="12" x2="19" y2="12"></line>
                                    <polyline points="12 5 19 12 12 19"></polyline>
                                </svg>
                            </a>
                        </div>
                    </article>

                </div>

            </div>

        </section>


        <!-- COMO FUNCIONA (PROCESSO) -->

        <section class="ur-section ur-process" id="como-funciona" aria-labelledby="processo-titulo">

            <div class="ur-process-container">

                <header class="ur-section-header">
                    <span class="ur-section-label">Processo</span>
                    <h2 class="ur-section-title" id="processo-titulo">Como funciona?</h2>
                    <p class="ur-section-description">
                        O processo é simples e transparente. Em três passos, a sua manifestação é registada, analisada e acompanhada até à resolução.
                    </p>
                </header>

                <div class="ur-steps">

                    <!-- Passo 1 -->
                    <article class="ur-step">
                        <div class="ur-step-number" aria-hidden="true">01</div>
                        <div class="ur-step-content">
                            <h3 class="ur-step-title">Submeta</h3>
                            <p class="ur-step-description">
                                Envie a sua manifestação através do canal adequado e forneça as informações necessárias para o tratamento do seu caso.
                            </p>
                        </div>
                    </article>

                    <!-- Passo 2 -->
                    <article class="ur-step">
                        <div class="ur-step-number" aria-hidden="true">02</div>
                        <div class="ur-step-content">
                            <h3 class="ur-step-title">Analisamos</h3>
                            <p class="ur-step-description">
                                A manifestação será encaminhada para a entidade responsável, de acordo com os procedimentos definidos pela instituição.
                            </p>
                        </div>
                    </article>

                    <!-- Passo 3 -->
                    <article class="ur-step">
                        <div class="ur-step-number" aria-hidden="true">03</div>
                        <div class="ur-step-content">
                            <h3 class="ur-step-title">Acompanhe</h3>
                            <p class="ur-step-description">
                                Consulte posteriormente o estado e as actualizações da sua manifestação através do código de acompanhamento.
                            </p>
                        </div>
                    </article>

                </div>

            </div>

        </section>


        <!-- ACOMPANHAMENTO -->

        <section class="ur-section ur-track" id="acompanhar" aria-labelledby="acompanhar-titulo">

            <div class="ur-track-container">

                <div class="ur-track-icon" aria-hidden="true">
                    <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                        <circle cx="12" cy="12" r="10"></circle>
                        <polyline points="12 6 12 12 16 14"></polyline>
                    </svg>
                </div>

                <h2 class="ur-track-title" id="acompanhar-titulo">Já submeteu uma manifestação?</h2>

                <p class="ur-track-description">
                    Acompanhe o estado e as actualizações do seu caso em tempo real. Introduza o código de acompanhamento para consultar o progresso da sua manifestação.
                </p>

                <a href="dash1" class="ur-btn-track-large">
                    <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true">
                        <circle cx="12" cy="12" r="10"></circle>
                        <polyline points="12 6 12 12 16 14"></polyline>
                    </svg>
                    Codigo de Manifestacao
                </a>
                
            </div>

        </section>


        <!-- CONFIDENCIALIDADE -->

        <section class="ur-privacy" aria-labelledby="privacidade-titulo">

            <div class="ur-privacy-container">

                <div class="ur-privacy-card">

                    <div class="ur-privacy-icon" aria-hidden="true">
                        <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                            <rect x="3" y="11" width="18" height="11" rx="2" ry="2"></rect>
                            <path d="M7 11V7a5 5 0 0 1 10 0v4"></path>
                        </svg>
                    </div>

                    <div class="ur-privacy-content">
                        <h2 class="ur-privacy-title" id="privacidade-titulo">Privacidade e confidencialidade</h2>
                        <p class="ur-privacy-text">
                            As Denuncias serão tratadas de acordo com as regras e procedimentos institucionais aplicáveis. A Universidade Rovuma compromete-se a analisar todas as comunicações com a devida seriedade e a salvaguardar a informação conforme a legislação em vigor.
                        </p>
                    </div>

                </div>

            </div>

        </section>


    </main>

    <!-- Roda pe -->
    <footer class="ur-footer" role="contentinfo">

        <div class="ur-footer-container">

            <div class="ur-footer-main">

                <div class="ur-footer-brand">
                    <div class="ur-footer-logo">
                        <img
                            src="{{ asset('img/ur_con.jpeg') }}"
                            alt="UniRovuma"
                            width="48"
                            height="48"
                        >
                        <span class="ur-footer-logo-text">UniRovuma</span>
                    </div>
                    <p class="ur-footer-description">
                        Universidade Rovuma — Comprometida com a excelência académica, a investigação e o desenvolvimento da sociedade moçambicana.
                    </p>
                </div>

                <!-- Links rapidos -->
                <nav class="ur-footer-links" aria-label="Links rápidos">
                    <h3 class="ur-footer-heading">Links rápidos</h3>
                    <a href="#inicio" class="ur-footer-link">Página inicial</a>
                    <a href="#tipos" class="ur-footer-link">Nova manifestação</a>
                    <a href="#acompanhar" class="ur-footer-link">Acompanhar caso</a>
                    <a href="#faq" class="ur-footer-link">Perguntas frequentes</a>
                </nav>

                <!-- Informacoes legais -->
                <nav class="ur-footer-links" aria-label="Informações legais">
                    <h3 class="ur-footer-heading">Informações</h3>
                    <a href="#" class="ur-footer-link">Política de privacidade</a>
                    <a href="#" class="ur-footer-link">Termos de utilização</a>
                    <a href="#" class="ur-footer-link">Regulamento interno</a>
                    <a href="#" class="ur-footer-link">Contactos</a>
                </nav>

            </div>

            <div class="ur-footer-bottom">

                <p class="ur-footer-copyright">
                    &copy; 2026 UniRovuma. Todos os direitos reservados.
                </p>

                <nav class="ur-footer-legal" aria-label="Links legais">
                    <a href="#" class="ur-footer-legal-link">Privacidade</a>
                    <a href="#" class="ur-footer-legal-link">Termos</a>
                    <a href="#" class="ur-footer-legal-link">Contactos</a>
                </nav>

            </div>

        </div>

    </footer>


    <!-- JS GLOBAL -->
    <script src="{{ asset('js/main.js') }}"></script>
    <!-- JS da Pagina -->
    <script src="{{ asset('js/home.js') }}"></script>
</body>

</html>