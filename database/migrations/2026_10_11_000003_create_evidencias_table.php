<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('evidencias', function (Blueprint $table) {
            $table->id();

            $table->foreignId('manifestacao_id')
                ->constrained('manifestacoes')
                ->cascadeOnDelete();

            $table->string('nome_original');
            $table->string('nome_arquivo');
            $table->string('caminho');
            $table->string('tipo')->nullable();
            $table->unsignedBigInteger('tamanho')->nullable();

            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('evidencias');
    }
};