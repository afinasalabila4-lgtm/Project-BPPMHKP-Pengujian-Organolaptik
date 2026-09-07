<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('session_users', function (Blueprint $table) {
            // nama nullable karena untuk panelis manual tanpa akun user
            // Kalau pakai user_id, nama diambil dari relasi user
            $table->string('nama')->nullable()->change();
        });
    }

    public function down(): void
    {
        Schema::table('session_users', function (Blueprint $table) {
            $table->string('nama')->nullable(false)->change();
        });
    }
};
