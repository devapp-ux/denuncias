<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Evidencia extends Model
{
    protected $fillable = [
        'manifestacao_id', 'nome_original', 'nome_arquivo',
        'caminho', 'tipo', 'tamanho',
    ];

    public function manifestacao(): BelongsTo
    {
        return $this->belongsTo(Manifestacao::class);
    }
}