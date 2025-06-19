<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('karyawan', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained('users')->cascadeOnDelete();
            $table->string('nik', 20)->unique();
            $table->string('nama_lengkap', 100);
            $table->string('tempat_lahir', 50);
            $table->date('tanggal_lahir');
            $table->enum('jenis_kelamin', ['LAKI_LAKI', 'PEREMPUAN']);
            $table->text('alamat');
            $table->string('no_telepon', 15);
            $table->string('email', 100)->unique();
            $table->string('jabatan', 50);
            $table->string('divisi', 50);
            $table->date('tanggal_masuk');
            $table->enum('status', ['AKTIF', 'TIDAK_AKTIF', 'CUTI'])->default('AKTIF');
            $table->decimal('gaji_pokok', 15, 2);
            $table->string('no_rekening', 50)->nullable();
            $table->string('nama_bank', 100)->nullable();
            $table->string('nama_pemilik_rekening', 100)->nullable();
            $table->string('npwp', 50)->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('karyawan');
    }
};
