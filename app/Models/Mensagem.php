<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Mensagem extends Model
{
    protected $table = 'mensagens';

    protected $fillable = ['manifestacao_id', 'autor', 'mensagem', 'lida'];
    protected $casts = ['lida' => 'boolean'];

    public function manifestacao(): BelongsTo
    {
        return $this->belongsTo(Manifestacao::class);
    }
}