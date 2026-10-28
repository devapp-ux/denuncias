<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('mensagens', function (Blueprint $table) {
            $table->id();

            $table->foreignId('manifestacao_id')
                ->constrained('manifestacoes')
                ->cascadeOnDelete();

            $table->enum('autor', ['utilizador', 'instituicao']);
            $table->text('mensagem');
            $table->boolean('lida')->default(false);

            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('mensagens');
    }
};