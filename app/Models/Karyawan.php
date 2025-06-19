<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class Karyawan extends Model
{
    use HasFactory;

    protected $table = 'karyawan';

    protected $fillable = [
        'user_id',
        'nik',
        'nama_lengkap',
        'tempat_lahir',
        'tanggal_lahir',
        'jenis_kelamin',
        'alamat',
        'no_telepon',
        'email',
        'jabatan',
        'divisi',
        'tanggal_masuk',
        'status',
        'gaji_pokok',
        'no_rekening',
        'nama_bank',
        'nama_pemilik_rekening',
        'npwp',
    ];

    protected $casts = [
        'tanggal_lahir' => 'date',
        'tanggal_masuk' => 'date',
        'gaji_pokok' => 'decimal:2',
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function absensi(): HasMany
    {
        return $this->hasMany(Absensi::class);
    }

    public function lembur(): HasMany
    {
        return $this->hasMany(Lembur::class);
    }

    public function penggajian(): HasMany
    {
        return $this->hasMany(Penggajian::class);
    }

    public function tunjangan(): BelongsToMany
    {
        return $this->belongsToMany(Tunjangan::class, 'karyawan_tunjangan')
            ->withPivot('nominal', 'tanggal_mulai', 'tanggal_selesai', 'status_aktif')
            ->withTimestamps();
    }

    public function karyawanTunjangan(): HasMany
    {
        return $this->hasMany(KaryawanTunjangan::class);
    }
}
