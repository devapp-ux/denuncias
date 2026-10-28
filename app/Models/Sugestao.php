<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Sugestao extends Model
{
    protected $table = 'sugestoes';
    protected $fillable = [
        'manifestacao_id', 'titulo', 'area_relacionada',
        'outra_area', 'sugestao', 'justificacao',
    ];

    public function manifestacao(): BelongsTo
    {
        return $this->belongsTo(Manifestacao::class);
    }
}