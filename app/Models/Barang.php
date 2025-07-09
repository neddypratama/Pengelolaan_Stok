<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Barang extends Model
{
    use HasFactory;

    protected $fillable = [
        'kode',
        'name',
        'stok',
        'satuan_id',
        'jenis_id',
        'created_at',
        'updated_at',
    ];

    public function satuan(): BelongsTo
    {
        return $this->belongsTo(Satuan::class);
    }

    public function jenis(): BelongsTo
    {
        return $this->belongsTo(JenisBarang::class);
    }

    public function barangkeluars(): HasMany
    {
        return $this->hasMany(BarangKeluar::class, 'barang_id');
    }

    public function barangmasuks(): HasMany
    {
        return $this->hasMany(BarangMasuk::class, 'barang_id');
    }
}
