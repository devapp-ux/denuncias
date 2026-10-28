<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('outbox_emails', function (Blueprint $table) {
            $table->string('destinatario')->nullable()->change();
        });
    }

    public function down(): void
    {
        if (DB::table('outbox_emails')->whereNull('destinatario')->exists()) {
            throw new RuntimeException('Configure os destinatários pendentes antes de reverter esta migration.');
        }

        Schema::table('outbox_emails', function (Blueprint $table) {
            $table->string('destinatario')->nullable(false)->change();
        });
    }
};
