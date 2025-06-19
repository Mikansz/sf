<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Absensi extends Model
{
    use HasFactory;

    protected $table = 'absensi';

    protected $fillable = [
        'karyawan_id',
        'tanggal',
        'jam_masuk',
        'jam_keluar',
        'lokasi_masuk',
        'lokasi_keluar',
        'keterangan',
        'status',
        'total_jam_kerja',
    ];

    protected $casts = [
        'tanggal' => 'date',
        'total_jam_kerja' => 'decimal:2',
    ];

    public function karyawan(): BelongsTo
    {
        return $this->belongsTo(Karyawan::class);
    }

    public function hitungJamKerja(): void
    {
        if ($this->jam_masuk && $this->jam_keluar) {
            $masuk = \Carbon\Carbon::parse($this->jam_masuk);
            $keluar = \Carbon\Carbon::parse($this->jam_keluar);
            $this->total_jam_kerja = $keluar->diffInHours($masuk);
        }
    }
}
