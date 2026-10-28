<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class OutboxEmail extends Model
{
    protected $fillable = [
        'manifestacao_id',
        'destinatario',
        'payload',
        'status',
        'tentativas',
        'disponivel_em',
        'bloqueado_em',
        'enviado_em',
        'ultimo_erro',
    ];

    protected $casts = [
        'payload' => 'array',
        'disponivel_em' => 'datetime',
        'bloqueado_em' => 'datetime',
        'enviado_em' => 'datetime',
    ];

    public function manifestacao(): BelongsTo
    {
        return $this->belongsTo(Manifestacao::class);
    }
}
