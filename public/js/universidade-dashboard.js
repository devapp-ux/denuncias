(() => {
    const liveRegion = document.getElementById('dashboard-live');
    if (!liveRegion) return;

    const statusText = () => liveRegion.querySelector('[data-live-status]');
    const setStatus = (text) => {
        const status = statusText();
        if (status) status.textContent = text;
    };

    let busy = false;
    const sincronizar = async () => {
        if (busy || document.hidden) return;
        busy = true;
        try {
            const url = new URL(liveRegion.dataset.syncUrl, window.location.origin);
            new URLSearchParams(window.location.search).forEach((value, key) => url.searchParams.set(key, value));
            url.searchParams.set('versao', liveRegion.dataset.versao || '');
            const resposta = await fetch(url, {
                headers: { Accept: 'application/json' },
                credentials: 'same-origin'
            });

            if (resposta.status === 204) {
                setStatus('Atualizado automaticamente');
                return;
            }
            if (!resposta.ok) throw new Error(`HTTP ${resposta.status}`);

            const dados = await resposta.json();
            liveRegion.innerHTML = dados.html;
            liveRegion.dataset.versao = dados.versao;
        } catch (erro) {
            console.warn('Não foi possível sincronizar o painel.', erro);
            setStatus('Sem ligação às atualizações. A tentar novamente…');
        } finally {
            busy = false;
        }
    };

    sincronizar();
    setInterval(sincronizar, 5000);
    document.addEventListener('visibilitychange', () => {
        if (!document.hidden) sincronizar();
    });
})();
