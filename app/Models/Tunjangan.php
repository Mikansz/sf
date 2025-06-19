<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Tunjangan extends Model
{
    use HasFactory;

    protected $table = 'tunjangan';

    protected $fillable = [
        'nama_tunjangan',
        'jenis',
        'nominal',
        'deskripsi',
        'status_aktif',
    ];

    protected $casts = [
        'nominal' => 'decimal:2',
        'status_aktif' => 'boolean',
    ];

    public function karyawan(): BelongsToMany
    {
        return $this->belongsToMany(Karyawan::class, 'karyawan_tunjangan')
            ->withPivot('nominal', 'tanggal_mulai', 'tanggal_selesai', 'status_aktif')
            ->withTimestamps();
    }

    public function karyawanTunjangan(): HasMany
    {
        return $this->hasMany(KaryawanTunjangan::class);
    }
}
