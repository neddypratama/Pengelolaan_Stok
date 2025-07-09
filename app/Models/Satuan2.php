<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Satuan2 extends Model
{
    use HasFactory;
    protected $connection = 'mysql2';
    protected $table = 'satuans'; 

    protected $fillable = [
        'name',
        'created_at',
        'updated_at',
    ];
    
    public function barangs(): HasMany
    {
        return $this->hasMany(Barang::class, 'satuan_id');
    }
}
