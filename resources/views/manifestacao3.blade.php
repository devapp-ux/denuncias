{{-- resources/views/sugestao.blade.php --}}

<!DOCTYPE html>
<html lang="pt-PT">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="description" content="Submeter sugestão - Portal de Manifestações da UniRovuma">
    
    <title>Submeter Sugestão | Portal de Manifestações</title>
    
    <link rel="icon" type="image/x-icon" href="{{ asset('img/ur_con.jpeg') }}">
    
    <link rel="stylesheet" href="{{ asset('css/manifestacao.css') }}">
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

    <main id="conteudo-principal" class="ur-main">
        <div class="ur-container">
            
            <div class="ur-page-header" id="page-header" style="display: none;">
                <h1 class="ur-page-title">
                    <div class="ur-page-title-icon" style="background-color: #dcfce7; color: #15803d;">
                        <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                            <path d="M9.663 17h4.673M12 3v1m6.364 1.636l-.707.707M21 12h-1M4 12H3m3.343-5.657l-.707-.707m2.828 9.9a5 5 0 117.072 0l-.548.547A3.374 3.374 0 0014 18.469V19a2 2 0 11-4 0v-.531c0-.895-.356-1.754-.988-2.386l-.548-.547z"></path>
                        </svg>
                    </div>
                    Submeter uma sugestão
                </h1>
                <p class="ur-page-description">
                    Partilhe as suas ideias, propostas de melhoria ou sugestões para aprimorar os serviços, processos ou funcionamento da UniRovuma.
                </p>

                <div class="ur-alert">
                    <svg class="ur-alert-icon" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                        <path d="M12 22s8-4 8-10V5l-8-3-8 3v7c0 6 8 10 8 10z"></path>
                    </svg>
                    <div class="ur-alert-content">
                        <div class="ur-alert-title">Informação importante</div>
                        <div class="ur-alert-text">
                            As sugestões serão analisadas e consideradas no âmbito das competências institucionais. A apresentação de uma sugestão não garante a sua implementação, mas todas serão devidamente avaliadas.
                        </div>
                    </div>
                </div>
            </div>

            <!-- Formulario (inicialmente oculto) -->
            <form id="form-sugestao" class="ur-form" action="{{ route('manifestacao.sugestao.store') }}" method="POST" enctype="multipart/form-data" style="display: none;">
                @csrf
                
                <!-- Secção: Informação da sugestão -->
                <div class="ur-form-section">
                    <div class="ur-section-header">
                        <h2 class="ur-section-title">
                            <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                <path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z"></path>
                                <polyline points="14 2 14 8 20 8"></polyline>
                            </svg>
                            Informação da sugestão
                        </h2>
                    </div>

                    <div class="ur-form-grid">
                        <div class="ur-form-group full-width">
                            <label for="titulo" class="ur-form-label ur-form-label-required">Título da sugestão</label>
                            <input type="text" id="titulo" name="titulo" class="ur-form-input" placeholder="Indique um título claro e resumido" required>
                            <span class="ur-error-message" id="error-titulo">
                                <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                    <circle cx="12" cy="12" r="10"></circle>
                                    <line x1="15" y1="9" x2="9" y2="15"></line>
                                    <line x1="9" y1="9" x2="15" y2="15"></line>
                                </svg>
                                Por favor, indique o título da sugestão
                            </span>
                        </div>

                        <div class="ur-form-group full-width">
                            <label for="area_relacionada" class="ur-form-label ur-form-label-required">Local ou área relacionada</label>
                            <select id="area_relacionada" name="area_relacionada" class="ur-form-select" required>
                                <option value="">Seleccione a área ou serviço</option>
                                <option value="atendimento">Atendimento</option>
                                <option value="secretaria">Secretaria</option>
                                <option value="servicos_academicos">Serviços académicos</option>
                                <option value="servicos_administrativos">Serviços administrativos</option>
                                <option value="servicos_financeiros">Serviços financeiros</option>
                                <option value="tic">TIC - Tecnologias de Informação</option>
                                <option value="biblioteca">Biblioteca</option>
                                <option value="infraestrutura">Infra-estrutura e instalações</option>
                                <option value="comunicacao">Comunicação institucional</option>
                                <option value="processos">Processos e procedimentos</option>
                                <option value="ambiente">Ambiente e sustentabilidade</option>
                                <option value="outra">Outra área</option>
                            </select>
                            <div class="ur-form-hint" style="margin-top: 0.5rem;">
                                <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                    <circle cx="12" cy="12" r="10"></circle>
                                    <line x1="12" y1="16" x2="12" y2="12"></line>
                                    <line x1="12" y1="8" x2="12.01" y2="8"></line>
                                </svg>
                                Indique a área ou serviço ao qual a sua sugestão diz respeito.
                            </div>
                            <span class="ur-error-message" id="error-area_relacionada">
                                <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                    <circle cx="12" cy="12" r="10"></circle>
                                    <line x1="15" y1="9" x2="9" y2="15"></line>
                                    <line x1="9" y1="9" x2="15" y2="15"></line>
                                </svg>
                                Por favor, seleccione a área relacionada
                            </span>
                        </div>

                        <div class="ur-form-group full-width" id="outra-area-group" style="display: none;">
                            <label for="outra_area" class="ur-form-label">Especifique a área</label>
                            <input type="text" id="outra_area" name="outra_area" class="ur-form-input" placeholder="Indique qual a área ou serviço">
                        </div>
                    </div>
                </div>

                <!-- Seccao: A sugestao -->
                <div class="ur-form-section">
                    <div class="ur-section-header">
                        <h2 class="ur-section-title">
                            <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                <path d="M11 4H4a2 2 0 0 0-2 2v14a2 2 0 0 0 2 2h14a2 2 0 0 0 2-2v-7"></path>
                                <path d="M18.5 2.5a2.121 2.121 0 0 1 3 3L12 15l-4 1 1-4 9.5-9.5z"></path>
                            </svg>
                            A sugestão
                        </h2>
                    </div>

                    <div class="ur-form-group">
                        <label for="sugestao" class="ur-form-label ur-form-label-required">Descreva a sua sugestão</label>
                        <textarea id="sugestao" name="sugestao" class="ur-form-textarea" placeholder="Descreva de forma clara a sua ideia ou proposta de melhoria. Explique o que sugere e como poderia ser implementada." required></textarea>
                        <div class="ur-char-counter" id="char-counter-sugestao">0 / 3000 caracteres</div>
                        <span class="ur-error-message" id="error-sugestao">
                            <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                <circle cx="12" cy="12" r="10"></circle>
                                <line x1="15" y1="9" x2="9" y2="15"></line>
                                <line x1="9" y1="9" x2="15" y2="15"></line>
                            </svg>
                            Por favor, descreva a sua sugestão
                        </span>
                        <div class="ur-form-hint">
                            <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                <circle cx="12" cy="12" r="10"></circle>
                                <line x1="12" y1="16" x2="12" y2="12"></line>
                                <line x1="12" y1="8" x2="12.01" y2="8"></line>
                            </svg>
                            Seja específico e objectivo na descrição da sua proposta.
                        </div>
                    </div>
                </div>

                <!-- Seccao: Justificacao -->
                <div class="ur-form-section">
                    <div class="ur-section-header">
                        <h2 class="ur-section-title">
                            <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                <line x1="8" y1="6" x2="21" y2="6"></line>
                                <line x1="8" y1="12" x2="21" y2="12"></line>
                                <line x1="8" y1="18" x2="21" y2="18"></line>
                                <line x1="3" y1="6" x2="3.01" y2="6"></line>
                                <line x1="3" y1="12" x2="3.01" y2="12"></line>
                                <line x1="3" y1="18" x2="3.01" y2="18"></line>
                            </svg>
                            Justificação
                        </h2>
                    </div>

                    <div class="ur-form-group">
                        <label for="justificacao" class="ur-form-label">Por que é importante ou útil?</label>
                        <textarea id="justificacao" name="justificacao" class="ur-form-textarea" rows="4" placeholder="Explique os benefícios ou vantagens da sua sugestão. Como esta melhoria poderia ajudar a comunidade académica ou o funcionamento da instituição?"></textarea>
                        <div class="ur-char-counter" id="char-counter-justificacao">0 / 2000 caracteres</div>
                        <span class="ur-error-message" id="error-justificacao"></span>
                        <div class="ur-form-hint">
                            <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                <circle cx="12" cy="12" r="10"></circle>
                                <line x1="12" y1="16" x2="12" y2="12"></line>
                                <line x1="12" y1="8" x2="12.01" y2="8"></line>
                            </svg>
                            Este campo é opcional, mas uma boa justificação aumenta a compreensão e valor da sua sugestão.
                        </div>
                    </div>
                </div>

                <!-- Seccao: Anexos (opcional) -->
                <div class="ur-form-section">
                    <div class="ur-section-header">
                        <h2 class="ur-section-title">
                            <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                <path d="M21.44 11.05l-9.19 9.19a6 6 0 0 1-8.49-8.49l9.19-9.19a4 4 0 0 1 5.66 5.66l-9.2 9.19a2 2 0 0 1-2.83-2.83l8.49-8.48"></path>
                            </svg>
                            Anexos (opcional)
                        </h2>
                    </div>

                    <p style="margin-bottom: var(--ur-spacing-lg); color: var(--ur-gray-600);">
                        Se desejar, pode anexar documentos, imagens ou outros ficheiros que ilustrem ou complementem a sua sugestão.
                    </p>

                    <div class="ur-upload-area" id="upload-area">
                        <svg class="ur-upload-icon" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                            <path d="M21 15v4a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2v-4"></path>
                            <polyline points="17 8 12 3 7 8"></polyline>
                            <line x1="12" y1="3" x2="12" y2="15"></line>
                        </svg>
                        <div class="ur-upload-text">Arraste os ficheiros para aqui</div>
                        <div class="ur-upload-subtext">ou</div>
                        <button type="button" class="ur-btn-upload" onclick="document.getElementById('file-input').click()">
                            <svg xmlns="http://www.w3.org/2000/svg" width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                <path d="M22 19a2 2 0 0 1-2 2H4a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2h5l2 3h9a2 2 0 0 1 2 2z"></path>
                            </svg>
                            Seleccionar ficheiros
                        </button>
                        <button type="button" class="ur-btn-upload" onclick="document.getElementById('camera-input').click()">
                            Tirar fotografia
                        </button>
                        <input type="file" id="file-input" name="evidencias[]" class="ur-upload-input" multiple accept="image/*,.pdf,.doc,.docx,.xls,.xlsx,.ppt,.pptx,.mp4,.mp3">
                        <input type="file" id="camera-input" class="ur-upload-input" accept="image/*" capture="environment">
                    </div>

                    <div class="ur-file-list" id="file-list"></div>

                    <div class="ur-form-hint" style="margin-top: var(--ur-spacing-lg);">
                        <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                            <circle cx="12" cy="12" r="10"></circle>
                            <line x1="12" y1="16" x2="12" y2="12"></line>
                            <line x1="12" y1="8" x2="12.01" y2="8"></line>
                        </svg>
                        Máximo de 7 ficheiros, até 20 MB cada. Formatos aceites: PDF, Office, imagens, vídeo e áudio.
                    </div>
                </div>

                <!--SECCAO: Seleccao do Provedor -->
                <div class="ur-form-section ur-provedor-section">
                    <div class="ur-section-header">
                        <h2 class="ur-section-title">
                            <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                <path d="M17 21v-2a4 4 0 0 0-4-4H5a4 4 0 0 0-4 4v2"></path>
                                <circle cx="9" cy="7" r="4"></circle>
                                <path d="M23 21v-2a4 4 0 0 0-3-3.87"></path>
                                <path d="M16 3.13a4 4 0 0 1 0 7.75"></path>
                            </svg>
                            Encaminhar para o Provedor de...
                        </h2>
                    </div>
                    
                    <div class="ur-provedor-info">
                        <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                            <circle cx="12" cy="12" r="10"></circle>
                            <line x1="12" y1="16" x2="12" y2="12"></line>
                            <line x1="12" y1="8" x2="12.01" y2="8"></line>
                        </svg>
                        <p>Seleccione o Provedor responsável pela área à qual a sua manifestação está relacionada. Esta escolha ajudará a encaminhar a manifestação para a entidade adequada.</p>
                    </div>

                    <div class="ur-form-group">
                        <label class="ur-form-label ur-form-label-required">Provedor</label>
                        <div class="ur-provedor-grid">
                            @forelse ($provedores as $provedor)
                                <label class="ur-provedor-card">
                                    <input type="radio" name="provedor_id" value="{{ $provedor->id }}" class="ur-provedor-input" required @checked(old('provedor_id') == $provedor->id)>
                                    <div class="ur-provedor-content">
                                        <div class="ur-provedor-title">{{ $provedor->nome }}</div>
                                    </div>
                                </label>
                            @empty
                                <p role="alert">Não há provedores ativos disponíveis para encaminhar a manifestação.</p>
                            @endforelse
                        </div>
                        <span class="ur-error-message" id="error-provedor_id">
                            <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                <circle cx="12" cy="12" r="10"></circle>
                                <line x1="15" y1="9" x2="9" y2="15"></line>
                                <line x1="9" y1="9" x2="15" y2="15"></line>
                            </svg>
                            Por favor, seleccione um provedor
                        </span>
                    </div>
                </div>

                <!-- Secção: Dados de Identificação (condicional) -->
                <div class="ur-form-section" id="secao-identificacao" style="display: none;">
                    <div class="ur-section-header">
                        <h2 class="ur-section-title">
                            <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                <path d="M20 21v-2a4 4 0 0 0-4-4H8a4 4 0 0 0-4 4v2"></path>
                                <circle cx="12" cy="7" r="4"></circle>
                            </svg>
                            Dados de identificação
                        </h2>
                    </div>
                    
                    <div class="ur-alert" style="margin-bottom: var(--ur-spacing-lg); background-color: #f0fdf4; border-left-color: #15803d;">
                        <svg class="ur-alert-icon" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" style="color: #15803d;">
                            <path d="M9.663 17h4.673M12 3v1m6.364 1.636l-.707.707M21 12h-1M4 12H3m3.343-5.657l-.707-.707m2.828 9.9a5 5 0 117.072 0l-.548.547A3.374 3.374 0 0014 18.469V19a2 2 0 11-4 0v-.531c0-.895-.356-1.754-.988-2.386l-.548-.547z"></path>
                        </svg>
                        <div class="ur-alert-content">
                            <div class="ur-alert-title" style="color: #14532d;">Sugestão identificada</div>
                            <div class="ur-alert-text" style="color: #14532d;">
                                A sua sugestão será associada aos seus dados, permitindo que a instituição possa contactá-lo para agradecer ou esclarecer dúvidas sobre a proposta.
                            </div>
                        </div>
                    </div>

                    <div class="ur-form-grid">
                        <div class="ur-form-group">
                            <label for="nome" class="ur-form-label ur-form-label-required">Nome completo</label>
                            <input type="text" id="nome" name="nome" class="ur-form-input" placeholder="O seu nome completo">
                            <span class="ur-error-message" id="error-nome">
                                <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                    <circle cx="12" cy="12" r="10"></circle>
                                    <line x1="15" y1="9" x2="9" y2="15"></line>
                                    <line x1="9" y1="9" x2="15" y2="15"></line>
                                </svg>
                                Por favor, indique o seu nome
                            </span>
                        </div>
                        
                        <div class="ur-form-group">
                            <label for="email" class="ur-form-label ur-form-label-required">E-mail</label>
                            <input type="email" id="email" name="email" class="ur-form-input" placeholder="o.seu.email@exemplo.com">
                            <span class="ur-error-message" id="error-email">
                                <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                    <circle cx="12" cy="12" r="10"></circle>
                                    <line x1="15" y1="9" x2="9" y2="15"></line>
                                    <line x1="9" y1="9" x2="15" y2="15"></line>
                                </svg>
                                Por favor, indique um e-mail válido
                            </span>
                        </div>
                        
                        <div class="ur-form-group">
                            <label for="celular" class="ur-form-label">Contacto telefónico</label>
                            <input type="tel" id="celular" name="celular" class="ur-form-input" placeholder="+258 84 000 0000">
                        </div>
                    </div>
                </div>

                <!-- Campo hidden para tipo de identificação -->
                <input type="hidden" id="tipo_identificacao" name="tipo_identificacao" value="">

                <!-- Seccao: Confidencialidade -->
                <div class="ur-form-section">
                    <div class="ur-privacy-card">
                        <div class="ur-privacy-icon" style="background-color: #15803d;">
                            <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                <rect x="3" y="11" width="18" height="11" rx="2" ry="2"></rect>
                                <path d="M7 11V7a5 5 0 0 1 10 0v4"></path>
                            </svg>
                        </div>
                        <div class="ur-privacy-content">
                            <div class="ur-privacy-title">Tratamento das sugestões</div>
                            <div class="ur-privacy-text">
                                As sugestões serão analisadas pelos serviços competentes. A identificação permite o contacto para esclarecimentos ou agradecimento, mas não afeta o tratamento confidencial da proposta.
                            </div>
                        </div>
                    </div>

                    <label class="ur-checkbox-group" id="checkbox-confidencialidade">
                        <input type="checkbox" id="confidencialidade" name="confidencialidade" class="ur-checkbox-input" required>
                        <span class="ur-checkbox-label">
                            Li e compreendi as informações sobre o tratamento da minha sugestão.
                        </span>
                    </label>
                    <span class="ur-error-message" id="error-confidencialidade">
                        <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                            <circle cx="12" cy="12" r="10"></circle>
                            <line x1="15" y1="9" x2="9" y2="15"></line>
                            <line x1="9" y1="9" x2="15" y2="15"></line>
                        </svg>
                        Deve confirmar que leu as informações
                    </span>
                </div>

                <!-- Secção: Confirmação -->
                <div class="ur-form-section">
                    <label class="ur-checkbox-group" id="checkbox-revisao">
                        <input type="checkbox" id="confirmacao" name="confirmacao" class="ur-checkbox-input" required>
                        <span class="ur-checkbox-label">
                            <strong>Confirmação de veracidade</strong><br>
                            Confirmo que as informações fornecidas são verdadeiras e que a sugestão é apresentada de boa-fé.
                        </span>
                    </label>
                    <span class="ur-error-message" id="error-confirmacao">
                        <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                            <circle cx="12" cy="12" r="10"></circle>
                            <line x1="15" y1="9" x2="9" y2="15"></line>
                            <line x1="9" y1="9" x2="15" y2="15"></line>
                        </svg>
                        Deve confirmar a veracidade das informações
                    </span>
                </div>

                <!-- Botões -->
                <div class="ur-form-actions">
                    <button type="button" class="ur-btn ur-btn-text" onclick="history.back()">Cancelar</button>
                    <button type="button" class="ur-btn ur-btn-secondary" onclick="guardarRascunho()">
                        <svg xmlns="http://www.w3.org/2000/svg" width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                            <path d="M19 21H5a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2h11l5 5v11a2 2 0 0 1-2 2z"></path>
                            <polyline points="17 21 17 13 7 13 7 21"></polyline>
                            <polyline points="7 3 7 8 15 8"></polyline>
                        </svg>
                        Guardar e continuar depois
                    </button>

                    <button type="button"
                            id="btn-submeter"
                            class="ur-btn ur-btn-primary"
                            onclick="abrirModalRevisao()">
                        Revisar e submeter
                    </button>
                </div>
            </form>
        </div>
    </main>

    <!-- MODAL INICIAL: Escolha do tipo de manifestação -->
    <div id="modal-inicial" class="ur-modal-overlay active" role="dialog" aria-modal="true" aria-labelledby="modal-inicial-titulo">
        <div class="ur-modal ur-modal--wide">
            <div class="ur-modal-header" style="text-align: center; border-bottom: none; padding-bottom: 0;">
                <div style="width: 64px; height: 64px; background-color: #dcfce7; border-radius: 50%; display: flex; align-items: center; justify-content: center; margin: 0 auto 1rem; color: #15803d;">
                    <svg xmlns="http://www.w3.org/2000/svg" width="32" height="32" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                        <path d="M9.663 17h4.673M12 3v1m6.364 1.636l-.707.707M21 12h-1M4 12H3m3.343-5.657l-.707-.707m2.828 9.9a5 5 0 117.072 0l-.548.547A3.374 3.374 0 0014 18.469V19a2 2 0 11-4 0v-.531c0-.895-.356-1.754-.988-2.386l-.548-.547z"></path>
                    </svg>
                </div>
                <h3 class="ur-modal-title" id="modal-inicial-titulo" style="font-size: 1.5rem;">Como deseja apresentar a sugestão?</h3>
                <p style="color: var(--ur-gray-600); margin-top: 0.5rem; font-size: 0.875rem;">Seleccione a forma como pretende submeter a sua proposta</p>
            </div>
            
            <div class="ur-modal-body">
                @include('partials.validation-errors')

                <div class="ur-identificacao-opcoes">
                    <!-- Opção Identificar-me -->
                    <label class="ur-identificacao-card" onclick="selecionarTipoSugestao('identificado')" style="cursor: pointer;">
                        <div class="ur-identificacao-content" style="border: 2px solid var(--ur-gray-200); border-radius: 0.75rem; padding: 1.5rem; transition: all 0.2s ease;">
                            <div class="ur-identificacao-header" style="display: flex; gap: 1rem; align-items: flex-start;">
                                <div style="width: 24px; height: 24px; border: 2px solid var(--ur-gray-300); border-radius: 50%; flex-shrink: 0; margin-top: 2px;" class="radio-indicator" id="radio-identificado"></div>
                                <div class="ur-identificacao-info">
                                    <div class="ur-identificacao-nome" style="font-weight: 700; color: var(--ur-gray-900); font-size: 1rem; margin-bottom: 0.25rem;">Manifestar-me como identificado</div>
                                    {{-- <div class="ur-identificacao-desc" style="font-size: 0.875rem; color: var(--ur-gray-500); line-height: 1.5;">
                                        A sugestão será associada aos seus dados de identificação, permitindo que a instituição o contacte para esclarecimentos ou agradecimento.
                                    </div> --}}
                                </div>
                            </div>
                        </div>
                    </label>

                    <!-- Opção Anónimo -->
                    <label class="ur-identificacao-card" onclick="selecionarTipoSugestao('anonimo')" style="cursor: pointer;">
                        <div class="ur-identificacao-content" style="border: 2px solid var(--ur-gray-200); border-radius: 0.75rem; padding: 1.5rem; transition: all 0.2s ease;">
                            <div class="ur-identificacao-header" style="display: flex; gap: 1rem; align-items: flex-start;">
                                <div style="width: 24px; height: 24px; border: 2px solid var(--ur-gray-300); border-radius: 50%; flex-shrink: 0; margin-top: 2px;" class="radio-indicator" id="radio-anonimo"></div>
                                <div class="ur-identificacao-info">
                                    <div class="ur-identificacao-nome" style="font-weight: 700; color: var(--ur-gray-900); font-size: 1rem; margin-bottom: 0.25rem;">Manifestar-me anonimamente</div>
                                    {{-- <div class="ur-identificacao-desc" style="font-size: 0.875rem; color: var(--ur-gray-500); line-height: 1.5;">
                                        A sugestão será apresentada sem associar os seus dados pessoais. Não será possível contactá-lo para esclarecimentos.
                                    </div> --}}
                                </div>
                            </div>
                        </div>
                    </label>
                </div>
            </div>
        </div>
    </div>

    <!-- Modal de revisão e confirmação final -->
    <div id="modal-revisao" class="ur-modal-overlay" role="dialog" aria-modal="true" aria-labelledby="modal-revisao-titulo" aria-hidden="true">
        <div class="ur-modal ur-modal--wide" style="max-width: 700px;">
            <div class="ur-modal-header">
                <h3 class="ur-modal-title" id="modal-revisao-titulo">Revisão da sugestão</h3>
                <button type="button" class="ur-modal-close" aria-label="Fechar modal" onclick="fecharModalRevisao()">
                    <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                        <line x1="18" y1="6" x2="6" y2="18"></line>
                        <line x1="6" y1="6" x2="18" y2="18"></line>
                    </svg>
                </button>
            </div>
            
            <div class="ur-modal-body">
                <!-- Resumo da sugestão -->
                <div class="ur-resumo-denuncia" style="margin-bottom: 1.5rem;">
                    <h4 class="ur-resumo-titulo">Resumo da proposta</h4>
                    <div class="ur-resumo-item">
                        <span class="ur-resumo-label">Título:</span>
                        <span class="ur-resumo-valor" id="resumo-titulo">-</span>
                    </div>
                    <div class="ur-resumo-item">
                        <span class="ur-resumo-label">Área relacionada:</span>
                        <span class="ur-resumo-valor" id="resumo-area">-</span>
                    </div>
                    <div class="ur-resumo-item" style="flex-direction: column; align-items: flex-start; gap: 0.5rem;">
                        <span class="ur-resumo-label">Sugestão:</span>
                        <span class="ur-resumo-valor" id="resumo-sugestao" style="max-width: 100%; text-align: left; font-size: 0.875rem; color: var(--ur-gray-600);">-</span>
                    </div>
                    <div class="ur-resumo-item" id="resumo-justificacao-item" style="display: none; flex-direction: column; align-items: flex-start; gap: 0.5rem;">
                        <span class="ur-resumo-label">Justificação:</span>
                        <span class="ur-resumo-valor" id="resumo-justificacao" style="max-width: 100%; text-align: left; font-size: 0.875rem; color: var(--ur-gray-600);">-</span>
                    </div>
                    <div class="ur-resumo-item">
                        <span class="ur-resumo-label">Forma de apresentação:</span>
                        <span class="ur-resumo-valor" id="resumo-tipo">-</span>
                    </div>
                    <div class="ur-resumo-item" id="resumo-anexos-item" style="display: none;">
                        <span class="ur-resumo-label">Anexos:</span>
                        <span class="ur-resumo-valor" id="resumo-anexos">-</span>
                    </div>
                </div>

                <div class="ur-alert" style="background-color: #fef3c7; border-left-color: #d97706;">
                    <svg class="ur-alert-icon" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" style="color: #d97706;">
                        <path d="M10.29 3.86L1.82 18a2 2 0 0 0 1.71 3h16.94a2 2 0 0 0 1.71-3L13.71 3.86a2 2 0 0 0-3.42 0z"></path>
                        <line x1="12" y1="9" x2="12" y2="13"></line>
                        <line x1="12" y1="17" x2="12.01" y2="17"></line>
                    </svg>
                    <div class="ur-alert-content">
                        <div class="ur-alert-title" style="color: #92400e;">Confirmação final</div>
                        <div class="ur-alert-text" style="color: #92400e;">
                            Após submeter, não será possível editar a sugestão. Verifique se todas as informações estão correctas antes de confirmar.
                        </div>
                    </div>
                </div>
            </div>

            <!-- Botões do modal -->
            <div class="ur-modal-footer">
                <button type="button" class="ur-btn ur-btn-text" onclick="fecharModalRevisao()">Voltar e revisar</button>
                <button type="submit" class="ur-btn ur-btn-primary" id="btn-confirmar-submissao" form="form-sugestao">
                    <svg xmlns="http://www.w3.org/2000/svg" width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                        <polyline points="20 6 9 17 4 12"></polyline>
                    </svg>
                    Submeter sugestão
                </button>
            </div>
        </div>
    </div>

    <!-- JavaScript -->
    <script src="{{ asset('js/sugestao.js') }}"></script>
</body>

</html>