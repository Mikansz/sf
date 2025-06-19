<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('penggajian', function (Blueprint $table) {
            $table->id();
            $table->foreignId('karyawan_id')->constrained('karyawan')->cascadeOnDelete();
            $table->string('periode_gaji', 20); // Format: YYYY-MM
            $table->decimal('gaji_pokok', 15, 2);
            $table->decimal('total_tunjangan', 15, 2)->default(0);
            $table->decimal('total_lembur', 15, 2)->default(0);
            $table->decimal('total_kehadiran', 15, 2)->default(0);
            $table->decimal('gaji_kotor', 15, 2);
            $table->decimal('gaji_bersih', 15, 2);
            $table->integer('total_hari_kerja');
            $table->integer('total_hadir');
            $table->integer('total_alpha')->default(0);
            $table->integer('total_izin')->default(0);
            $table->integer('total_sakit')->default(0);
            $table->enum('status', ['DRAFT', 'DISETUJUI', 'DIBAYAR'])->default('DRAFT');
            $table->date('tanggal_bayar')->nullable();
            $table->timestamps();
            
            $table->unique(['karyawan_id', 'periode_gaji']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('penggajian');
    }
};
