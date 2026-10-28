/**
 * Portal de Manifestações - UniRovuma
 * JavaScript da Página de Acompanhamento
 * Versão 2.0 - Fluxo: Código → Loading → Timeline + Chat (API real)
 */

// ============================================
// ESTADO DA APLICAÇÃO
// ============================================
const state = {
    codigo: null,
    senha: null,
    carregando: false,
    mensagens: [],
    versao: null,
    ultimaMensagemId: 0,
    sincronizando: false,
    credenciaisParaColar: null
};

// ============================================
// ELEMENTOS DO DOM
// ============================================
const elements = {
    body: document.body,
    modalCodigo: document.getElementById('modal-codigo'),
    loadingOverlay: document.getElementById('loading-overlay'),
    conteudoPrincipal: document.getElementById('conteudo-principal'),
    inputCodigo: document.getElementById('codigo-manifestacao'),
    inputSenha: document.getElementById('senha-manifestacao'),
    btnColar: document.getElementById('colar-credenciais'),
    estadoColagem: document.getElementById('estado-colagem'),
    errorCodigo: document.getElementById('error-codigo'),
    timelineContainer: document.getElementById('timeline-container'),
    documentosContainer: document.getElementById('documentos-container'),
    infoCodigo: document.getElementById('info-codigo'),
    infoTipo: document.getElementById('info-tipo'),
    infoData: document.getElementById('info-data'),
    infoStatus: document.getElementById('info-status'),
    chatMessages: document.getElementById('chat-messages'),
    chatInput: document.getElementById('chat-input'),
    btnEnviar: document.getElementById('btn-enviar')
};

// ============================================
// HELPERS
// ============================================
function csrfToken() {
    const meta = document.querySelector('meta[name="csrf-token"]');
    return meta ? meta.content : '';
}

function escapeHtml(text) {
    const div = document.createElement('div');
    div.textContent = text;
    return div.innerHTML;
}

// ============================================
// FUNÇÕES PRINCIPAIS
// ============================================

/**
 * Inicia a consulta da manifestação
 */
function consultarManifestacao() {
    const codigo = elements.inputCodigo.value.trim().toUpperCase();
    const senha = elements.inputSenha.value.trim().toUpperCase();

    if (!codigo || !senha) {
        mostrarErroCodigo(true, 'Introduza o código e a senha indicados no comprovativo.');
        return;
    }

    // Validar formato mínimo (ex: MAN-2026D0001)
    if (codigo.length < 6) {
        alert('O código inserido parece estar incompleto. Verifique e tente novamente.');
        return;
    }

    mostrarErroCodigo(false);
    state.codigo = codigo;
    state.senha = senha;

    // Mostrar loading
    mostrarLoading(true);

    fetch(elements.modalCodigo.dataset.consultaUrl, {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
            'Accept': 'application/json',
            'X-CSRF-TOKEN': csrfToken()
        },
        body: JSON.stringify({ codigo, senha })
    })
        .then((response) => response.json().then((data) => ({ status: response.status, data })))
        .then(({ status, data }) => {
            mostrarLoading(false);

            if (status !== 200 || !data.ok) {
                state.codigo = null;
                state.senha = null;
                mostrarErroCodigo(true, data.mensagem || 'Código ou senha inválidos. Verifique os dados no comprovativo.');
                return;
            }

            state.codigo = data.manifestacao.codigo;
            state.versao = data.manifestacao.versao;
            state.ultimaMensagemId = Math.max(0, ...(data.mensagens || []).map((mensagem) => Number(mensagem.id) || 0));
            preencherManifestacao(data);
            abrirPaginaPrincipal();
            sincronizarPeriodicamente();
        })
        .catch(() => {
            mostrarLoading(false);
            state.codigo = null;
            state.senha = null;
            mostrarErroCodigo(true, 'Não foi possível contactar o servidor. Tente novamente.');
        });
}

/**
 * Preenche a página com os dados recebidos da API
 */
function preencherManifestacao(data) {
    elements.infoCodigo.textContent = data.manifestacao.codigo;
    elements.infoTipo.textContent = data.manifestacao.tipo;
    elements.infoData.textContent = data.manifestacao.data;

    elements.infoStatus.className = 'ur-status-badge ' + data.manifestacao.estado_badge;
    elements.infoStatus.innerHTML =
        '<span style="width:6px;height:6px;background-color:currentColor;border-radius:50%;"></span> ' +
        escapeHtml(data.manifestacao.estado_label);

    renderTimeline(data.timeline);
    renderMensagens(data.mensagens);
    renderEvidencias(data.evidencias);
}

/**
 * Renderiza a timeline
 */
function renderTimeline(passos) {
    elements.timelineContainer.innerHTML = '';

    (passos || []).forEach((passo) => {
        const classe = passo.estado === 'completed' ? 'completed'
            : passo.estado === 'active' ? 'active' : '';

        const item = document.createElement('div');
        item.className = ('ur-timeline-item ' + classe).trim();
        item.innerHTML =
            '<div class="ur-timeline-dot"></div>' +
            '<div class="ur-timeline-content">' +
                '<div class="ur-timeline-date">' + escapeHtml(passo.data) + '</div>' +
                '<div class="ur-timeline-title">' + escapeHtml(passo.titulo) + '</div>' +
                '<div class="ur-timeline-desc">' + escapeHtml(passo.desc) + '</div>' +
            '</div>';

        elements.timelineContainer.appendChild(item);
    });
}

/**
 * Renderiza as mensagens do chat
 */
function renderMensagens(mensagens) {
    elements.chatMessages.innerHTML = '';

    if (!mensagens || !mensagens.length) {
        elements.chatMessages.innerHTML =
            '<div class="ur-chat-message ur-chat-message--received">' +
                '<div>Olá! A sua manifestação foi recebida. Poderá utilizar este espaço para enviar esclarecimentos adicionais.</div>' +
            '</div>';
        return;
    }

    mensagens.forEach((m) => {
        const tipo = m.autor === 'utilizador' ? 'sent' : 'received';
        const div = document.createElement('div');
        div.className = 'ur-chat-message ur-chat-message--' + tipo;
        if (m.id) div.dataset.mensagemId = m.id;
        div.innerHTML =
            '<div>' + escapeHtml(m.mensagem) + '</div>' +
            '<div class="ur-chat-message-time">' + escapeHtml(m.data) + '</div>';
        elements.chatMessages.appendChild(div);
    });

    elements.chatMessages.scrollTop = elements.chatMessages.scrollHeight;
}

/**
 * Renderiza os documentos/evidências
 */
function renderEvidencias(evidencias) {
    if (!elements.documentosContainer) return;

    if (!evidencias || !evidencias.length) {
        elements.documentosContainer.innerHTML =
            '<p style="font-size:0.875rem;color:var(--ur-gray-500);">Sem documentos associados.</p>';
        return;
    }

    elements.documentosContainer.innerHTML = evidencias.map((e) =>
        '<a href="' + e.url + '" target="_blank" rel="noopener" ' +
        'style="display:flex;align-items:center;gap:0.75rem;padding:0.75rem;background-color:var(--ur-gray-50);border-radius:0.5rem;margin-bottom:0.5rem;text-decoration:none;">' +
            '<svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" style="color:var(--ur-primary);">' +
                '<path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z"></path>' +
                '<polyline points="14 2 14 8 20 8"></polyline>' +
            '</svg>' +
            '<div style="flex:1;">' +
                '<div style="font-size:0.875rem;font-weight:500;color:var(--ur-gray-800);">' + escapeHtml(e.nome) + '</div>' +
                '<div style="font-size:0.75rem;color:var(--ur-gray-500);">' + escapeHtml(e.tamanho) + '</div>' +
            '</div>' +
        '</a>'
    ).join('');
}

/**
 * Mostra/esconde o loading
 */
function mostrarLoading(mostrar) {
    if (mostrar) {
        elements.loadingOverlay.classList.add('active');
        state.carregando = true;
    } else {
        elements.loadingOverlay.classList.remove('active');
        state.carregando = false;
    }
}

/**
 * Abre a página principal após carregamento
 */
function abrirPaginaPrincipal() {
    elements.modalCodigo.classList.remove('active');
    elements.modalCodigo.style.display = 'none';

    elements.conteudoPrincipal.classList.remove('ur-body-blur');

    window.scrollTo({ top: 0, behavior: 'smooth' });
}

/**
 * Mostra/esconde erro do código
 */
function mostrarErroCodigo(mostrar, mensagem) {
    if (mostrar) {
        if (mensagem) {
            const texto = elements.errorCodigo.querySelector('.ur-error-texto');
            if (texto) texto.textContent = mensagem;
        }
        elements.inputCodigo.classList.add('error');
        elements.inputSenha.classList.add('error');
        elements.errorCodigo.classList.add('visible');
        elements.errorCodigo.style.display = 'flex';
    } else {
        elements.inputCodigo.classList.remove('error');
        elements.inputSenha.classList.remove('error');
        elements.errorCodigo.classList.remove('visible');
        elements.errorCodigo.style.display = 'none';
    }
}

async function colarCredenciais() {
    if (state.credenciaisParaColar) {
        preencherCredenciais(state.credenciaisParaColar);
        return;
    }

    const guardadas = lerCredenciaisGuardadas();
    if (guardadas) {
        preencherCredenciais(guardadas);
        return;
    }

    try {
        const texto = await navigator.clipboard.readText();
        const credenciais = extrairCredenciais(texto);
        if (!credenciais) {
            elements.estadoColagem.textContent = 'Não foram detetados um código e uma senha válidos.';
            return;
        }

        preencherCredenciais(credenciais);
    } catch {
        elements.estadoColagem.textContent = 'Cole o texto copiado num dos campos para detetar o código e a senha.';
        elements.inputCodigo.focus();
    }
}

function extrairCredenciais(texto) {
    if (!texto) return null;

    const codigo = texto.match(/MAN-\d{4}[DRS]\d{4}/i)?.[0];
    const senha = texto.match(/\b[2-9A-HJ-NP-Z]{10}\b/i)?.[0];

    return codigo && senha
        ? { codigo: codigo.toUpperCase(), senha: senha.toUpperCase() }
        : null;
}

function preencherCredenciais(credenciais) {
    elements.inputCodigo.value = credenciais.codigo;
    elements.inputSenha.value = credenciais.senha;
    elements.estadoColagem.textContent = 'Código e senha preenchidos. Toque em Consultar.';
    elements.btnColar.hidden = true;
    state.credenciaisParaColar = null;
    try {
        sessionStorage.removeItem('manifestacao_credenciais');
    } catch (erro) {
        console.warn('Não foi possível limpar as credenciais guardadas nesta sessão.', erro);
    }
    mostrarErroCodigo(false);
}

function lerCredenciaisGuardadas() {
    try {
        const credenciais = JSON.parse(sessionStorage.getItem('manifestacao_credenciais') || 'null');
        return credenciais?.codigo && credenciais?.senha
            ? extrairCredenciais(`${credenciais.codigo}\n${credenciais.senha}`)
            : null;
    } catch (erro) {
        console.warn('Não foi possível ler as credenciais desta sessão.', erro);
        return null;
    }
}

function prepararBotaoColar(credenciais) {
    if (!credenciais) return;

    state.credenciaisParaColar = credenciais;
    elements.btnColar.hidden = false;
    elements.estadoColagem.textContent = 'Código e senha detetados. Toque em «Colar».';
}

async function detetarCredenciaisNaAreaDeTransferencia() {
    if (!navigator.clipboard?.readText) return;

    try {
        prepararBotaoColar(extrairCredenciais(await navigator.clipboard.readText()));
    } catch {
        elements.estadoColagem.textContent = 'Cole o texto copiado num dos campos para detetar o código e a senha.';
    }
}

// ============================================
// CHAT / MENSAGENS
// ============================================

/**
 * Envia uma mensagem no chat (persiste no servidor)
 */
function enviarMensagem() {
    const texto = elements.chatInput.value.trim();

    if (!texto || !state.codigo) return;

    elements.btnEnviar.disabled = true;

    const urlMensagem = elements.modalCodigo.dataset.mensagemUrl
        .replace('__codigo__', encodeURIComponent(state.codigo));

    fetch(urlMensagem, {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
            'Accept': 'application/json',
            'X-CSRF-TOKEN': csrfToken()
        },
        body: JSON.stringify({ mensagem: texto, senha: state.senha })
    })
        .then((response) => response.json())
        .then((data) => {
            elements.btnEnviar.disabled = false;

            if (!data.ok) return;

            adicionarMensagem(data.mensagem.mensagem, 'sent', data.mensagem.data, data.mensagem.id);
            elements.chatInput.value = '';
        })
        .catch(() => {
            elements.btnEnviar.disabled = false;
            console.warn('Não foi possível enviar a mensagem ao acompanhamento.');
            const status = document.getElementById('acompanhamento-sync-status');
            if (status) status.textContent = 'A mensagem não foi enviada. Verifique a ligação e tente novamente.';
        });
}

/**
 * Adiciona uma mensagem ao chat
 */
function adicionarMensagem(texto, tipo, dataHora, id = null) {
    if (id && elements.chatMessages.querySelector(`[data-mensagem-id="${id}"]`)) return;
    if (!dataHora) {
        const agora = new Date();
        dataHora = agora.toLocaleDateString('pt-PT') + ' ' +
            agora.getHours().toString().padStart(2, '0') + ':' +
            agora.getMinutes().toString().padStart(2, '0');
    }

    const mensagemDiv = document.createElement('div');
    mensagemDiv.className = `ur-chat-message ur-chat-message--${tipo}`;
    if (id) mensagemDiv.dataset.mensagemId = id;
    mensagemDiv.innerHTML = `
        <div>${escapeHtml(texto)}</div>
        <div class="ur-chat-message-time">${escapeHtml(dataHora)}</div>
    `;

    elements.chatMessages.appendChild(mensagemDiv);

    elements.chatMessages.scrollTop = elements.chatMessages.scrollHeight;
}

async function sincronizarAcompanhamento() {
    if (!state.codigo || !state.senha || state.sincronizando || document.hidden) return;
    state.sincronizando = true;
    const status = document.getElementById('acompanhamento-sync-status');

    try {
        const resposta = await fetch(elements.modalCodigo.dataset.sincronizarUrl, {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'Accept': 'application/json',
                'X-CSRF-TOKEN': csrfToken()
            },
            body: JSON.stringify({
                codigo: state.codigo,
                senha: state.senha,
                versao: state.versao,
                mensagem_apos: state.ultimaMensagemId
            })
        });

        if (resposta.status === 204) {
            if (status) status.textContent = 'Atualizado automaticamente';
            return;
        }
        if (!resposta.ok) throw new Error(`HTTP ${resposta.status}`);

        const dados = await resposta.json();
        const manifestacao = dados.manifestacao;
        elements.infoStatus.className = 'ur-status-badge ' + manifestacao.estado_badge;
        elements.infoStatus.innerHTML =
            '<span style="width:6px;height:6px;background-color:currentColor;border-radius:50%;"></span> ' +
            escapeHtml(manifestacao.estado_label);
        renderTimeline(dados.timeline);

        (dados.mensagens || []).forEach((mensagem) => {
            adicionarMensagem(
                mensagem.mensagem,
                mensagem.autor === 'utilizador' ? 'sent' : 'received',
                mensagem.data,
                mensagem.id
            );
        });
        state.ultimaMensagemId = Math.max(state.ultimaMensagemId, Number(dados.ultima_mensagem_id) || 0);
        state.versao = manifestacao.versao;
        if (status) status.textContent = 'Atualizado automaticamente';
    } catch (erro) {
        console.warn('Não foi possível sincronizar o acompanhamento.', erro);
        if (status) status.textContent = 'Sem ligação às atualizações. A tentar novamente…';
    } finally {
        state.sincronizando = false;
    }
}

function sincronizarPeriodicamente() {
    if (state.intervaloSincronizacao) clearInterval(state.intervaloSincronizacao);
    state.intervaloSincronizacao = setInterval(sincronizarAcompanhamento, 5000);
    sincronizarAcompanhamento();
}

// ============================================
// EVENT LISTENERS
// ============================================

function initEventListeners() {
    elements.btnColar?.addEventListener('click', colarCredenciais);
    [elements.inputCodigo, elements.inputSenha].forEach((input) => {
        input?.addEventListener('paste', (event) => {
            const credenciais = extrairCredenciais(event.clipboardData?.getData('text') || '');
            if (!credenciais) return;

            event.preventDefault();
            prepararBotaoColar(credenciais);
        });
        input?.addEventListener('focus', detetarCredenciaisNaAreaDeTransferencia);
    });
    document.addEventListener('visibilitychange', () => {
        if (!document.hidden) sincronizarAcompanhamento();
    });

    // Enter no input de código
    elements.inputCodigo?.addEventListener('keypress', (e) => {
        if (e.key === 'Enter') {
            consultarManifestacao();
        }
    });
    elements.inputSenha?.addEventListener('keypress', (e) => {
        if (e.key === 'Enter') {
            consultarManifestacao();
        }
    });

    // Limpar erro ao digitar
    elements.inputCodigo?.addEventListener('input', () => {
        mostrarErroCodigo(false);
    });
    elements.inputSenha?.addEventListener('input', () => {
        mostrarErroCodigo(false);
    });

    // Enter no chat input
    elements.chatInput?.addEventListener('keypress', (e) => {
        if (e.key === 'Enter' && !e.shiftKey) {
            e.preventDefault();
            enviarMensagem();
        }
    });

    // Fechar modal com ESC (opcional - para acessibilidade)
    document.addEventListener('keydown', (e) => {
        if (e.key === 'Escape' && elements.modalCodigo.classList.contains('active')) {
            elements.inputCodigo.focus();
        }
    });
}

// ============================================
// INICIALIZAÇÃO
// ============================================

function init() {
    initEventListeners();
    prepararBotaoColar(lerCredenciaisGuardadas());
    if (elements.btnColar?.hidden) detetarCredenciaisNaAreaDeTransferencia();

    setTimeout(() => {
        elements.inputCodigo?.focus();
    }, 100);

}

// Iniciar quando DOM estiver pronto
if (document.readyState === 'loading') {
    document.addEventListener('DOMContentLoaded', init);
} else {
    init();
}

// ============================================
// FUNÇÕES GLOBAIS
// ============================================
window.consultarManifestacao = consultarManifestacao;
window.enviarMensagem = enviarMensagem;
window.colarCredenciais = colarCredenciais;