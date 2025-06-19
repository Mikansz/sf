<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class KaryawanTunjangan extends Model
{
    use HasFactory;

    protected $table = 'karyawan_tunjangan';

    protected $fillable = [
        'karyawan_id',
        'tunjangan_id',
        'nominal',
        'tanggal_mulai',
        'tanggal_selesai',
        'status_aktif',
    ];

    protected $casts = [
        'nominal' => 'decimal:2',
        'tanggal_mulai' => 'date',
        'tanggal_selesai' => 'date',
        'status_aktif' => 'boolean',
    ];

    public function karyawan(): BelongsTo
    {
        return $this->belongsTo(Karyawan::class);
    }

    public function tunjangan(): BelongsTo
    {
        return $this->belongsTo(Tunjangan::class);
    }
}
