<!DOCTYPE html>
<html lang="pt-PT">
<head>
    <meta charset="UTF-8">
    <title>Nova manifestação recebida</title>
</head>
<body>
    <h1>Nova manifestação recebida</h1>
    <p>Olá, {{ $dados['provedor'] }}.</p>
    <p>Foi registada uma nova manifestação dirigida ao seu serviço:</p>
    <ul>
        <li><strong>Referência:</strong> {{ $dados['codigo'] }}</li>
        <li><strong>Tipo:</strong> {{ ucfirst($dados['tipo']) }}</li>
        <li><strong>Data:</strong> {{ \Illuminate\Support\Carbon::parse($dados['submetida_em'])->format('d/m/Y H:i') }}</li>
    </ul>
    <p>Aceda ao sistema institucional para consultar e tratar a manifestação.</p>
</body>
</html>
