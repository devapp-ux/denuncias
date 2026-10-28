<!DOCTYPE html>
<html lang="pt-PT">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Carta de {{ ucfirst($manifestacao->tipo) }} — {{ $manifestacao->codigo }}</title>
    <style>
        @page { size: A4; margin: 22mm 18mm; }
        body { font-family: 'DejaVu Sans', sans-serif; font-size: 11px; color: #1f2937; line-height: 1.5; }
        body { max-width: 800px; margin: 2rem auto; padding: 0 1.5rem; background: #fff; }

        header { position: static; height: auto; padding-bottom: 12px; border-bottom: 2px solid #1e40af; }
        header .logo { float: left; width: 46px; height: 46px; }
        header .titulo { float: left; margin-left: 12px; padding-top: 4px; }
        header .inst { font-size: 15px; font-weight: bold; color: #1e3a8a; }
        header .sub { font-size: 10px; color: #6b7280; }

        footer { position: static; height: auto; border-top: 1px solid #e5e7eb; padding-top: 8px; font-size: 9px; color: #6b7280; }
        footer .esq { float: left; }
        footer .dir { float: right; }

        .clear { clear: both; }

        h1 { font-size: 16px; color: #1e3a8a; margin: 0 0 4px 0; text-align: center; text-transform: uppercase; letter-spacing: 1px; }
        .subtitulo { text-align: center; font-size: 10px; color: #6b7280; margin-bottom: 18px; }

        .caixa { border: 1px solid #d1d5db; border-radius: 6px; padding: 10px 12px; margin-bottom: 14px; background: #f9fafb; }
        .caixa .linha { margin-bottom: 4px; }
        .caixa .rot { font-weight: bold; color: #374151; }

        .seccao { margin-bottom: 14px; }
        .seccao h2 { font-size: 12px; color: #1e40af; border-bottom: 1px solid #e5e7eb; padding-bottom: 3px; margin: 0 0 6px 0; }
        .seccao p { margin: 0; text-align: justify; }

        table.dados { width: 100%; border-collapse: collapse; }
        table.dados td { padding: 4px 6px; vertical-align: top; border-bottom: 1px solid #f3f4f6; }
        table.dados td.rot { width: 38%; font-weight: bold; color: #374151; }

        .codigo { text-align: center; font-size: 14px; font-weight: bold; letter-spacing: 2px; color: #1e3a8a; border: 1px dashed #1e40af; padding: 8px; border-radius: 6px; margin: 16px 0; }
        .rodape-nota { font-size: 9px; color: #6b7280; margin-top: 20px; text-align: center; font-style: italic; }
        .barra-acoes { display: flex; justify-content: center; gap: 1rem; margin: 0 auto 1.5rem; }
        .barra-acoes button, .barra-acoes a { border: 0; border-radius: 0.5rem; padding: 0.75rem 1rem; background: #1e40af; color: white; font: inherit; text-decoration: none; cursor: pointer; }
        .barra-acoes a { background: #4b5563; }

        @media screen and (max-width: 600px) {
            body { margin: 1rem auto; padding: 0 0.75rem; font-size: 14px; }
            header .logo { width: 40px; height: 40px; }
            header .titulo { margin-left: 0.75rem; max-width: calc(100% - 52px); }
            header .inst { font-size: 13px; }
            header .sub { font-size: 9px; }
            h1 { font-size: 1.25rem; }
            .barra-acoes { flex-direction: column; gap: 0.5rem; padding: 0 0.25rem; }
            .barra-acoes button, .barra-acoes a { width: 100%; min-height: 46px; text-align: center; }
            .codigo { font-size: 0.875rem; letter-spacing: 0.04em; overflow-wrap: anywhere; }
            .caixa { padding: 0.75rem; }
            table.dados { table-layout: fixed; }
            table.dados td { padding: 0.4rem; overflow-wrap: anywhere; }
            table.dados td.rot { width: 36%; }
            footer .esq, footer .dir { float: none; text-align: center; }
            footer .dir { margin-top: 0.25rem; }
        }

        @media print {
            body { max-width: none; margin: 0; padding: 0; }
            .barra-acoes { display: none; }
            .instrucao-pdf { display: none; }
        }
    </style>
</head>
<body>

@unless ($pdfDownload)
<nav class="barra-acoes" aria-label="Ações do comprovativo">
    <a href="{{ route('manifestacao.carta.pdf') }}" download>Baixar comprovativo em PDF</a>
    <button type="button" data-copy="{{ $manifestacao->codigo }}">Copiar código</button>
    <button type="button" data-copy="{{ $senhaAcompanhamento }}">Copiar senha</button>
    <button type="button"
        data-copy-credentials
        data-code="{{ $manifestacao->codigo }}"
        data-password="{{ $senhaAcompanhamento }}">Copiar código e senha</button>
    <a href="{{ route('acompanhamento.index') }}" data-acompanhamento-link>Acompanhar manifestação</a>
    <a href="{{ url('/') }}">Voltar ao início</a>
</nav>
<p id="estado-copia" role="status" aria-live="polite" style="text-align:center;margin:0 0 1rem;color:#166534;"></p>
@endunless

@if (session('sucesso'))
    <p role="status" style="text-align: center; color: #166534;">{{ session('sucesso') }} Guarde este comprovativo: o código de acompanhamento aparece apenas nele.</p>
@endif

<header>
    <img src="{{ $pdfDownload ? $logoPdf : asset('img/ur_con.jpeg') }}" class="logo" alt="">
    <div class="titulo">
        <div class="inst">Universidade Rovuma</div>
        <div class="sub">Portal de Manifestações — Comprovativo Oficial</div>
    </div>
    <div class="clear"></div>
</header>

<footer>
    <div class="esq">Documento nº {{ 'DOC-' . $manifestacao->codigo }}</div>
    <div class="dir">Gerado em {{ $geradoEm->format('d/m/Y H:i') }}</div>
    <div class="clear"></div>
</footer>

<h1>Carta de {{ ucfirst($manifestacao->tipo) }}</h1>
<div class="subtitulo">Comprovativo de submissão de manifestação</div>

<div class="codigo">CÓDIGO DE ACOMPANHAMENTO: {{ $manifestacao->codigo }}</div>
<div class="codigo">SENHA DE ACOMPANHAMENTO: {{ $senhaAcompanhamento }}</div>

<div class="caixa">
    <div class="linha"><span class="rot">Tipo de manifestação:</span> {{ ucfirst($manifestacao->tipo) }}</div>
    <div class="linha"><span class="rot">Data de submissão:</span> {{ $manifestacao->created_at->format('d/m/Y H:i') }}</div>
    <div class="linha"><span class="rot">Estado inicial:</span> {{ ucfirst(str_replace('_', ' ', $manifestacao->estado)) }}</div>
    <div class="linha"><span class="rot">Encaminhado para:</span> {{ $provedor->nome ?? 'A definir' }}</div>
</div>

<div class="seccao">
    <h2>Dados do manifestante</h2>
    @if ($manifestacao->anonimo)
        <p>Manifestação submetida em regime <strong>anónimo</strong>.</p>
    @else
        <table class="dados">
            <tr><td class="rot">Nome</td><td>{{ $manifestacao->nome }}</td></tr>
            <tr><td class="rot">E-mail</td><td>{{ $manifestacao->email }}</td></tr>
            @if ($manifestacao->celular)
                <tr><td class="rot">Contacto</td><td>{{ $manifestacao->celular }}</td></tr>
            @endif
        </table>
    @endif
</div>

@if ($manifestacao->tipo === 'denuncia' && $detalhe)
    <div class="seccao">
        <h2>Detalhes da denúncia</h2>
        <table class="dados">
            <tr><td class="rot">Assunto</td><td>{{ $detalhe->assunto }}</td></tr>
            <tr><td class="rot">Categoria</td><td>{{ $detalhe->categoria }}{{ $detalhe->outra_categoria ? ' — ' . $detalhe->outra_categoria : '' }}</td></tr>
            <tr><td class="rot">Unidade orgânica</td><td>{{ $detalhe->unidade_organica }}</td></tr>
            @if ($detalhe->local)<tr><td class="rot">Local</td><td>{{ $detalhe->local }}</td></tr>@endif
            @if ($detalhe->data_ocorrencia)<tr><td class="rot">Data da ocorrência</td><td>{{ $detalhe->data_ocorrencia->format('d/m/Y') }}</td></tr>@endif
            @if ($detalhe->continua)<tr><td class="rot">Situação continua</td><td>{{ ucfirst(str_replace('_', ' ', $detalhe->continua)) }}</td></tr>@endif
            @if ($detalhe->pessoas_envolvidas)<tr><td class="rot">Pessoas envolvidas</td><td>{{ ucfirst($detalhe->pessoas_envolvidas) }}</td></tr>@endif
            @if ($detalhe->testemunhas)<tr><td class="rot">Testemunhas</td><td>{{ ucfirst($detalhe->testemunhas) }}</td></tr>@endif
        </table>
    </div>
@endif

@if ($manifestacao->tipo === 'reclamacao' && $detalhe)
    <div class="seccao">
        <h2>Detalhes da reclamação</h2>
        <table class="dados">
            <tr><td class="rot">Assunto</td><td>{{ $detalhe->assunto }}</td></tr>
            <tr><td class="rot">Categoria</td><td>{{ $detalhe->categoria }}{{ $detalhe->outra_categoria ? ' — ' . $detalhe->outra_categoria : '' }}</td></tr>
            <tr><td class="rot">Área/serviço</td><td>{{ $detalhe->area_servico }}</td></tr>
            @if ($detalhe->local)<tr><td class="rot">Local</td><td>{{ $detalhe->local }}</td></tr>@endif
            @if ($detalhe->data_ocorrencia)<tr><td class="rot">Data da ocorrência</td><td>{{ $detalhe->data_ocorrencia->format('d/m/Y') }}</td></tr>@endif
            @if ($detalhe->continua)<tr><td class="rot">Situação continua</td><td>{{ ucfirst(str_replace('_', ' ', $detalhe->continua)) }}</td></tr>@endif
        </table>
    </div>
@endif

@if ($manifestacao->tipo === 'sugestao' && $detalhe)
    <div class="seccao">
        <h2>Detalhes da sugestão</h2>
        <table class="dados">
            <tr><td class="rot">Título</td><td>{{ $detalhe->titulo }}</td></tr>
            <tr><td class="rot">Área relacionada</td><td>{{ $detalhe->area_relacionada }}{{ $detalhe->outra_area ? ' — ' . $detalhe->outra_area : '' }}</td></tr>
        </table>
    </div>
@endif

@if ($detalhe)
    <div class="seccao">
        <h2>Descrição</h2>
        <p>{{ $detalhe->descricao ?? $detalhe->sugestao }}</p>
    </div>

    @if (!empty($detalhe->impacto))
        <div class="seccao">
            <h2>Impacto</h2>
            <p>{{ $detalhe->impacto }}</p>
        </div>
    @endif

    @if (!empty($detalhe->pretensao))
        <div class="seccao">
            <h2>Pretensão</h2>
            <p>{{ $detalhe->pretensao }}</p>
        </div>
    @endif

    @if (!empty($detalhe->justificacao))
        <div class="seccao">
            <h2>Justificação</h2>
            <p>{{ $detalhe->justificacao }}</p>
        </div>
    @endif
@endif

@if ($manifestacao->evidencias->count())
    <div class="seccao">
        <h2>Documentos anexados ({{ $manifestacao->evidencias->count() }})</h2>
        <table class="dados">
            @foreach ($manifestacao->evidencias as $e)
                <tr><td>{{ $e->nome_original }}</td><td>{{ $e->tamanho ? round($e->tamanho / 1024) . ' KB' : '' }}</td></tr>
            @endforeach
        </table>
    </div>
@endif

<div class="rodape-nota">
    Este documento é gerado automaticamente pelo Portal de Manifestações da Universidade Rovuma.<br>
    Guarde o código e a senha de acompanhamento para consultar o estado da sua manifestação. A senha não pode ser recuperada.
</div>

@unless ($pdfDownload)
<script>
    document.querySelectorAll('[data-copy]').forEach((button) => {
        button.addEventListener('click', async () => {
            const status = document.getElementById('estado-copia');
            try {
                await navigator.clipboard.writeText(button.dataset.copy);
                status.textContent = 'Copiado. Pode colar os dados no acompanhamento.';
            } catch {
                const campo = document.createElement('textarea');
                campo.value = button.dataset.copy;
                campo.style.position = 'fixed';
                campo.style.opacity = '0';
                document.body.appendChild(campo);
                campo.select();
                const copiado = document.execCommand('copy');
                campo.remove();
                status.textContent = copiado
                    ? 'Copiado. Pode colar os dados no acompanhamento.'
                    : 'Não foi possível copiar automaticamente. Selecione e copie o código e a senha.';
            }
        });
    });

    document.querySelector('[data-copy-credentials]')?.addEventListener('click', async (event) => {
        const button = event.currentTarget;
        const credentials = {
            codigo: button.dataset.code,
            senha: button.dataset.password
        };
        const texto = `Código: ${credentials.codigo}\nSenha: ${credentials.senha}`;
        const status = document.getElementById('estado-copia');

        try {
            sessionStorage.setItem('manifestacao_credenciais', JSON.stringify(credentials));
        } catch {
            status.textContent = 'A cópia automática pode não estar disponível neste navegador.';
        }

        try {
            await navigator.clipboard.writeText(texto);
            status.textContent = 'Código e senha copiados. Pode colá-los no acompanhamento.';
        } catch {
            const campo = document.createElement('textarea');
            campo.value = texto;
            campo.style.position = 'fixed';
            campo.style.opacity = '0';
            document.body.appendChild(campo);
            campo.select();
            const copiado = document.execCommand('copy');
            campo.remove();
            status.textContent = copiado
                ? 'Código e senha copiados. Pode colá-los no acompanhamento.'
                : 'Não foi possível copiar. Guarde o código e a senha apresentados no comprovativo.';
        }
    });

    document.querySelector('[data-acompanhamento-link]')?.addEventListener('click', () => {
        try {
            sessionStorage.setItem('manifestacao_credenciais', JSON.stringify({
                codigo: @json($manifestacao->codigo),
                senha: @json($senhaAcompanhamento)
            }));
        } catch {
            // A consulta continua disponível para introdução manual.
        }
    });
</script>
@endunless

</body>
</html>