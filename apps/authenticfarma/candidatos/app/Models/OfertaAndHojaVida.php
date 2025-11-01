<?php

/**
 * Created by Reliese Model.
 */

namespace App\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;

/**
 * Class OfertaAndHojaVida
 * 
 * @property int $id
 * @property int $oferta_laboral_id
 * @property int $hoja_vida_id
 * @property string $evaluacion_ai
 * @property Carbon|null $created_at
 * @property Carbon|null $updated_at
 *
 * @package App\Models
 */
class OfertaAndHojaVida extends Model
{
	protected $table = 'oferta_and_hoja_vida';
	public $incrementing = false;

	protected $casts = [
		'id' => 'int',
		'oferta_laboral_id' => 'int',
		'hoja_vida_id' => 'int'
	];

	protected $fillable = [
		'oferta_laboral_id',
		'hoja_vida_id',
		'evaluacion_ai'
	];
	
	public function ofertaLaboral()
	{
		return $this->belongsTo(OfOfertaLaboral::class, 'oferta_laboral_id');
	}

	public function hojaVida()
	{
		return $this->belongsTo(HvHojaVida::class, 'hoja_vida_id');
	}

}
