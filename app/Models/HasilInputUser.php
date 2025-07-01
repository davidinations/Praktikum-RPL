<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class HasilInputUser extends Model
{
	use HasFactory;

	protected $table = 'hasil_input_user';
	protected $primaryKey = 'id_hasil_input';

	protected $fillable = [
		'id_input',
		'id_laptop',
		'rating',
		'ranking'
	];

	public function laptop()
	{
		return $this->belongsTo(MasterLaptop::class, 'id_laptop', 'id_laptop');
	}

	public function detailHasil()
	{
		return $this->hasMany(DetailHasilInputUser::class, 'id_hasil_input', 'id_hasil_input');
	}

	// Get ranking results for a specific input session
	public static function getRankingResults($idInput)
	{
		return self::where('id_input', $idInput)
			->with('laptop')
			->orderBy('ranking', 'asc')
			->get();
	}
}
