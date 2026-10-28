<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Encaminhamento extends Model
{
    protected $fillable = [
        'manifestacao_id', 'provedor_id',
        'estado_anterior', 'estado_novo', 'observacao', 'user_id',
    ];

    public function manifestacao(): BelongsTo
    {
        return $this->belongsTo(Manifestacao::class);
    }

    public function provedor(): BelongsTo
    {
        return $this->belongsTo(Provedor::class);
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }
}
