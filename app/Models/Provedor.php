<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Provedor extends Model
{
    protected $table = 'provedores';

    protected $fillable = ['nome', 'email', 'ativo'];

    protected $casts = ['ativo' => 'boolean'];

    public function manifestacoes(): HasMany
    {
        return $this->hasMany(Manifestacao::class);
    }

    public function utilizadores(): HasMany
    {
        return $this->hasMany(User::class);
    }
}
