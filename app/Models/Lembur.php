<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Lembur extends Model
{
    use HasFactory;

    protected $table = 'lembur';

    protected $fillable = [
        'karyawan_id',
        'tanggal',
        'jam_mulai',
        'jam_selesai',
        'total_jam_lembur',
        'tarif_per_jam',
        'total_bayar_lembur',
        'keterangan',
        'status',
    ];

    protected $casts = [
        'tanggal' => 'date',
        'total_jam_lembur' => 'decimal:2',
        'tarif_per_jam' => 'decimal:2',
        'total_bayar_lembur' => 'decimal:2',
    ];

    public function karyawan(): BelongsTo
    {
        return $this->belongsTo(Karyawan::class);
    }

    public function hitungTotalBayar(): void
    {
        $this->total_bayar_lembur = $this->total_jam_lembur * $this->tarif_per_jam;
    }

    protected static function boot()
    {
        parent::boot();

        static::saving(function ($lembur) {
            $lembur->hitungTotalBayar();
        });
    }
}
