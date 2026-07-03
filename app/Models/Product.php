<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Product extends Model
{
    protected $fillable = [
        'kode_barang', 'nama_barang', 'category_id', 'stok', 'lokasi_penyimpanan', 'kondisi_barang', 'image'
    ];

    public function category()
    {
        return $this->belongsTo(Category::class);
    }
}
