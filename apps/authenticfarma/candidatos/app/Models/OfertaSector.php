<?php

/**
 * Created by Reliese Model.
 */

namespace App\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;

/**
 * Class OfertaSector
 * 
 * @property int $id_oferta_sector
 * @property Carbon|null $fecha_creacion
 * @property Carbon|null $fecha_modificacion
 * @property string|null $usuario_creacion
 * @property string|null $usuario_modificacion
 * @property int $id_sector
 * @property int $id_oferta
 * @property int|null $id_estado
 *
 * @package App\Models
 */
class OfertaSector extends Model
{
	protected $table = 'oferta_sector';
	protected $primaryKey = 'id_oferta_sector';
	public $incrementing = false;
	public $timestamps = false;

	protected $casts = [
		'id_oferta_sector' => 'int',
		'fecha_creacion' => 'datetime',
		'fecha_modificacion' => 'datetime',
		'id_sector' => 'int',
		'id_oferta' => 'int',
		'id_estado' => 'int'
	];

	protected $fillable = [
		'fecha_creacion',
		'fecha_modificacion',
		'usuario_creacion',
		'usuario_modificacion',
		'id_sector',
		'id_oferta',
		'id_estado'
	];

	
	public function ofertaLaboral()
	{
		return $this->belongsTo(OfOfertaLaboral::class, 'id_oferta', 'idofoferta_laboral');
	}

	public function sector()
	{
		return $this->belongsTo(Sector::class, 'id_sector');
	}
}
