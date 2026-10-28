<?php

namespace App\Services;

use App\Mail\NotificacaoManifestacaoMail;
use App\Models\OutboxEmail;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;
use Throwable;

class OutboxEmailProcessor
{
    public const MAX_TENTATIVAS = 5;

    public function processar(int $id): bool
    {
        if (in_array(config('mail.default'), ['log', 'array'], true)) {
            $email = OutboxEmail::findOrFail($id);
            $erro = 'MAIL_MAILER está configurado para um transporte que não entrega e-mails.';

            OutboxEmail::query()->whereKey($id)->update([
                'ultimo_erro' => $erro,
            ]);

            Log::error('Notificação de manifestação não enviada: transporte de e-mail não entrega mensagens.', [
                'mailer' => config('mail.default'),
                'outbox_email_id' => $email->id,
                'manifestacao_id' => $email->manifestacao_id,
                'error' => $erro,
            ]);

            return false;
        }

        $reclamado = OutboxEmail::query()
            ->whereKey($id)
            ->whereIn('status', ['pendente', 'falhado'])
            ->where('tentativas', '<', self::MAX_TENTATIVAS)
            ->where(fn ($query) => $query
                ->whereNull('disponivel_em')
                ->orWhere('disponivel_em', '<=', now()))
            ->update([
                'status' => 'processando',
                'tentativas' => DB::raw('tentativas + 1'),
                'bloqueado_em' => now(),
            ]);

        if (! $reclamado) {
            return false;
        }

        $email = OutboxEmail::findOrFail($id);
        $destinatario = trim((string) $email->destinatario)
            ?: $email->manifestacao()->with('provedor')->first()?->provedor?->email;
        $destinatario = trim((string) $destinatario);

        if (! $destinatario || ! filter_var($destinatario, FILTER_VALIDATE_EMAIL)) {
            $email->decrement('tentativas');
            $email->update([
                'status' => 'pendente',
                'bloqueado_em' => null,
                'disponivel_em' => now()->addMinutes(15),
                'ultimo_erro' => 'O provedor ainda não tem um endereço de e-mail configurado.',
            ]);

            Log::warning('Notificação de manifestação pendente: e-mail do provedor não configurado.', [
                'outbox_email_id' => $email->id,
                'manifestacao_id' => $email->manifestacao_id,
            ]);

            return false;
        }

        try {
            Mail::to($destinatario)
                ->send(new NotificacaoManifestacaoMail($email->payload));
        } catch (Throwable $exception) {
            $this->registarFalha($email, $exception);

            return false;
        }

        $email->update([
            'status' => 'processado',
            'destinatario' => $destinatario,
            'bloqueado_em' => null,
            'enviado_em' => now(),
            'ultimo_erro' => null,
        ]);

        return true;
    }

    private function registarFalha(OutboxEmail $email, Throwable $exception): void
    {
        $email->update([
            'status' => $email->tentativas >= self::MAX_TENTATIVAS ? 'falhado' : 'pendente',
            'bloqueado_em' => null,
            'disponivel_em' => now()->addMinutes(2 ** $email->tentativas),
            'ultimo_erro' => $exception->getMessage(),
        ]);

        Log::error('Falha ao enviar notificação de manifestação.', [
            'outbox_email_id' => $email->id,
            'manifestacao_id' => $email->manifestacao_id,
            'tentativas' => $email->tentativas,
            'exception' => $exception,
        ]);
    }
}
