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
        'bobot'
    ];

    public function admin()
    {
        return $this->belongsTo(Admin::class, 'id_admin', 'id_admin');
    }
}
