<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        DB::statement("UPDATE users SET nip = CONCAT('NIP', LPAD(id, 14, '0')) WHERE nip = '' OR nip IS NULL");

        Schema::table('users', function (Blueprint $table) {
            $table->string('nip', 18)->unique()->change();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->string('nip', 18)->nullable()->change();
        });
    }
};
