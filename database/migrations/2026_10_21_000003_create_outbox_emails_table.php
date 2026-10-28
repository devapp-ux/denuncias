<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('outbox_emails', function (Blueprint $table) {
            $table->id();
            $table->foreignId('manifestacao_id')
                ->unique()
                ->constrained('manifestacoes')
                ->cascadeOnDelete();
            $table->string('destinatario')->nullable();
            $table->json('payload');
            $table->string('status')->default('pendente');
            $table->unsignedTinyInteger('tentativas')->default(0);
            $table->timestamp('disponivel_em')->nullable();
            $table->timestamp('bloqueado_em')->nullable();
            $table->timestamp('enviado_em')->nullable();
            $table->text('ultimo_erro')->nullable();
            $table->timestamps();

            $table->index(['status', 'disponivel_em']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('outbox_emails');
    }
};
