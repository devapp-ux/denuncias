<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('encaminhamentos', function (Blueprint $table) {
            $table->id();

            $table->foreignId('manifestacao_id')
                ->constrained('manifestacoes')
                ->cascadeOnDelete();

            $table->foreignId('provedor_id')
                ->nullable()
                ->constrained('provedores')
                ->nullOnDelete();

            $table->string('estado_anterior')->nullable();
            $table->string('estado_novo');
            $table->text('observacao')->nullable();
            $table->unsignedBigInteger('user_id')->nullable();

            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('encaminhamentos');
    }
};