<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class InputUser extends Model
{
	use HasFactory;

	protected $table = 'input_user';
	protected $primaryKey = 'id_data';

	protected $fillable = [
		'id_input',
		'id_user',
		'id_kriteria',
		'value',
		'actual_value'
	];

	public function user()
	{
		return $this->belongsTo(User::class, 'id_user', 'id_user');
	}

	public function kriteria()
	{
		return $this->belongsTo(MasterKriteria::class, 'id_kriteria', 'id_kriteria');
	}

	// Relationship to hasil_input_user via id_input
	public function hasilInputUser()
	{
		return $this->hasMany(HasilInputUser::class, 'id_input', 'id_input');
	}

	// Get all input data for a specific input session
	public static function getInputSession($idInput)
	{
		return self::where('id_input', $idInput)
			->with('kriteria')
			->get();
	}

	// Get all input data for a specific input session with results
	public static function getInputSessionWithResults($idInput)
	{
		return self::where('id_input', $idInput)
			->with(['kriteria', 'hasilInputUser.laptop'])
			->get();
	}

	// Get the results for this input session
	public function getResults()
	{
		return $this->hasilInputUser()->with('laptop')->orderBy('ranking', 'asc')->get();
	}
}
