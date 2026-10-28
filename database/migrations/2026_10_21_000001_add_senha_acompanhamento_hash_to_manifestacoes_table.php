<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('manifestacoes', function (Blueprint $table) {
            $table->string('senha_acompanhamento_hash')->nullable();
        });
    }

    public function down(): void
    {
        Schema::table('manifestacoes', function (Blueprint $table) {
            $table->dropColumn('senha_acompanhamento_hash');
        });
    }
};
