<!--Manifestacao 2-->
<!DOCTYPE html>
<html lang="pt-PT">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="description" content="Submeter reclamação - Portal de Manifestações da UniRovuma">
    
    <title>Submeter Reclamação | Portal de Manifestações</title>
    
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
                    <div class="ur-page-title-icon" style="background-color: #dbeafe; color: #1e40af;">
                        <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                            <path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z"></path>
                            <polyline points="14 2 14 8 20 8"></polyline>
                            <line x1="16" y1="13" x2="8" y2="13"></line>
                            <line x1="16" y1="17" x2="8" y2="17"></line>
                            <polyline points="10 9 9 9 8 9"></polyline>
                        </svg>
                    </div>
                    Submeter uma reclamação
                </h1>
                <p class="ur-page-description">
                    Utilize este formulário para comunicar um problema, insatisfação ou situação relacionada com serviços, atendimento, procedimentos ou funcionamento institucional da UniRovuma.
                </p>

                <div class="ur-alert">
                    <svg class="ur-alert-icon" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                        <path d="M12 22s8-4 8-10V5l-8-3-8 3v7c0 6 8 10 8 10z"></path>
                    </svg>
                    <div class="ur-alert-content">
                        <div class="ur-alert-title">Informação importante</div>
                        <div class="ur-alert-text">
                            Esta informação será tratada de forma confidencial e encaminhada para análise segundo os procedimentos institucionais aplicáveis. Forneça apenas informações verdadeiras e relevantes para a análise da situação.
                        </div>
                    </div>
                </div>
            </div>

            <!-- Alerta
            
                U
                U
            "U-->
            <!-- Formulario (inicialmente oculto) -->
            <form id="form-reclamacao" class="ur-form" action="{{ route('manifestacao.reclamacao.store') }}" method="POST" enctype="multipart/form-data" style="display: none;">
                @csrf
                
                <!-- Secção: Informação da reclamação -->
                <div class="ur-form-section">
                    <div class="ur-section-header">
                        <h2 class="ur-section-title">
                            <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                <path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z"></path>
                                <polyline points="14 2 14 8 20 8"></polyline>
                            </svg>
                            Informação da reclamação
                        </h2>
                    </div>

                    <div class="ur-form-grid">
                        <div class="ur-form-group full-width">
                            <label for="assunto" class="ur-form-label ur-form-label-required">Assunto da reclamação</label>
                            <input type="text" id="assunto" name="assunto" class="ur-form-input" placeholder="Indique resumidamente o assunto" required>
                            <span class="ur-error-message" id="error-assunto">
                                <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                    <circle cx="12" cy="12" r="10"></circle>
                                    <line x1="15" y1="9" x2="9" y2="15"></line>
                                    <line x1="9" y1="9" x2="15" y2="15"></line>
                                </svg>
                                Por favor, indique o assunto da reclamação
                            </span>
                        </div>

                        <div class="ur-form-group">
                            <label for="categoria" class="ur-form-label ur-form-label-required">Categoria</label>
                            <select id="categoria" name="categoria" class="ur-form-select" required>
                                <option value="">Seleccione uma categoria</option>
                                <option value="atendimento">Atendimento</option>
                                <option value="servicos">Serviços</option>
                                <option value="processos">Processos administrativos</option>
                                <option value="inscricoes">Inscrições</option>
                                <option value="pagamentos">Pagamentos</option>
                                <option value="avaliacao">Avaliação</option>
                                <option value="infraestrutura">Infra-estrutura</option>
                                <option value="sistemas">Sistemas informáticos</option>
                                <option value="comunicacao">Comunicação institucional</option>
                                <option value="outra">Outro</option>
                            </select>
                            <span class="ur-error-message" id="error-categoria">
                                <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                    <circle cx="12" cy="12" r="10"></circle>
                                    <line x1="15" y1="9" x2="9" y2="15"></line>
                                    <line x1="9" y1="9" x2="15" y2="15"></line>
                                </svg>
                                Por favor, seleccione uma categoria
                            </span>
                        </div>

                        <div class="ur-form-group" id="outra-categoria-group" style="display: none;">
                            <label for="outra_categoria" class="ur-form-label">Outra categoria</label>
                            <input type="text" id="outra_categoria" name="outra_categoria" class="ur-form-input" placeholder="Especifique a categoria">
                        </div>

                        <div class="ur-form-group">
                            <label for="area_servico" class="ur-form-label ur-form-label-required">Área ou serviço relacionado</label>
                            <select id="area_servico" name="area_servico" class="ur-form-select" required>
                                <option value="">Seleccione a área ou serviço</option>
                                <option value="atendimento">Atendimento</option>
                                <option value="secretaria">Secretaria</option>
                                <option value="servicos_academicos">Serviços académicos</option>
                                <option value="servicos_administrativos">Serviços administrativos</option>
                                <option value="servicos_financeiros">Serviços financeiros</option>
                                <option value="tic">TIC</option>
                                <option value="biblioteca">Biblioteca</option>
                                <option value="infraestrutura">Infra-estrutura</option>
                                <option value="outro">Outro</option>
                            </select>
                            <div class="ur-form-hint" style="margin-top: 0.5rem;">
                                <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                    <circle cx="12" cy="12" r="10"></circle>
                                    <line x1="12" y1="16" x2="12" y2="12"></line>
                                    <line x1="12" y1="8" x2="12.01" y2="8"></line>
                                </svg>
                                Seleccione a área ou serviço relacionado com a situação. Esta informação será utilizada para orientar o tratamento da reclamação.
                            </div>
                            <span class="ur-error-message" id="error-area_servico">
                                <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                    <circle cx="12" cy="12" r="10"></circle>
                                    <line x1="15" y1="9" x2="9" y2="15"></line>
                                    <line x1="9" y1="9" x2="15" y2="15"></line>
                                </svg>
                                Por favor, seleccione a área ou serviço relacionado
                            </span>
                        </div>

                        <div class="ur-form-group">
                            <label for="local" class="ur-form-label">Local da ocorrência</label>
                            <input type="text" id="local" name="local" class="ur-form-input" placeholder="Indique onde ocorreu a situação">
                        </div>

                        <div class="ur-form-group">
                            <label for="data_ocorrencia" class="ur-form-label">Data da ocorrência</label>
                            <input type="date" id="data_ocorrencia" name="data_ocorrencia" class="ur-form-input">
                        </div>

                        <div class="ur-form-group full-width">
                            <label class="ur-form-label">A situação continua a ocorrer?</label>
                            <div class="ur-radio-group">
                                <input type="radio" id="continua_sim" name="continua" value="sim" class="ur-radio-input">
                                <label for="continua_sim" class="ur-radio-label">Sim</label>

                                <input type="radio" id="continua_nao" name="continua" value="nao" class="ur-radio-input">
                                <label for="continua_nao" class="ur-radio-label">Não</label>

                                <input type="radio" id="continua_nao_sei" name="continua" value="nao_sei" class="ur-radio-input">
                                <label for="continua_nao_sei" class="ur-radio-label">Não sei</label>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Seccao: Descricao da reclamacao -->
                <div class="ur-form-section">
                    <div class="ur-section-header">
                        <h2 class="ur-section-title">
                            <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                <line x1="17" y1="10" x2="3" y2="10"></line>
                                <line x1="21" y1="6" x2="3" y2="6"></line>
                                <line x1="21" y1="14" x2="3" y2="14"></line>
                                <line x1="17" y1="18" x2="3" y2="18"></line>
                            </svg>
                            Descrição da reclamação
                        </h2>
                    </div>

                    <div class="ur-form-group">
                        <label for="descricao" class="ur-form-label ur-form-label-required">Descreva a situação</label>
                        <textarea id="descricao" name="descricao" class="ur-form-textarea" placeholder="Descreva de forma clara o problema ou situação que pretende comunicar. Indique o que aconteceu, quando aconteceu, onde aconteceu e quem ou qual serviço esteve envolvido, quando necessário." required></textarea>
                        <div class="ur-char-counter" id="char-counter">0 / 5000 caracteres</div>
                        <span class="ur-error-message" id="error-descricao">
                            <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                <circle cx="12" cy="12" r="10"></circle>
                                <line x1="15" y1="9" x2="9" y2="15"></line>
                                <line x1="9" y1="9" x2="15" y2="15"></line>
                            </svg>
                            Por favor, descreva a situação
                        </span>
                        <div class="ur-form-hint">
                            <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                <circle cx="12" cy="12" r="10"></circle>
                                <line x1="12" y1="16" x2="12" y2="12"></line>
                                <line x1="12" y1="8" x2="12.01" y2="8"></line>
                            </svg>
                            Inclua apenas informações relevantes para a análise da reclamação.
                        </div>
                    </div>
                </div>

                <!-- Seccao: Impacto da situacao -->
                <div class="ur-form-section">
                    <div class="ur-section-header">
                        <h2 class="ur-section-title">
                            <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                <path d="M12 2v4M12 18v4M4.93 4.93l2.83 2.83M16.24 16.24l2.83 2.83M2 12h4M18 12h4M4.93 19.07l2.83-2.83M16.24 7.76l2.83-2.83"></path>
                            </svg>
                            Impacto da situação
                        </h2>
                    </div>

                    <div class="ur-form-group">
                        <label for="impacto" class="ur-form-label">Como a situação afectou o seu atendimento, serviço ou actividade?</label>
                        <textarea id="impacto" name="impacto" class="ur-form-textarea" rows="4" placeholder="Explique, se aplicável, de que forma esta situação afectou o seu atendimento, o acesso a um serviço, um processo, uma actividade académica ou administrativa."></textarea>
                        <div class="ur-form-hint">
                            <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                <circle cx="12" cy="12" r="10"></circle>
                                <line x1="12" y1="16" x2="12" y2="12"></line>
                                <line x1="12" y1="8" x2="12.01" y2="8"></line>
                            </svg>
                            Este campo é opcional, mas ajuda a compreender a gravidade e a urgência da situação.
                        </div>
                    </div>
                </div>

                <!-- Seccao: O que pretende que seja feito -->
                <div class="ur-form-section">
                    <div class="ur-section-header">
                        <h2 class="ur-section-title">
                            <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                <path d="M22 11.08V12a10 10 0 1 1-5.93-9.14"></path>
                                <polyline points="22 4 12 14.01 9 11.01"></polyline>
                            </svg>
                            O que pretende que seja feito?
                        </h2>
                    </div>

                    <div class="ur-form-group">
                        <label for="pretensao" class="ur-form-label">Qual a solução ou resposta que considera adequada?</label>
                        <textarea id="pretensao" name="pretensao" class="ur-form-textarea" rows="4" placeholder="Indique, se possível, o que gostaria que fosse analisado, esclarecido ou corrigido relativamente à situação apresentada."></textarea>
                        <div class="ur-form-hint">
                            <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                <circle cx="12" cy="12" r="10"></circle>
                                <line x1="12" y1="16" x2="12" y2="12"></line>
                                <line x1="12" y1="8" x2="12.01" y2="8"></line>
                            </svg>
                            Este campo é opcional. A sua sugestão ajudará a orientar a resposta institucional.
                        </div>
                    </div>
                </div>

                <!-- Seccao: Evidencias -->
                <div class="ur-form-section">
                    <div class="ur-section-header">
                        <h2 class="ur-section-title">
                            <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                <path d="M21.44 11.05l-9.19 9.19a6 6 0 0 1-8.49-8.49l9.19-9.19a4 4 0 0 1 5.66 5.66l-9.2 9.19a2 2 0 0 1-2.83-2.83l8.49-8.48"></path>
                            </svg>
                            Evidências
                        </h2>
                    </div>

                    <p style="margin-bottom: var(--ur-spacing-lg); color: var(--ur-gray-600);">
                        Pode anexar documentos, fotografias, capturas de ecrã ou outros ficheiros que possam ajudar na análise da reclamação.
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
                            <path d="M12 22s8-4 8-10V5l-8-3-8 3v7c0 6 8 10 8 10z"></path>
                        </svg>
                        Não envie palavras-passe, dados bancários ou documentos pessoais desnecessários. Máximo de 7 ficheiros, até 20 MB cada.
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
                    
                    <div class="ur-alert" style="margin-bottom: var(--ur-spacing-lg); background-color: #eff6ff; border-left-color: var(--ur-primary);">
                        <svg class="ur-alert-icon" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                            <path d="M12 22s8-4 8-10V5l-8-3-8 3v7c0 6 8 10 8 10z"></path>
                        </svg>
                        <div class="ur-alert-content">
                            <div class="ur-alert-title">Reclamação identificada</div>
                            <div class="ur-alert-text">
                                A sua reclamação será associada aos seus dados de identificação, permitindo estabelecer contacto consigo quando necessário para esclarecimentos ou para comunicar a resposta.
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
                        <div class="ur-privacy-icon">
                            <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                <rect x="3" y="11" width="18" height="11" rx="2" ry="2"></rect>
                                <path d="M7 11V7a5 5 0 0 1 10 0v4"></path>
                            </svg>
                        </div>
                        <div class="ur-privacy-content">
                            <div class="ur-privacy-title">Tratamento confidencial</div>
                            <div class="ur-privacy-text">
                                A reclamação será tratada de forma confidencial e encaminhada para análise segundo os procedimentos institucionais aplicáveis. Identificar-se não significa que os seus dados sejam públicos; significa apenas que a instituição poderá contactá-lo se necessário.
                            </div>
                        </div>
                    </div>

                    <label class="ur-checkbox-group" id="checkbox-confidencialidade">
                        <input type="checkbox" id="confidencialidade" name="confidencialidade" class="ur-checkbox-input" required>
                        <span class="ur-checkbox-label">
                            Li e compreendi as informações sobre o tratamento confidencial da minha reclamação.
                        </span>
                    </label>
                    <span class="ur-error-message" id="error-confidencialidade">
                        <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                            <circle cx="12" cy="12" r="10"></circle>
                            <line x1="15" y1="9" x2="9" y2="15"></line>
                            <line x1="9" y1="9" x2="15" y2="15"></line>
                        </svg>
                        Deve confirmar que leu as informações sobre o tratamento confidencial
                    </span>
                </div>

                <!-- Secção: Revisão e Confirmação -->
                <div class="ur-form-section">
                    <label class="ur-checkbox-group" id="checkbox-revisao">
                        <input type="checkbox" id="confirmacao" name="confirmacao" class="ur-checkbox-input" required>
                        <span class="ur-checkbox-label">
                            <strong>Confirmação de veracidade</strong><br>
                            Confirmo que as informações fornecidas são verdadeiras segundo o meu conhecimento e entendo que a apresentação de informações falsas pode ter consequências.
                        </span>
                    </label>
                    <span class="ur-error-message" id="error-confirmacao">
                        <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                            <circle cx="12" cy="12" r="10"></circle>
                            <line x1="15" y1="9" x2="9" y2="15"></line>
                            <line x1="9" y1="9" x2="15" y2="15"></line>
                        </svg>
                        Deve confirmar que as informações são verdadeiras
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

    <!-- MODAL INICIAL: Escolha do tipo de reclamação -->
    <div id="modal-inicial" class="ur-modal-overlay active" role="dialog" aria-modal="true" aria-labelledby="modal-inicial-titulo">
        <div class="ur-modal ur-modal--wide">
            <div class="ur-modal-header" style="text-align: center; border-bottom: none; padding-bottom: 0;">
                <div style="width: 64px; height: 64px; background-color: #dbeafe; border-radius: 50%; display: flex; align-items: center; justify-content: center; margin: 0 auto 1rem; color: #1e40af;">
                    <svg xmlns="http://www.w3.org/2000/svg" width="32" height="32" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                        <path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z"></path>
                        <polyline points="14 2 14 8 20 8"></polyline>
                        <line x1="16" y1="13" x2="8" y2="13"></line>
                        <line x1="16" y1="17" x2="8" y2="17"></line>
                        <polyline points="10 9 9 9 8 9"></polyline>
                    </svg>
                </div>
                <h3 class="ur-modal-title" id="modal-inicial-titulo" style="font-size: 1.5rem;">Como pretende apresentar a reclamação?</h3>
                <p style="color: var(--ur-gray-600); margin-top: 0.5rem; font-size: 0.875rem;">Seleccione a opção que melhor se adequa à sua situação</p>
            </div>
            
            <div class="ur-modal-body">
                @include('partials.validation-errors')

                <div class="ur-identificacao-opcoes">
                    <!-- Opção Identificar-me -->
                    <label class="ur-identificacao-card" onclick="selecionarTipoReclamacao('identificado')" style="cursor: pointer;">
                        <div class="ur-identificacao-content" style="border: 2px solid var(--ur-gray-200); border-radius: 0.75rem; padding: 1.5rem; transition: all 0.2s ease;">
                            <div class="ur-identificacao-header" style="display: flex; gap: 1rem; align-items: flex-start;">
                                <div style="width: 24px; height: 24px; border: 2px solid var(--ur-gray-300); border-radius: 50%; flex-shrink: 0; margin-top: 2px;" class="radio-indicator" id="radio-identificado"></div>
                                <div class="ur-identificacao-info">
                                    <div class="ur-identificacao-nome" style="font-weight: 700; color: var(--ur-gray-900); font-size: 1rem; margin-bottom: 0.25rem;">Identificar-me</div>
                                    {{-- <div class="ur-identificacao-desc" style="font-size: 0.875rem; color: var(--ur-gray-500); line-height: 1.5;">
                                        A reclamação será associada aos seus dados de identificação, permitindo estabelecer contacto consigo quando necessário. 
                                        <strong style="color: var(--ur-primary);">Recomendado</strong> para obter resposta directa.
                                    </div> --}}
                                </div>
                            </div>
                        </div>
                    </label>

                    <!-- Opção Anónimo -->
                    <label class="ur-identificacao-card" onclick="selecionarTipoReclamacao('anonimo')" style="cursor: pointer;">
                        <div class="ur-identificacao-content" style="border: 2px solid var(--ur-gray-200); border-radius: 0.75rem; padding: 1.5rem; transition: all 0.2s ease;">
                            <div class="ur-identificacao-header" style="display: flex; gap: 1rem; align-items: flex-start;">
                                <div style="width: 24px; height: 24px; border: 2px solid var(--ur-gray-300); border-radius: 50%; flex-shrink: 0; margin-top: 2px;" class="radio-indicator" id="radio-anonimo"></div>
                                <div class="ur-identificacao-info">
                                    <div class="ur-identificacao-nome" style="font-weight: 700; color: var(--ur-gray-900); font-size: 1rem; margin-bottom: 0.25rem;">Reclamar como anónimo</div>
                                    {{-- <div class="ur-identificacao-desc" style="font-size: 0.875rem; color: var(--ur-gray-500); line-height: 1.5;">
                                        A reclamação será apresentada sem associar os seus dados de identificação à manifestação. 
                                        <strong style="color: var(--ur-gray-700);">Não será possível</strong> o contacto para esclarecimentos ou resposta directa.
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
                <h3 class="ur-modal-title" id="modal-revisao-titulo">Revisão da reclamação</h3>
                <button type="button" class="ur-modal-close" aria-label="Fechar modal" onclick="fecharModalRevisao()">
                    <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                        <line x1="18" y1="6" x2="6" y2="18"></line>
                        <line x1="6" y1="6" x2="18" y2="18"></line>
                    </svg>
                </button>
            </div>
            
            <div class="ur-modal-body">
                <!-- Resumo da reclamação -->
                <div class="ur-resumo-denuncia" style="margin-bottom: 1.5rem;">
                    <h4 class="ur-resumo-titulo">Resumo da reclamação</h4>
                    <div class="ur-resumo-item">
                        <span class="ur-resumo-label">Assunto:</span>
                        <span class="ur-resumo-valor" id="resumo-assunto">-</span>
                    </div>
                    <div class="ur-resumo-item">
                        <span class="ur-resumo-label">Categoria:</span>
                        <span class="ur-resumo-valor" id="resumo-categoria">-</span>
                    </div>
                    <div class="ur-resumo-item">
                        <span class="ur-resumo-label">Área ou serviço:</span>
                        <span class="ur-resumo-valor" id="resumo-area">-</span>
                    </div>
                    <div class="ur-resumo-item">
                        <span class="ur-resumo-label">Local:</span>
                        <span class="ur-resumo-valor" id="resumo-local">-</span>
                    </div>
                    <div class="ur-resumo-item">
                        <span class="ur-resumo-label">Data:</span>
                        <span class="ur-resumo-valor" id="resumo-data">-</span>
                    </div>
                    <div class="ur-resumo-item">
                        <span class="ur-resumo-label">Situação continua:</span>
                        <span class="ur-resumo-valor" id="resumo-continua">-</span>
                    </div>
                    <div class="ur-resumo-item">
                        <span class="ur-resumo-label">Forma de apresentação:</span>
                        <span class="ur-resumo-valor" id="resumo-tipo">-</span>
                    </div>
                    <div class="ur-resumo-item" style="flex-direction: column; align-items: flex-start; gap: 0.5rem;">
                        <span class="ur-resumo-label">Descrição:</span>
                        <span class="ur-resumo-valor" id="resumo-descricao" style="max-width: 100%; text-align: left; font-size: 0.875rem; color: var(--ur-gray-600);">-</span>
                    </div>
                    <div class="ur-resumo-item" id="resumo-impacto-item" style="display: none; flex-direction: column; align-items: flex-start; gap: 0.5rem;">
                        <span class="ur-resumo-label">Impacto:</span>
                        <span class="ur-resumo-valor" id="resumo-impacto" style="max-width: 100%; text-align: left; font-size: 0.875rem; color: var(--ur-gray-600);">-</span>
                    </div>
                    <div class="ur-resumo-item" id="resumo-pretensao-item" style="display: none; flex-direction: column; align-items: flex-start; gap: 0.5rem;">
                        <span class="ur-resumo-label">Pretensão:</span>
                        <span class="ur-resumo-valor" id="resumo-pretensao" style="max-width: 100%; text-align: left; font-size: 0.875rem; color: var(--ur-gray-600);">-</span>
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
                            Após submeter, não será possível editar a reclamação. Verifique se todas as informações estão correctas antes de confirmar.
                        </div>
                    </div>
                </div>
            </div>

            <!-- Botões do modal -->
            <div class="ur-modal-footer">
                <button type="button" class="ur-btn ur-btn-text" onclick="fecharModalRevisao()">Voltar e revisar</button>
                <button type="submit" class="ur-btn ur-btn-primary" id="btn-confirmar-submissao" form="form-reclamacao">
                    <svg xmlns="http://www.w3.org/2000/svg" width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                        <polyline points="20 6 9 17 4 12"></polyline>
                    </svg>
                    Submeter reclamação
                </button>
            </div>
        </div>
    </div>

    <!-- JavaScript -->
    <script src="{{ asset('js/reclamacao.js') }}"></script>
</body>

</html>