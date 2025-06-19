<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use App\Models\User;
use App\Models\Karyawan;
use App\Models\Tunjangan;

class InitialDataSeeder extends Seeder
{
    public function run(): void
    {
        // Create CEO User
        $ceo = User::create([
            'nama' => 'Direktur CEO',
            'email' => 'ceo@perusahaan.com',
            'password' => Hash::make('password123'),
            'peran' => 'CEO',
            'status_aktif' => true,
            'email_verified_at' => now(),
        ]);

        // Create HRD User
        $hrd = User::create([
            'nama' => 'Manager HRD',
            'email' => 'hrd@perusahaan.com', 
            'password' => Hash::make('password123'),
            'peran' => 'HRD',
            'status_aktif' => true,
            'email_verified_at' => now(),
        ]);

        // Create HRD Karyawan Profile
        Karyawan::create([
            'user_id' => $hrd->id,
            'nik' => 'HRD001',
            'nama_lengkap' => 'Manager HRD',
            'tempat_lahir' => 'Jakarta',
            'tanggal_lahir' => '1985-01-01',
            'jenis_kelamin' => 'LAKI_LAKI',
            'alamat' => 'Jl. Sudirman No. 1, Jakarta',
            'no_telepon' => '081234567890',
            'email' => 'hrd@perusahaan.com',
            'jabatan' => 'Manager HRD',
            'divisi' => 'Human Resources',
            'tanggal_masuk' => '2020-01-01',
            'status' => 'AKTIF',
            'gaji_pokok' => 15000000,
            'no_rekening' => '1234567890',
            'nama_bank' => 'Bank BCA',
            'nama_pemilik_rekening' => 'Manager HRD',
            'npwp' => '12.345.678.9-012.000',
        ]);

        // Create Sample Employee
        $karyawan1 = User::create([
            'nama' => 'Ahmad Rizki',
            'email' => 'ahmad.rizki@perusahaan.com',
            'password' => Hash::make('password123'),
            'peran' => 'KARYAWAN',
            'status_aktif' => true,
            'email_verified_at' => now(),
        ]);

        Karyawan::create([
            'user_id' => $karyawan1->id,
            'nik' => 'KRY001',
            'nama_lengkap' => 'Ahmad Rizki',
            'tempat_lahir' => 'Bandung',
            'tanggal_lahir' => '1990-05-15',
            'jenis_kelamin' => 'LAKI_LAKI',
            'alamat' => 'Jl. Asia Afrika No. 100, Bandung',
            'no_telepon' => '082234567890',
            'email' => 'ahmad.rizki@perusahaan.com',
            'jabatan' => 'Software Developer',
            'divisi' => 'IT',
            'tanggal_masuk' => '2022-01-15',
            'status' => 'AKTIF',
            'gaji_pokok' => 8000000,
            'no_rekening' => '0987654321',
            'nama_bank' => 'Bank Mandiri',
            'nama_pemilik_rekening' => 'Ahmad Rizki',
            'npwp' => '98.765.432.1-098.000',
        ]);

        // Create Sample Employee 2
        $karyawan2 = User::create([
            'nama' => 'Siti Nurhaliza',
            'email' => 'siti.nurhaliza@perusahaan.com',
            'password' => Hash::make('password123'),
            'peran' => 'KARYAWAN',
            'status_aktif' => true,
            'email_verified_at' => now(),
        ]);

        Karyawan::create([
            'user_id' => $karyawan2->id,
            'nik' => 'KRY002',
            'nama_lengkap' => 'Siti Nurhaliza',
            'tempat_lahir' => 'Surabaya',
            'tanggal_lahir' => '1992-08-20',
            'jenis_kelamin' => 'PEREMPUAN',
            'alamat' => 'Jl. Pemuda No. 50, Surabaya',
            'no_telepon' => '083234567890',
            'email' => 'siti.nurhaliza@perusahaan.com',
            'jabatan' => 'Marketing Executive',
            'divisi' => 'Marketing',
            'tanggal_masuk' => '2021-06-01',
            'status' => 'AKTIF',
            'gaji_pokok' => 6500000,
            'no_rekening' => '1122334455',
            'nama_bank' => 'Bank BNI',
            'nama_pemilik_rekening' => 'Siti Nurhaliza',
            'npwp' => '11.223.344.5-567.000',
        ]);

        // Create Basic Allowances (Tunjangan)
        $tunjanganTransport = Tunjangan::create([
            'nama_tunjangan' => 'Tunjangan Transport',
            'jenis' => 'TETAP',
            'nominal' => 500000,
            'deskripsi' => 'Tunjangan transportasi bulanan',
            'status_aktif' => true,
        ]);

        $tunjanganMakan = Tunjangan::create([
            'nama_tunjangan' => 'Tunjangan Makan',
            'jenis' => 'TETAP',
            'nominal' => 300000,
            'deskripsi' => 'Tunjangan makan bulanan',
            'status_aktif' => true,
        ]);

        $tunjanganKeluarga = Tunjangan::create([
            'nama_tunjangan' => 'Tunjangan Keluarga',
            'jenis' => 'TETAP',
            'nominal' => 250000,
            'deskripsi' => 'Tunjangan keluarga untuk karyawan yang sudah menikah',
            'status_aktif' => true,
        ]);

        $tunjanganJabatan = Tunjangan::create([
            'nama_tunjangan' => 'Tunjangan Jabatan',
            'jenis' => 'TETAP',
            'nominal' => 1000000,
            'deskripsi' => 'Tunjangan jabatan untuk posisi tertentu',
            'status_aktif' => true,
        ]);

        // Assign allowances to employees
        $hrdKaryawan = Karyawan::where('user_id', $hrd->id)->first();
        $hrdKaryawan->karyawanTunjangan()->create([
            'tunjangan_id' => $tunjanganTransport->id,
            'nominal' => 500000,
            'tanggal_mulai' => '2020-01-01',
            'status_aktif' => true,
        ]);

        $hrdKaryawan->karyawanTunjangan()->create([
            'tunjangan_id' => $tunjanganJabatan->id,
            'nominal' => 1000000,
            'tanggal_mulai' => '2020-01-01',
            'status_aktif' => true,
        ]);

        $ahmadKaryawan = Karyawan::where('user_id', $karyawan1->id)->first();
        $ahmadKaryawan->karyawanTunjangan()->create([
            'tunjangan_id' => $tunjanganTransport->id,
            'nominal' => 500000,
            'tanggal_mulai' => '2022-01-15',
            'status_aktif' => true,
        ]);

        $ahmadKaryawan->karyawanTunjangan()->create([
            'tunjangan_id' => $tunjanganMakan->id,
            'nominal' => 300000,
            'tanggal_mulai' => '2022-01-15',
            'status_aktif' => true,
        ]);

        $sitiKaryawan = Karyawan::where('user_id', $karyawan2->id)->first();
        $sitiKaryawan->karyawanTunjangan()->create([
            'tunjangan_id' => $tunjanganTransport->id,
            'nominal' => 500000,
            'tanggal_mulai' => '2021-06-01',
            'status_aktif' => true,
        ]);

        $sitiKaryawan->karyawanTunjangan()->create([
            'tunjangan_id' => $tunjanganMakan->id,
            'nominal' => 300000,
            'tanggal_mulai' => '2021-06-01',
            'status_aktif' => true,
        ]);
    }
}
