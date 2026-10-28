<?php

namespace App\Console\Commands;

use App\Models\OutboxEmail;
use App\Services\OutboxEmailProcessor;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Log;

class ProcessarOutboxEmails extends Command
{
    protected $signature = 'outbox:processar {--lote=50}';

    protected $description = 'Envia notificações de e-mail pendentes no outbox';

    public function handle(): int
    {
        $this->recuperarBloqueadas();

        $ids = OutboxEmail::query()
            ->whereIn('status', ['pendente', 'falhado'])
            ->where('tentativas', '<', OutboxEmailProcessor::MAX_TENTATIVAS)
            ->where(fn ($query) => $query
                ->whereNull('disponivel_em')
                ->orWhere('disponivel_em', '<=', now()))
            ->orderBy('id')
            ->limit(max(1, (int) $this->option('lote')))
            ->pluck('id');

        if ($ids->isNotEmpty() && in_array(config('mail.default'), ['log', 'array'], true)) {
            $erroTransporte = 'MAIL_MAILER está configurado para um transporte que não entrega e-mails.';
            OutboxEmail::query()->whereIn('id', $ids)->update(['ultimo_erro' => $erroTransporte]);

            $this->error('As notificações continuam pendentes: configure um transporte de e-mail real (por exemplo, SMTP) em MAIL_MAILER.');

            Log::error('Outbox não processado porque o transporte de e-mail não entrega mensagens.', [
                'mailer' => config('mail.default'),
                'outbox_email_ids' => $ids->all(),
                'error' => $erroTransporte,
            ]);

            return self::FAILURE;
        }

        $enviadas = 0;

        foreach ($ids as $id) {
            if (app(OutboxEmailProcessor::class)->processar((int) $id)) {
                $enviadas++;
            }
        }

        $this->info("Enviadas {$enviadas} de {$ids->count()} notificações selecionadas.");

        return self::SUCCESS;
    }

    private function recuperarBloqueadas(): void
    {
        $bloqueadas = OutboxEmail::query()
            ->where('status', 'processando')
            ->where('bloqueado_em', '<', now()->subMinutes(10));

        (clone $bloqueadas)
            ->where('tentativas', '<', OutboxEmailProcessor::MAX_TENTATIVAS)
            ->update([
                'status' => 'pendente',
                'bloqueado_em' => null,
                'disponivel_em' => now(),
            ]);

        (clone $bloqueadas)
            ->where('tentativas', '>=', OutboxEmailProcessor::MAX_TENTATIVAS)
            ->update([
                'status' => 'falhado',
                'bloqueado_em' => null,
            ]);
    }
}
