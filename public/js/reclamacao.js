/**
 * Portal de Manifestações - UniRovuma
 * JavaScript do Formulário de Reclamação
 * Versão 1.0
 * Autor: fortuna!
 */

// ============================================
// CONFIGURAÇÃO
// ============================================
const CONFIG = {
    maxFileSize: 20 * 1024 * 1024,
    maxFiles: 7,
    maxChars: 5000,
    allowedTypes: [
        'application/pdf',
        'image/jpeg', 'image/png', 'image/jpg',
        'application/msword',
        'application/vnd.openxmlformats-officedocument.wordprocessingml.document',
        'application/vnd.ms-excel',
        'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet',
        'application/vnd.ms-powerpoint',
        'application/vnd.openxmlformats-officedocument.presentationml.presentation',
        'video/mp4',
        'audio/mpeg'
    ],
    allowedExtensions: ['pdf', 'jpg', 'jpeg', 'png', 'webp', 'heic', 'heif', 'doc', 'docx', 'xls', 'xlsx', 'ppt', 'pptx', 'mp4', 'mp3'],
    categorias: {
        'atendimento': 'Atendimento',
        'servicos': 'Serviços',
        'processos': 'Processos administrativos',
        'inscricoes': 'Inscrições',
        'pagamentos': 'Pagamentos',
        'avaliacao': 'Avaliação',
        'infraestrutura': 'Infra-estrutura',
        'sistemas': 'Sistemas informáticos',
        'comunicacao': 'Comunicação institucional',
        'outra': 'Outro'
    },
    areas: {
        'atendimento': 'Atendimento',
        'secretaria': 'Secretaria',
        'servicos_academicos': 'Serviços académicos',
        'servicos_administrativos': 'Serviços administrativos',
        'servicos_financeiros': 'Serviços financeiros',
        'tic': 'TIC',
        'biblioteca': 'Biblioteca',
        'infraestrutura': 'Infra-estrutura',
        'outro': 'Outro'
    }
};

// ============================================
// ESTADO DA APLICAÇÃO
// ============================================
const state = {
    selectedFiles: [],
    formValid: false,
    modalAberto: false,
    tipoReclamacao: null // 'identificado' | 'anonimo' | null
};

// ============================================
// ELEMENTOS DO DOM
// ============================================
const elements = {
    // Modais
    modalInicial: document.getElementById('modal-inicial'),
    modalRevisao: document.getElementById('modal-revisao'),
    
    // Container principal
    pageHeader: document.getElementById('page-header'),
    form: document.getElementById('form-reclamacao'),
    
    // Campos do formulário
    assunto: document.getElementById('assunto'),
    categoria: document.getElementById('categoria'),
    outraCategoria: document.getElementById('outra_categoria'),
    outraCategoriaGroup: document.getElementById('outra-categoria-group'),
    areaServico: document.getElementById('area_servico'),
    local: document.getElementById('local'),
    dataOcorrencia: document.getElementById('data_ocorrencia'),
    descricao: document.getElementById('descricao'),
    impacto: document.getElementById('impacto'),
    pretensao: document.getElementById('pretensao'),
    charCounter: document.getElementById('char-counter'),
    
    // Secção de identificação (dinâmica)
    secaoIdentificacao: document.getElementById('secao-identificacao'),
    tipoIdentificacaoInput: document.getElementById('tipo_identificacao'),
    nome: document.getElementById('nome'),
    email: document.getElementById('email'),
    celular: document.getElementById('celular'),
    
    // Upload
    uploadArea: document.getElementById('upload-area'),
    fileInput: document.getElementById('file-input'),
    cameraInput: document.getElementById('camera-input'),
    fileList: document.getElementById('file-list'),
    
    // Checkboxes
    confidencialidade: document.getElementById('confidencialidade'),
    confirmacao: document.getElementById('confirmacao'),
    
    // Resumo no modal
    resumoAssunto: document.getElementById('resumo-assunto'),
    resumoCategoria: document.getElementById('resumo-categoria'),
    resumoArea: document.getElementById('resumo-area'),
    resumoLocal: document.getElementById('resumo-local'),
    resumoData: document.getElementById('resumo-data'),
    resumoContinua: document.getElementById('resumo-continua'),
    resumoTipo: document.getElementById('resumo-tipo'),
    resumoDescricao: document.getElementById('resumo-descricao'),
    resumoImpacto: document.getElementById('resumo-impacto'),
    resumoImpactoItem: document.getElementById('resumo-impacto-item'),
    resumoPretensao: document.getElementById('resumo-pretensao'),
    resumoPretensaoItem: document.getElementById('resumo-pretensao-item'),
    resumoAnexos: document.getElementById('resumo-anexos'),
    resumoAnexosItem: document.getElementById('resumo-anexos-item'),
    
    // Botões
    btnSubmeter: document.getElementById('btn-submeter')
};

// ============================================
// FLUXO INICIAL: Selecção do tipo de reclamação
// ============================================

function selecionarTipoReclamacao(tipo) {
    state.tipoReclamacao = tipo;
    
    // Atualizar visual dos radio buttons do modal inicial
    const radioIdentificado = document.getElementById('radio-identificado');
    const radioAnonimo = document.getElementById('radio-anonimo');
    
    if (tipo === 'identificado') {
        radioIdentificado.style.borderColor = 'var(--ur-primary)';
        radioIdentificado.style.backgroundColor = 'var(--ur-primary)';
        radioIdentificado.style.borderWidth = '6px';
        
        radioAnonimo.style.borderColor = 'var(--ur-gray-300)';
        radioAnonimo.style.backgroundColor = 'transparent';
        radioAnonimo.style.borderWidth = '2px';
    } else {
        radioAnonimo.style.borderColor = 'var(--ur-primary)';
        radioAnonimo.style.backgroundColor = 'var(--ur-primary)';
        radioAnonimo.style.borderWidth = '6px';
        
        radioIdentificado.style.borderColor = 'var(--ur-gray-300)';
        radioIdentificado.style.backgroundColor = 'transparent';
        radioIdentificado.style.borderWidth = '2px';
    }
    
    // Pequeno delay para o utilizador ver a selecção antes de fechar
    setTimeout(() => {
        iniciarFormulario(tipo);
    }, 300);
}

function iniciarFormulario(tipo) {
    // Fechar modal inicial
    elements.modalInicial.classList.remove('active');
    elements.modalInicial.style.display = 'none';
    
    // Mostrar cabeçalho e formulário
    elements.pageHeader.style.display = 'block';
    elements.form.style.display = 'block';
    
    // Configurar baseado no tipo
    elements.tipoIdentificacaoInput.value = tipo;
    
    if (tipo === 'identificado') {
        // Mostrar secção de identificação
        elements.secaoIdentificacao.style.display = 'block';
        elements.nome.required = true;
        elements.email.required = true;
        
        // Focar no primeiro campo do formulário
        setTimeout(() => elements.assunto.focus(), 100);
    } else {
        // Esconder secção de identificação
        elements.secaoIdentificacao.style.display = 'none';
        elements.nome.required = false;
        elements.email.required = false;
        
        // Limpar valores
        elements.nome.value = '';
        elements.email.value = '';
        elements.celular.value = '';
        
        // Focar no primeiro campo
        setTimeout(() => elements.assunto.focus(), 100);
    }
    
    // Scroll para o topo
    window.scrollTo({ top: 0, behavior: 'smooth' });
}

// ============================================
// UTILITÁRIOS
// ============================================

function formatFileSize(bytes) {
    if (bytes === 0) return '0 Bytes';
    const k = 1024;
    const sizes = ['Bytes', 'KB', 'MB', 'GB'];
    const i = Math.floor(Math.log(bytes) / Math.log(k));
    return parseFloat((bytes / Math.pow(k, i)).toFixed(2)) + ' ' + sizes[i];
}

function getFileIcon(type) {
    const icons = {
        pdf: '<svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z"></path><polyline points="14 2 14 8 20 8"></polyline></svg>',
        image: '<svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><rect x="3" y="3" width="18" height="18" rx="2" ry="2"></rect><circle cx="8.5" cy="8.5" r="1.5"></circle><polyline points="21 15 16 10 5 21"></polyline></svg>',
        video: '<svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><rect x="2" y="2" width="20" height="20" rx="2.18" ry="2.18"></rect><line x1="7" y1="2" x2="7" y2="22"></line><line x1="17" y1="2" x2="17" y2="22"></line><line x1="2" y1="12" x2="22" y2="12"></line><line x1="2" y1="7" x2="7" y2="7"></line><line x1="2" y1="17" x2="7" y2="17"></line><line x1="17" y1="17" x2="22" y2="17"></line><line x1="17" y1="7" x2="22" y2="7"></line></svg>',
        audio: '<svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M9 18V5l12-2v13"></path><circle cx="6" cy="18" r="3"></circle><circle cx="18" cy="16" r="3"></circle></svg>',
        default: '<svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z"></path><polyline points="14 2 14 8 20 8"></polyline><line x1="16" y1="13" x2="8" y2="13"></line><line x1="16" y1="17" x2="8" y2="17"></line><polyline points="10 9 9 9 8 9"></polyline></svg>'
    };
    
    if (type.includes('pdf')) return icons.pdf;
    if (type.includes('image')) return icons.image;
    if (type.includes('video')) return icons.video;
    if (type.includes('audio')) return icons.audio;
    return icons.default;
}

function showError(inputId, show = true) {
    const input = document.getElementById(inputId);
    const error = document.getElementById('error-' + inputId);
    
    if (!input || !error) return;
    
    if (show) {
        input.classList.add('error');
        error.classList.add('visible');
        error.style.display = 'flex';
    } else {
        input.classList.remove('error');
        error.classList.remove('visible');
        error.style.display = 'none';
    }
}

function showCheckboxError(checkboxId, show = true) {
    const checkbox = document.getElementById(checkboxId);
    const error = document.getElementById('error-' + checkboxId);
    const group = checkbox?.closest('.ur-checkbox-group');
    
    if (!checkbox || !error) return;
    
    if (show) {
        group?.classList.add('has-error');
        error.classList.add('visible');
        error.style.display = 'flex';
    } else {
        group?.classList.remove('has-error');
        error.classList.remove('visible');
        error.style.display = 'none';
    }
}

// ============================================
// GESTÃO DE FICHEIROS (Múltiplos anexos)
// ============================================

function handleFiles(files) {
    for (const file of Array.from(files || [])) {
        const extension = file.name.split('.').pop().toLowerCase();
        if (!CONFIG.allowedTypes.includes(file.type.toLowerCase()) && !CONFIG.allowedExtensions.includes(extension)) {
            alert(`O ficheiro "${file.name}" não é suportado.`);
            continue;
        }

        if (file.size > CONFIG.maxFileSize) {
            alert(`O ficheiro "${file.name}" excede o limite de 20 MB.`);
            continue;
        }

        if (state.selectedFiles.length >= CONFIG.maxFiles) {
            alert('Pode anexar no máximo 7 ficheiros.');
            break;
        }

        state.selectedFiles.push(file);
    }

    if (elements.fileInput) elements.fileInput.value = '';
    if (elements.cameraInput) elements.cameraInput.value = '';
    renderFileList();
}

function renderFileList() {
    if (!elements.fileList) return;
    
    elements.fileList.innerHTML = '';
    
    if (state.selectedFiles.length === 0) {
        elements.fileList.style.display = 'none';
        return;
    }
    
    elements.fileList.style.display = 'block';
    
    state.selectedFiles.forEach((file, index) => {
        const fileItem = document.createElement('div');
        fileItem.className = 'ur-file-item';
        
        fileItem.innerHTML = `
            <div class="ur-file-icon">${getFileIcon(file.type)}</div>
            <div class="ur-file-info">
                <div class="ur-file-name"></div>
                <div class="ur-file-meta">${formatFileSize(file.size)}</div>
            </div>
            <button type="button" class="ur-file-remove" data-index="${index}" aria-label="Remover ficheiro">
                <svg xmlns="http://www.w3.org/2000/svg" width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                    <line x1="18" y1="6" x2="6" y2="18"></line>
                    <line x1="6" y1="6" x2="18" y2="18"></line>
                </svg>
            </button>
        `;
        fileItem.querySelector('.ur-file-name').textContent = file.name;
        
        elements.fileList.appendChild(fileItem);
    });
    
    // Adicionar event listeners aos botões de remover
    elements.fileList.querySelectorAll('.ur-file-remove').forEach(btn => {
        btn.addEventListener('click', function() {
            const index = parseInt(this.dataset.index);
            removeFile(index);
        });
    });
}

function removeFile(index) {
    state.selectedFiles.splice(index, 1);
    renderFileList();
}

// ============================================
// CAMPOS CONDICIONAIS
// ============================================

function toggleOutraCategoria() {
    const isOutra = elements.categoria.value === 'outra';
    elements.outraCategoriaGroup.style.display = isOutra ? 'block' : 'none';
    elements.outraCategoria.required = isOutra;
    if (!isOutra) {
        elements.outraCategoria.value = '';
        showError('outra_categoria', false);
    }
}

function updateCharCounter() {
    const length = elements.descricao.value.length;
    elements.charCounter.textContent = `${length} / ${CONFIG.maxChars} caracteres`;
    elements.charCounter.classList.toggle('warning', length > CONFIG.maxChars - 500);
}

// ============================================
// VALIDAÇÃO DO FORMULÁRIO
// ============================================

function validarFormulario() {
    let valido = true;
    
    // Campos obrigatórios textuais
    const camposObrigatorios = ['assunto', 'categoria', 'area_servico', 'descricao'];
    
    camposObrigatorios.forEach(campo => {
        const input = document.getElementById(campo);
        if (!input?.value.trim()) {
            showError(campo, true);
            valido = false;
        } else {
            showError(campo, false);
        }
    });
    
    // Validar "Outra categoria"
    if (elements.categoria.value === 'outra' && !elements.outraCategoria.value.trim()) {
        showError('outra_categoria', true);
        valido = false;
    }
    
    // Se for identificado, validar campos de identificação
    if (state.tipoReclamacao === 'identificado') {
        if (!elements.nome.value.trim()) {
            showError('nome', true);
            valido = false;
        } else {
            showError('nome', false);
        }
        
        const emailRegex = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
        if (!elements.email.value.trim() || !emailRegex.test(elements.email.value)) {
            showError('email', true);
            valido = false;
        } else {
            showError('email', false);
        }
    }
    
    // Validar checkboxes
    if (!elements.confidencialidade?.checked) {
        showCheckboxError('confidencialidade', true);
        valido = false;
    } else {
        showCheckboxError('confidencialidade', false);
    }
    
    if (!elements.confirmacao?.checked) {
        showCheckboxError('confirmacao', true);
        valido = false;
    } else {
        showCheckboxError('confirmacao', false);
    }
    
    // Scroll para o primeiro erro se houver
    if (!valido) {
        const primeiroErro = document.querySelector('.error, .has-error, .ur-error-message.visible');
        if (primeiroErro) {
            primeiroErro.scrollIntoView({ behavior: 'smooth', block: 'center' });
        }
    }
    
    return valido;
}

// ============================================
// MODAL DE REVISÃO
// ============================================

function preencherResumo() {
    // Assunto
    elements.resumoAssunto.textContent = elements.assunto?.value || '-';
    
    // Categoria
    const categoriaText = elements.categoria?.value ? CONFIG.categorias[elements.categoria.value] || elements.categoria.value : '-';
    elements.resumoCategoria.textContent = categoriaText;
    
    // Área ou serviço
    const areaText = elements.areaServico?.value ? CONFIG.areas[elements.areaServico.value] || elements.areaServico.value : '-';
    elements.resumoArea.textContent = areaText;
    
    // Local
    elements.resumoLocal.textContent = elements.local?.value || '-';
    
    // Data
    elements.resumoData.textContent = elements.dataOcorrencia?.value ? new Date(elements.dataOcorrencia.value).toLocaleDateString('pt-PT') : '-';
    
    // Continua
    const continuaValor = document.querySelector('input[name="continua"]:checked')?.value;
    const continuaTexto = {
        'sim': 'Sim',
        'nao': 'Não',
        'nao_sei': 'Não sei'
    };
    elements.resumoContinua.textContent = continuaTexto[continuaValor] || '-';
    
    // Tipo de reclamação
    elements.resumoTipo.textContent = state.tipoReclamacao === 'identificado' 
        ? 'Identificada (com dados de contacto)' 
        : 'Anónima';
    
    // Descrição (truncada se muito longa)
    const descricao = elements.descricao?.value || '-';
    elements.resumoDescricao.textContent = descricao.length > 200 ? descricao.substring(0, 200) + '...' : descricao;
    
    // Impacto
    if (elements.impacto?.value.trim()) {
        elements.resumoImpacto.textContent = elements.impacto.value.length > 200 
            ? elements.impacto.value.substring(0, 200) + '...' 
            : elements.impacto.value;
        elements.resumoImpactoItem.style.display = 'flex';
    } else {
        elements.resumoImpactoItem.style.display = 'none';
    }
    
    // Pretensão
    if (elements.pretensao?.value.trim()) {
        elements.resumoPretensao.textContent = elements.pretensao.value.length > 200 
            ? elements.pretensao.value.substring(0, 200) + '...' 
            : elements.pretensao.value;
        elements.resumoPretensaoItem.style.display = 'flex';
    } else {
        elements.resumoPretensaoItem.style.display = 'none';
    }
    
    // Anexos
    if (state.selectedFiles.length > 0) {
        elements.resumoAnexos.textContent = `${state.selectedFiles.length} ficheiro(s) anexado(s)`;
        elements.resumoAnexosItem.style.display = 'flex';
    } else {
        elements.resumoAnexosItem.style.display = 'none';
    }
}

function abrirModalRevisao() {
    if (!validarFormulario()) {
        return;
    }
    
    preencherResumo();
    
    elements.modalRevisao.classList.add('active');
    elements.modalRevisao.setAttribute('aria-hidden', 'false');
    document.body.style.overflow = 'hidden';
    state.modalAberto = true;
    
    // Focar no primeiro elemento interativo do modal
    setTimeout(() => {
        elements.modalRevisao.querySelector('button')?.focus();
    }, 100);
}

function fecharModalRevisao() {
    elements.modalRevisao.classList.remove('active');
    elements.modalRevisao.setAttribute('aria-hidden', 'true');
    document.body.style.overflow = '';
    state.modalAberto = false;
}

// ============================================
// EVENT LISTENERS
// ============================================

function initEventListeners() {
    // Categoria
    elements.categoria?.addEventListener('change', toggleOutraCategoria);
    
    // Contador de caracteres
    elements.descricao?.addEventListener('input', updateCharCounter);
    
    // Upload - Drag and Drop
    elements.uploadArea?.addEventListener('dragover', (e) => {
        e.preventDefault();
        elements.uploadArea.classList.add('dragover');
    });
    
    elements.uploadArea?.addEventListener('dragleave', () => {
        elements.uploadArea.classList.remove('dragover');
    });
    
    elements.uploadArea?.addEventListener('drop', (e) => {
        e.preventDefault();
        elements.uploadArea.classList.remove('dragover');
        handleFiles(e.dataTransfer.files);
    });
    
    elements.fileInput?.addEventListener('change', (e) => {
        handleFiles(e.target.files);
    });

    elements.cameraInput?.addEventListener('change', (e) => {
        handleFiles(e.target.files);
    });

    elements.form?.addEventListener('formdata', (e) => {
        e.formData.delete('evidencias[]');
        state.selectedFiles.forEach(file => e.formData.append('evidencias[]', file, file.name));
    });
    
    // Limpar erros ao digitar
    document.querySelectorAll('.ur-form-input, .ur-form-select, .ur-form-textarea').forEach(input => {
        input.addEventListener('input', function() {
            this.classList.remove('error');
            const error = document.getElementById('error-' + this.id);
            if (error) {
                error.classList.remove('visible');
                error.style.display = 'none';
            }
        });
    });
    
    // Limpar erros de checkbox ao alterar
    document.querySelectorAll('.ur-checkbox-input').forEach(checkbox => {
        checkbox.addEventListener('change', function() {
            this.closest('.ur-checkbox-group')?.classList.remove('has-error');
            const error = document.getElementById('error-' + this.id);
            if (error) {
                error.classList.remove('visible');
                error.style.display = 'none';
            }
        });
    });
    
    // Fechar modal ao clicar fora
    elements.modalRevisao?.addEventListener('click', (e) => {
        if (e.target === elements.modalRevisao) {
            fecharModalRevisao();
        }
    });
    
    // Fechar modal com ESC
    document.addEventListener('keydown', (e) => {
        if (e.key === 'Escape' && state.modalAberto) {
            fecharModalRevisao();
        }
    });
    
    // Interceptar submit do formulário
    elements.form?.addEventListener('submit', (e) => {
        if (!state.modalAberto) {
            e.preventDefault();
            abrirModalRevisao();
        }
    });
}

// ============================================
// FUNÇÕES GLOBAIS
// ============================================

window.selecionarTipoReclamacao = selecionarTipoReclamacao;
window.abrirModalRevisao = abrirModalRevisao;
window.fecharModalRevisao = fecharModalRevisao;

window.guardarRascunho = function() {
    const formData = new FormData(elements.form);
    const dados = {};
    formData.forEach((value, key) => {
        dados[key] = value;
    });
    dados.tipoReclamacao = state.tipoReclamacao;
    dados.anexos = state.selectedFiles.map(f => f.name);
    
    localStorage.setItem('rascunho_reclamacao', JSON.stringify(dados));
    alert('Rascunho guardado com sucesso!\n\nPode continuar mais tarde. Os dados ficarão guardados neste navegador.');
};

// ============================================
// INICIALIZAÇÃO
// ============================================

function init() {
    // Verificar se existe rascunho
    const rascunho = localStorage.getItem('rascunho_reclamacao');
    if (rascunho) {
        try {
            const dados = JSON.parse(rascunho);
            if (dados.tipoReclamacao && confirm('Existe um rascunho guardado. Deseja recuperá-lo?')) {
                // Restaurar tipo de reclamação
                state.tipoReclamacao = dados.tipoReclamacao;
                elements.tipoIdentificacaoInput.value = dados.tipoReclamacao;
                
                // Fechar modal inicial e mostrar formulário
                elements.modalInicial.classList.remove('active');
                elements.modalInicial.style.display = 'none';
                elements.pageHeader.style.display = 'block';
                elements.form.style.display = 'block';
                
                // Restaurar dados do formulário
                Object.keys(dados).forEach(key => {
                    const input = document.querySelector(`[name="${key}"]`);
                    if (input && key !== 'anexos' && key !== 'tipoReclamacao') {
                        input.value = dados[key];
                    }
                });
                
                // Se for identificado, mostrar secção
                if (dados.tipoReclamacao === 'identificado') {
                    elements.secaoIdentificacao.style.display = 'block';
                    elements.nome.required = true;
                    elements.email.required = true;
                }
                
                updateCharCounter();
                toggleOutraCategoria();
            }
        } catch (e) {
            console.error('Erro ao carregar rascunho:', e);
        }
    }
    
    initEventListeners();
    console.log('Portal de Manifestações - Formulário de Reclamação inicializado');
}

// Iniciar quando DOM estiver pronto
if (document.readyState === 'loading') {
    document.addEventListener('DOMContentLoaded', init);
} else {
    init();
}