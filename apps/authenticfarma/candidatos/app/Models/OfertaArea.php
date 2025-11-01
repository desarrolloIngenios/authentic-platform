<?php

/**
 * Created by Reliese Model.
 */

namespace App\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;

/**
 * Class OfertaArea
 * 
 * @property int $id_oferta_area
 * @property Carbon|null $fecha_creacion
 * @property Carbon|null $fecha_modificacion
 * @property string|null $usuario_creacion
 * @property string|null $usuario_modificacion
 * @property int $id_area
 * @property int $id_oferta
 * @property int|null $id_estado
 *
 * @package App\Models
 */
class OfertaArea extends Model
{
	protected $table = 'oferta_area';
	protected $primaryKey = 'id_oferta_area';
	public $incrementing = false;
	public $timestamps = false;

	protected $casts = [
		'id_oferta_area' => 'int',
		'fecha_creacion' => 'datetime',
		'fecha_modificacion' => 'datetime',
		'id_area' => 'int',
		'id_oferta' => 'int',
		'id_estado' => 'int'
	];

	protected $fillable = [
		'fecha_creacion',
		'fecha_modificacion',
		'usuario_creacion',
		'usuario_modificacion',
		'id_area',
		'id_oferta',
		'id_estado'
	];


	public function ofertaLaboral()
	{
		return $this->belongsTo(OfOfertaLaboral::class, 'id_oferta', 'idofoferta_laboral');
	}

	public function area()
	{
		return $this->belongsTo(Area::class, 'id_area');
	}
}
