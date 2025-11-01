<?php

/**
 * Created by Reliese Model.
 */

namespace App\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;

/**
 * Class OfertaAndHojaVidaSeleccionado
 * 
 * @property int $id
 * @property int $oferta_laboral_id
 * @property int $hoja_vida_id
 * @property Carbon|null $created_at
 * @property Carbon|null $updated_at
 *
 * @package App\Models
 */
class OfertaAndHojaVidaSeleccionado extends Model
{
	protected $table = 'oferta_and_hoja_vida_seleccionados';
	public $incrementing = false;

	protected $casts = [
		'id' => 'int',
		'oferta_laboral_id' => 'int',
		'hoja_vida_id' => 'int'
	];

	protected $fillable = [
		'oferta_laboral_id',
		'hoja_vida_id'
	];

	
}
