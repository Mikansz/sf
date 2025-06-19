<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Penggajian extends Model
{
    use HasFactory;

    protected $table = 'penggajian';

    protected $fillable = [
        'karyawan_id',
        'periode_gaji',
        'gaji_pokok',
        'total_tunjangan',
        'total_lembur',
        'total_kehadiran',
        'gaji_kotor',
        'gaji_bersih',
        'total_hari_kerja',
        'total_hadir',
        'total_alpha',
        'total_izin',
        'total_sakit',
        'status',
        'tanggal_bayar',
    ];

    protected $casts = [
        'gaji_pokok' => 'decimal:2',
        'total_tunjangan' => 'decimal:2',
        'total_lembur' => 'decimal:2',
        'total_kehadiran' => 'decimal:2',
        'gaji_kotor' => 'decimal:2',
        'gaji_bersih' => 'decimal:2',
        'tanggal_bayar' => 'date',
    ];

    public function karyawan(): BelongsTo
    {
        return $this->belongsTo(Karyawan::class);
    }

    public function hitungGajiKotor(): void
    {
        $this->gaji_kotor = $this->gaji_pokok + $this->total_tunjangan + $this->total_lembur + $this->total_kehadiran;
    }

    public function hitungGajiBersih(): void
    {
        $this->hitungGajiKotor();
        $this->gaji_bersih = $this->gaji_kotor;
    }

    protected static function boot()
    {
        parent::boot();

        static::saving(function ($penggajian) {
            $penggajian->hitungGajiBersih();
        });
    }
}
