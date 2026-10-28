<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;

class Manifestacao extends Model
{
    protected $table = 'manifestacoes';
    const ESTADOS = ['pendente', 'encaminhada', 'em_analise', 'respondida', 'arquivada'];

    protected $fillable = [
        'codigo', 'tipo', 'provedor_id', 'anonimo',
        'nome', 'email', 'celular',
        'confidencialidade', 'confirmacao',
        'estado', 'encaminhada_em', 'senha_acompanhamento_hash',
    ];

    protected $casts = [
        'anonimo' => 'boolean',
        'confidencialidade' => 'boolean',
        'confirmacao' => 'boolean',
        'encaminhada_em' => 'datetime',
    ];

    public function provedor(): BelongsTo
    {
        return $this->belongsTo(Provedor::class);
    }

    public function evidencias(): HasMany
    {
        return $this->hasMany(Evidencia::class);
    }

    public function encaminhamentos(): HasMany
    {
        return $this->hasMany(Encaminhamento::class);
    }

    public function denuncia(): HasOne
    {
        return $this->hasOne(Denuncia::class);
    }

    public function reclamacao(): HasOne
    {
        return $this->hasOne(Reclamacao::class);
    }

    public function sugestao(): HasOne
    {
        return $this->hasOne(Sugestao::class);
    }

    public function getDetalheAttribute(): ?Model
    {
        return match ($this->tipo) {
            'denuncia' => $this->denuncia,
            'reclamacao' => $this->reclamacao,
            'sugestao' => $this->sugestao,
            default => null,
        };
    }

    public function mensagens(): HasMany
    {
        return $this->hasMany(Mensagem::class)->orderBy('created_at');
    }
}