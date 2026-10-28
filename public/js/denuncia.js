/**
 * Portal de Manifestacoes - UniRovuma
 * JavaScript do Formulario de Denuncia
 * Versao 3.0 - Fluxo: Escolha inicial → Formulario → Confirmacao
 */

// ============================================
// CONFIGURACAO
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
};

// ============================================
// ESTADO DA APLICAÇÃO
// ============================================
const state = {
    selectedFiles: [],
    formValid: false,
    modalAberto: false,
    tipoDenuncia: null // 'identificado' | 'anonimo' | null
};

// ============================================
// ELEMENTOS DO DOM
// ============================================
const elements = {
    // Modais
    modalInicial: document.getElementById('modal-inicial'),
    modalFinalizacao: document.getElementById('modal-finalizacao'),
    
    // Container principal
    pageHeader: document.getElementById('page-header'),
    form: document.getElementById('form-denuncia'),
    
    // Campos do formulário
    assunto: document.getElementById('assunto'),
    categoria: document.getElementById('categoria'),
    outraCategoria: document.getElementById('outra_categoria'),
    outraCategoriaGroup: document.getElementById('outra-categoria-group'),
    descricao: document.getElementById('descricao'),
    charCounter: document.getElementById('char-counter'),
    
    // Secção de identificação (dinâmica)
    secaoIdentificacao: document.getElementById('secao-identificacao'),
    tipoIdentificacaoInput: document.getElementById('tipo_identificacao'),
    nome: document.getElementById('nome'),
    email: document.getElementById('email'),
    celular: document.getElementById('celular'),
    
    // Campos condicionais
    pessoasRadios: document.querySelectorAll('input[name="pessoas_envolvidas"]'),
    pessoasDetalhes: document.getElementById('pessoas-detalhes'),
    testemunhasRadios: document.querySelectorAll('input[name="testemunhas"]'),
    testemunhasDetalhes: document.getElementById('testemunhas-detalhes'),
    
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
    resumoProvedor: document.getElementById('resumo-provedor'),
    resumoTipo: document.getElementById('resumo-tipo'),
    
    // Botões
    btnSubmeter: document.getElementById('btn-submeter')
};

// ============================================
// FLUXO INICIAL: Selecção do tipo de denúncia
// ============================================

function selecionarTipoDenuncia(tipo) {
    state.tipoDenuncia = tipo;
    
    // Atualizar visual dos radio buttons do modal inicial
    document.getElementById('radio-identificado').style.borderColor = tipo === 'identificado' ? 'var(--ur-primary)' : 'var(--ur-gray-300)';
    document.getElementById('radio-identificado').style.backgroundColor = tipo === 'identificado' ? 'var(--ur-primary)' : 'transparent';
    document.getElementById('radio-identificado').style.borderWidth = tipo === 'identificado' ? '6px' : '2px';
    
    document.getElementById('radio-anonimo').style.borderColor = tipo === 'anonimo' ? 'var(--ur-primary)' : 'var(--ur-gray-300)';
    document.getElementById('radio-anonimo').style.backgroundColor = tipo === 'anonimo' ? 'var(--ur-primary)' : 'transparent';
    document.getElementById('radio-anonimo').style.borderWidth = tipo === 'anonimo' ? '6px' : '2px';
    
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
        default: '<svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z"></path><polyline points="14 2 14 8 20 8"></polyline><line x1="16" y1="13" x2="8" y2="13"></line><line x1="16" y1="17" x2="8" y2="17"></line><polyline points="10 9 9 9 8 9"></polyline></svg>'
    };
    
    if (type.includes('pdf')) return icons.pdf;
    if (type.includes('image')) return icons.image;
    if (type.includes('video')) return icons.video;
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

function togglePessoasDetalhes() {
    const mostrar = document.getElementById('pessoas_sim')?.checked;
    elements.pessoasDetalhes.classList.toggle('visible', mostrar);
}

function toggleTestemunhasDetalhes() {
    const mostrar = document.getElementById('testemunhas_sim')?.checked;
    elements.testemunhasDetalhes.classList.toggle('visible', mostrar);
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
    const camposObrigatorios = ['assunto', 'categoria', 'descricao'];
    
    camposObrigatorios.forEach(campo => {
        const input = document.getElementById(campo);
        if (!input?.value.trim()) {
            showError(campo, true);
            valido = false;
        } else {
            showError(campo, false);
        }
    });
    
    // Validar provedor
    const provedorSelecionado = document.querySelector('input[name="provedor_id"]:checked');
    if (!provedorSelecionado) {
        const errorProvedor = document.getElementById('error-provedor_id');
        if (errorProvedor) {
            errorProvedor.style.display = 'flex';
            errorProvedor.classList.add('visible');
        }
        valido = false;
    } else {
        const errorProvedor = document.getElementById('error-provedor_id');
        if (errorProvedor) {
            errorProvedor.style.display = 'none';
            errorProvedor.classList.remove('visible');
        }
    }
    
    // Validar "Outra categoria"
    if (elements.categoria.value === 'outra' && !elements.outraCategoria.value.trim()) {
        showError('outra_categoria', true);
        valido = false;
    }
    
    // Se for identificado, validar campos de identificação
    if (state.tipoDenuncia === 'identificado') {
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
// MODAL DE FINALIZAÇÃO
// ============================================

function preencherResumo() {
    // Assunto
    elements.resumoAssunto.textContent = elements.assunto?.value || '-';
    
    // Categoria
    const categoriaText = elements.categoria?.options[elements.categoria.selectedIndex]?.text || '-';
    elements.resumoCategoria.textContent = categoriaText;
    
    // Provedor
    const provedorSelecionado = document.querySelector('input[name="provedor_id"]:checked');
    elements.resumoProvedor.textContent = provedorSelecionado 
        ? provedorSelecionado.closest('.ur-provedor-card').querySelector('.ur-provedor-title').textContent.trim()
        : '-';
    
    // Tipo de denúncia
    elements.resumoTipo.textContent = state.tipoDenuncia === 'identificado' 
        ? 'Identificada (com dados de contacto)' 
        : 'Anónima';
}

function abrirModalFinalizacao() {
    if (!validarFormulario()) {
        return;
    }
    
    preencherResumo();
    
    elements.modalFinalizacao.classList.add('active');
    elements.modalFinalizacao.setAttribute('aria-hidden', 'false');
    document.body.style.overflow = 'hidden';
    state.modalAberto = true;
    
    // Focar no primeiro elemento interativo do modal
    setTimeout(() => {
        elements.modalFinalizacao.querySelector('button')?.focus();
    }, 100);
}

function fecharModalFinalizacao() {
    elements.modalFinalizacao.classList.remove('active');
    elements.modalFinalizacao.setAttribute('aria-hidden', 'true');
    document.body.style.overflow = '';
    state.modalAberto = false;
}

// ============================================
// EVENT LISTENERS
// ============================================

function initEventListeners() {
    // Categoria
    elements.categoria?.addEventListener('change', toggleOutraCategoria);
    
    // Pessoas envolvidas
    elements.pessoasRadios.forEach(radio => {
        radio.addEventListener('change', togglePessoasDetalhes);
    });
    
    // Testemunhas
    elements.testemunhasRadios.forEach(radio => {
        radio.addEventListener('change', toggleTestemunhasDetalhes);
    });
    
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
    elements.modalFinalizacao?.addEventListener('click', (e) => {
        if (e.target === elements.modalFinalizacao) {
            fecharModalFinalizacao();
        }
    });
    
    // Fechar modal com ESC
    document.addEventListener('keydown', (e) => {
        if (e.key === 'Escape' && state.modalAberto) {
            fecharModalFinalizacao();
        }
    });
    
    // Interceptar submit do formulário
    elements.form?.addEventListener('submit', (e) => {
        e.preventDefault();
        
        if (state.modalAberto) {
            // Já está no modal de confirmação, pode submeter
            elements.form.submit();
        } else {
            abrirModalFinalizacao();
        }
    });
}

// ============================================
// FUNÇÕES GLOBAIS
// ============================================

window.selecionarTipoDenuncia = selecionarTipoDenuncia;
window.abrirModalFinalizacao = abrirModalFinalizacao;
window.fecharModalFinalizacao = fecharModalFinalizacao;

window.guardarRascunho = function() {
    const formData = new FormData(elements.form);
    const dados = {};
    formData.forEach((value, key) => {
        dados[key] = value;
    });
    dados.tipoDenuncia = state.tipoDenuncia;
    dados.anexos = state.selectedFiles.map(f => f.name);
    
    localStorage.setItem('rascunho_denuncia', JSON.stringify(dados));
    alert('Rascunho guardado com sucesso!\n\nPode continuar mais tarde. Os dados ficarão guardados neste navegador.');
};

// ============================================
// INICIALIZAÇÃO
// ============================================

function init() {
    // Verificar se existe rascunho
    const rascunho = localStorage.getItem('rascunho_denuncia');
    if (rascunho) {
        try {
            const dados = JSON.parse(rascunho);
            if (dados.tipoDenuncia && confirm('Existe um rascunho guardado. Deseja recuperá-lo?')) {
                // Restaurar tipo de denúncia
                state.tipoDenuncia = dados.tipoDenuncia;
                elements.tipoIdentificacaoInput.value = dados.tipoDenuncia;
                
                // Fechar modal inicial e mostrar formulário
                elements.modalInicial.classList.remove('active');
                elements.modalInicial.style.display = 'none';
                elements.pageHeader.style.display = 'block';
                elements.form.style.display = 'block';
                
                // Restaurar dados do formulário
                Object.keys(dados).forEach(key => {
                    const input = document.querySelector(`[name="${key}"]`);
                    if (input && key !== 'anexos' && key !== 'tipoDenuncia') {
                        input.value = dados[key];
                    }
                });
                
                // Se for identificado, mostrar secção
                if (dados.tipoDenuncia === 'identificado') {
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
    console.log('Portal de Manifestações - Formulário inicializado');
}

// Iniciar quando DOM estiver pronto
if (document.readyState === 'loading') {
    document.addEventListener('DOMContentLoaded', init);
} else {
    init();
}