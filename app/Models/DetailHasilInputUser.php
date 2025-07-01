<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class DetailHasilInputUser extends Model
{
	use HasFactory;

	protected $table = 'detail_hasil_input_user';
	protected $primaryKey = 'id_detail';

	protected $fillable = [
		'id_hasil_input',
		'id_kriteria',
		'hasil_kalkulasi'
	];

	public function hasilInput()
	{
		return $this->belongsTo(HasilInputUser::class, 'id_hasil_input', 'id_hasil_input');
	}

	public function kriteria()
	{
		return $this->belongsTo(MasterKriteria::class, 'id_kriteria', 'id_kriteria');
	}
}
