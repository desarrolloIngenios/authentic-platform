<?php

/**
 * Created by Reliese Model.
 */

namespace App\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;

/**
 * Class OfertaLaboralCargo
 * 
 * @property int $id
 * @property int $id_oferta_laboral
 * @property int $id_cargo
 * @property Carbon|null $created_at
 * @property Carbon|null $updated_at
 *
 * @package App\Models
 */
class OfertaLaboralCargo extends Model
{
	protected $table = 'oferta_laboral_cargo';
	public $incrementing = false;

	protected $casts = [
		'id' => 'int',
		'id_oferta_laboral' => 'int',
		'id_cargo' => 'int'
	];

	protected $fillable = [
		'id_oferta_laboral',
		'id_cargo'
	];

}
