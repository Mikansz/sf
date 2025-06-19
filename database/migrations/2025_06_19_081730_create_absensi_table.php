<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('absensi', function (Blueprint $table) {
            $table->id();
            $table->foreignId('karyawan_id')->constrained('karyawan')->cascadeOnDelete();
            $table->date('tanggal');
            $table->time('jam_masuk')->nullable();
            $table->time('jam_keluar')->nullable();
            $table->string('lokasi_masuk', 50)->nullable();
            $table->string('lokasi_keluar', 50)->nullable();
            $table->text('keterangan')->nullable();
            $table->enum('status', ['HADIR', 'IZIN', 'SAKIT', 'ALPHA', 'CUTI'])->default('HADIR');
            $table->decimal('total_jam_kerja', 4, 2)->default(0);
            $table->timestamps();
            
            $table->unique(['karyawan_id', 'tanggal']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('absensi');
    }
};
