<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('reclamacoes', function (Blueprint $table) {
            $table->id();

            $table->foreignId('manifestacao_id')
                ->unique()
                ->constrained('manifestacoes')
                ->cascadeOnDelete();

            $table->string('assunto');
            $table->string('categoria');
            $table->string('outra_categoria')->nullable();
            $table->string('area_servico');
            $table->string('local')->nullable();
            $table->date('data_ocorrencia')->nullable();
            $table->string('continua')->nullable();
            $table->text('descricao');
            $table->text('impacto')->nullable();
            $table->text('pretensao')->nullable();

            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('reclamacoes');
    }
};