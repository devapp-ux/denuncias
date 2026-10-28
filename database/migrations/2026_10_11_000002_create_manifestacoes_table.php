<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('manifestacoes', function (Blueprint $table) {
            $table->id();

            $table->string('codigo')->unique();
            $table->string('tipo')->index(); // denuncia | reclamacao | sugestao

            // $table->foreignId('provedor_id')
            //     ->nullable()
            //     ->constrained('provedores')
            //     ->nullOnDelete();

            $table->foreignId('provedor_id')
                ->nullable()
                ->constrained('provedores')
                ->nullOnDelete();

            $table->boolean('anonimo')->default(false);
            $table->string('nome')->nullable();
            $table->string('email')->nullable();
            $table->string('celular')->nullable();

            $table->boolean('confidencialidade')->default(false);
            $table->boolean('confirmacao')->default(false);

            $table->string('estado')->default('pendente')->index();
            $table->timestamp('encaminhada_em')->nullable();

            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('manifestacoes');
    }
};