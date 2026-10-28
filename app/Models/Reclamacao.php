<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Reclamacao extends Model
{
    protected $table = 'reclamacoes';
    protected $fillable = [
        'manifestacao_id', 'assunto', 'categoria', 'outra_categoria',
        'area_servico', 'local', 'data_ocorrencia', 'continua',
        'descricao', 'impacto', 'pretensao',
    ];

    protected $casts = ['data_ocorrencia' => 'date'];

    public function manifestacao(): BelongsTo
    {
        return $this->belongsTo(Manifestacao::class);
    }
}