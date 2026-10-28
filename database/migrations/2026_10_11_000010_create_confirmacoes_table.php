<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('confirmacoes', function (Blueprint $table) {
            $table->id();

            $table->foreignId('manifestacao_id')
                ->unique()
                ->constrained('manifestacoes')
                ->cascadeOnDelete();

            $table->string('tipo_documento');   // carta_denuncia | carta_reclamacao | carta_sugestao
            $table->string('numero')->unique();  // DOC-MAN-2026D0001

            // --- resultado (preenchido pelo worker) ---
            $table->string('ficheiro')->nullable();
            $table->string('hash')->nullable();
            $table->timestamp('gerado_em')->nullable();

            // --- controlo de outbox ---
            $table->string('status')->default('pendente')->index(); // pendente|processando|processado|falhado
            $table->unsignedTinyInteger('tentativas')->default(0);
            $table->timestamp('disponivel_em')->nullable();
            $table->timestamp('bloqueado_em')->nullable();
            $table->timestamp('processado_em')->nullable();
            $table->text('ultimo_erro')->nullable();

            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('confirmacoes');
    }
};