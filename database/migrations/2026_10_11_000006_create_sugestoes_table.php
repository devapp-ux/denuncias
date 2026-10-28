<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('sugestoes', function (Blueprint $table) {
            $table->id();

            $table->foreignId('manifestacao_id')
                ->unique()
                ->constrained('manifestacoes')
                ->cascadeOnDelete();

            $table->string('titulo');
            $table->string('area_relacionada');
            $table->string('outra_area')->nullable();
            $table->text('sugestao');
            $table->text('justificacao')->nullable();

            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('sugestoes');
    }
};