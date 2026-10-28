(() => {
    const chat = document.getElementById('manifestacao-chat');
    const form = document.getElementById('responder-manifestacao');
    const atualizarForm = document.getElementById('atualizar-manifestacao');
    const historico = document.getElementById('manifestacao-historico');
    if (!chat || !form || !atualizarForm || !historico) return;

    const csrf = document.querySelector('meta[name="csrf-token"]')?.content || '';
    const estados = {
        pendente: 'ur-status--pendente',
        encaminhada: 'ur-status--encaminhada',
        em_analise: 'ur-status--em_analise',
        respondida: 'ur-status--respondida',
        arquivada: 'ur-status--arquivada'
    };
    let busy = false;
    let enviando = false;

    const setStatus = (text) => {
        const status = chat.querySelector('[data-live-status]');
        if (status) status.textContent = text;
    };

    const criarMensagem = (mensagem) => {
        chat.querySelector('.ur-muted')?.remove();
        if (chat.querySelector(`[data-mensagem-id="${mensagem.id}"]`)) return;

        const article = document.createElement('article');
        article.className = `ur-chat-message ur-chat-message--${mensagem.autor}`;
        article.dataset.mensagemId = mensagem.id;
        article.append(document.createTextNode(mensagem.mensagem));
        const meta = document.createElement('small');
        meta.textContent = `${mensagem.autor === 'instituicao' ? 'Universidade' : 'Manifestante'} · ${mensagem.data}`;
        article.appendChild(meta);
        chat.appendChild(article);
        chat.scrollTop = chat.scrollHeight;
    };

    const criarEvento = (evento) => {
        if (historico.querySelector(`[data-evento-id="${evento.id}"]`)) return;
        historico.querySelector('.ur-muted')?.remove();

        const item = document.createElement('li');
        item.dataset.eventoId = evento.id;
        const title = document.createElement('strong');
        title.textContent = evento.estado;
        item.appendChild(title);

        if (evento.estado_anterior && evento.estado_anterior !== evento.estado) {
            const previous = document.createElement('span');
            previous.className = 'ur-muted';
            previous.textContent = ` · Estado anterior: ${evento.estado_anterior}`;
            item.appendChild(previous);
        }

        const details = document.createElement('div');
        details.className = 'ur-muted';
        details.textContent = `${evento.data} · ${evento.utilizador}${evento.provedor ? ` · ${evento.provedor}` : ''}`;
        item.appendChild(details);

        if (evento.observacao) {
            const note = document.createElement('p');
            note.textContent = evento.observacao;
            item.appendChild(note);
        }
        historico.appendChild(item);
    };

    const sincronizar = async () => {
        if (busy || document.hidden) return;
        busy = true;
        const url = new URL(chat.dataset.syncUrl, window.location.origin);
        url.searchParams.set('versao', chat.dataset.versao || '');
        url.searchParams.set('mensagem_apos', chat.dataset.mensagemApos || '0');
        url.searchParams.set('evento_apos', chat.dataset.eventoApos || '0');

        try {
            const resposta = await fetch(url, {
                headers: { Accept: 'application/json' },
                credentials: 'same-origin'
            });
            if (resposta.status === 404) {
                window.location.assign(chat.dataset.dashboardUrl);
                return;
            }
            if (resposta.status === 204) {
                setStatus('Atualizado automaticamente');
                return;
            }
            if (!resposta.ok) throw new Error(`HTTP ${resposta.status}`);

            const dados = await resposta.json();
            dados.mensagens.forEach(criarMensagem);
            dados.eventos.forEach(criarEvento);
            chat.dataset.versao = dados.versao;
            chat.dataset.mensagemApos = dados.ultima_mensagem_id;
            chat.dataset.eventoApos = dados.ultimo_evento_id;

            const estado = document.getElementById('manifestacao-estado');
            estado.className = `ur-status ${estados[dados.estado] || ''}`;
            estado.textContent = dados.estado_label;
            document.getElementById('manifestacao-provedor').textContent = dados.provedor;
            setStatus('Atualizado automaticamente');
        } catch (erro) {
            console.warn('Não foi possível sincronizar a manifestação.', erro);
            setStatus('Sem ligação às atualizações. A tentar novamente…');
        } finally {
            busy = false;
        }
    };

    form.addEventListener('submit', async (event) => {
        event.preventDefault();
        if (enviando) return;
        enviando = true;
        const button = form.querySelector('button[type="submit"]');
        const data = new FormData(form);
        if (button) button.disabled = true;
        try {
            const resposta = await fetch(form.action, {
                method: 'POST',
                headers: {
                    Accept: 'application/json',
                    'X-CSRF-TOKEN': csrf
                },
                body: data,
                credentials: 'same-origin'
            });
            if (!resposta.ok) throw new Error(`HTTP ${resposta.status}`);
            const resultado = await resposta.json();
            criarMensagem(resultado.mensagem);
            chat.dataset.mensagemApos = Math.max(
                Number(chat.dataset.mensagemApos || 0),
                Number(resultado.mensagem.id)
            );
            form.reset();
        } catch (erro) {
            console.error('Não foi possível enviar a mensagem.', erro);
            setStatus('A mensagem não foi enviada. Verifique a ligação e tente novamente.');
        } finally {
            enviando = false;
            if (button) button.disabled = false;
        }
    });

    atualizarForm.addEventListener('submit', async (event) => {
        event.preventDefault();
        if (enviando) return;
        enviando = true;
        const button = atualizarForm.querySelector('button[type="submit"]');
        if (button) button.disabled = true;
        try {
            const resposta = await fetch(atualizarForm.action, {
                method: 'POST',
                headers: {
                    Accept: 'application/json',
                    'X-CSRF-TOKEN': csrf,
                    'X-HTTP-Method-Override': 'PATCH'
                },
                body: new FormData(atualizarForm),
                credentials: 'same-origin'
            });
            if (!resposta.ok) throw new Error(`HTTP ${resposta.status}`);
            const dados = await resposta.json();
            chat.dataset.versao = dados.versao;
            const estado = document.getElementById('manifestacao-estado');
            estado.className = `ur-status ${estados[dados.estado] || ''}`;
            estado.textContent = dados.estado_label;
            document.getElementById('manifestacao-provedor').textContent = dados.provedor;
            document.getElementById('observacao').value = '';
            setStatus('Alterações guardadas. A timeline está atualizada.');
            await sincronizar();
        } catch (erro) {
            console.error('Não foi possível guardar as alterações da manifestação.', erro);
            setStatus('Não foi possível guardar as alterações. Verifique os campos e tente novamente.');
        } finally {
            enviando = false;
            if (button) button.disabled = false;
        }
    });

    sincronizar();
    setInterval(sincronizar, 5000);
    document.addEventListener('visibilitychange', () => {
        if (!document.hidden) sincronizar();
    });
})();
