<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Rekapan extends Model
{
    protected $fillable = [
        'aktifitas',
        'kategori',
        'nama_barang',
        'code',
        'box_masuk',
        'kgs_masuk',
        'box_keluar', 
        'kgs_keluar',
    ];
    
    protected $casts = [
        'kgs_masuk' => 'decimal:2',
        'kgs_keluar' => 'decimal:2',
    ];
    
    // Calculated fields
    public function getStokBoxAttribute()
    {
        return $this->box_masuk - $this->box_keluar;
    }
    
    public function getStokKgsAttribute()
    {
        return $this->kgs_masuk - $this->kgs_keluar;
    }
}
