<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        DB::table('users')
            ->where('role', 'pimpinan')
            ->update(['role' => 'penyelia']);
    }

    public function down(): void
    {
        DB::table('users')
            ->where('role', 'penyelia')
            ->update(['role' => 'pimpinan']);
    }
};