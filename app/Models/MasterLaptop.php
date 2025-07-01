<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class MasterLaptop extends Model
{
    use HasFactory;

    protected $table = 'master_laptop';
    protected $primaryKey = 'id_laptop';

    protected $fillable = [
        'id_admin',
        'merek',
        'model',
        'harga',
        'processor',
        'ram',
        'storage',
        'gpu',
        'ukuran_baterai',
        'gambar'
    ];

    public function admin()
    {
        return $this->belongsTo(Admin::class, 'id_admin', 'id_admin');
    }
}
