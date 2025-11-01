<?php

/**
 * Created by Reliese Model.
 */

namespace App\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;

/**
 * Class NivelIdioma
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
class NivelIdioma extends Model
{
	protected $table = 'nivel_idioma';
	public $incrementing = false;
	public $timestamps = false;

	protected $casts = [
		'id' => 'int',
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

	public function idioma()
	{
		return $this->hasMany(HvCanIdioma::class, 'id_candidato');
	}
	public function ofertasLaborales()
	{
		return $this->hasMany(OfOfertaLaboral::class, 'id_nivel_idioma');
	}
}
