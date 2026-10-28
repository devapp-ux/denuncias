<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (Schema::hasTable('confirmacoes') && DB::table('confirmacoes')->exists()) {
            throw new RuntimeException('A tabela confirmacoes contém dados. Preserve-os antes de removê-la.');
        }

        Schema::dropIfExists('confirmacoes');
    }

    public function down(): void
    {
        Schema::create('confirmacoes', function (Blueprint $table) {
            $table->id();
            $table->foreignId('manifestacao_id')
                ->unique()
                ->constrained('manifestacoes')
                ->cascadeOnDelete();
            $table->string('tipo_documento');
            $table->string('numero')->unique();
            $table->string('ficheiro')->nullable();
            $table->string('hash')->nullable();
            $table->timestamp('gerado_em')->nullable();
            $table->string('status')->default('pendente')->index();
            $table->unsignedTinyInteger('tentativas')->default(0);
            $table->timestamp('disponivel_em')->nullable();
            $table->timestamp('bloqueado_em')->nullable();
            $table->timestamp('processado_em')->nullable();
            $table->text('ultimo_erro')->nullable();
            $table->timestamps();
        });
    }
};
