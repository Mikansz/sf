<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->string('nama', 100)->after('id');
            $table->enum('peran', ['CEO', 'HRD', 'KARYAWAN'])->after('email_verified_at');
            $table->boolean('status_aktif')->default(true)->after('peran');
        });
    }

    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn(['nama', 'peran', 'status_aktif']);
        });
    }
};
