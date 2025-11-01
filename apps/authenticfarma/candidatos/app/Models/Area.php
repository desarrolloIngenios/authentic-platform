<?php

/**
 * Created by Reliese Model.
 */

namespace App\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;

/**
 * Class Area
 * 
 * @property int $id
 * @property string|null $descripcion
 * @property Carbon|null $fecha_creacion
 * @property Carbon|null $fecha_modificacion
 * @property string|null $nombre
 * @property string|null $usuario_creacion
 * @property string|null $usuario_modificacion
 * @property int $id_estado
 *
 * @package App\Models
 */
class Area extends Model
{
	protected $table = 'area';
	public $timestamps = false;

	protected $casts = [
		'fecha_creacion' => 'datetime',
		'fecha_modificacion' => 'datetime',
		'id_estado' => 'int'
	];

	protected $fillable = [
		'descripcion',
		'fecha_creacion',
		'fecha_modificacion',
		'nombre',
		'usuario_creacion',
		'usuario_modificacion',
		'id_estado'
	];
	public function ofertasLaborales()
	{
		return $this->belongsToMany(OfOfertaLaboral::class, 'oferta_area', 'id_area', 'id_oferta');
	}

	public function ofertaLaboral()
	{
		return $this->belongsTo(OfOfertaLaboral::class, 'id_area', 'idofoferta_laboral');
	}

	public function perfilesArea()
	{
		return $this->hasMany(HvCanPerArea::class, 'id_area');
	}
}
