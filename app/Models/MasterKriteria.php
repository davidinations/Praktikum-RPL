<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class MasterKriteria extends Model
{
    use HasFactory;

    protected $table = 'master_kriteria';
    protected $primaryKey = 'id_kriteria';

    protected $fillable = [
        'id_admin',
        'nama',
        'satuan',
        'jenis',
        'bobot',
        'rating_1_min',
        'rating_1_max',
        'rating_2_min',
        'rating_2_max',
        'rating_3_min',
        'rating_3_max',
        'rating_4_min',
        'rating_4_max',
        'rating_5_min',
        'rating_5_max'
    ];

    public function admin()
    {
        return $this->belongsTo(Admin::class, 'id_admin', 'id_admin');
    }
}
