<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Denuncia extends Model
{
    protected $fillable = [
        'manifestacao_id', 'assunto', 'categoria', 'outra_categoria',
        'local', 'data_ocorrencia', 'continua', 'unidade_organica', 'descricao',
        'pessoas_envolvidas', 'identificacao_pessoas',
        'testemunhas', 'identificacao_testemunhas',
    ];

    protected $casts = ['data_ocorrencia' => 'date'];

    public function manifestacao(): BelongsTo
    {
        return $this->belongsTo(Manifestacao::class);
    }
}