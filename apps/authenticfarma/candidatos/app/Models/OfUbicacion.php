<?php

/**
 * Created by Reliese Model.
 */

namespace App\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;

/**
 * Class OfUbicacion
 * 
 * @property int $idofoferta_ubicacion
 * @property string|null $direccion
 * @property Carbon|null $fecha_creacion
 * @property Carbon|null $fecha_modificacion
 * @property string|null $usuario_creacion
 * @property string|null $usuario_modificacion
 * @property int $id_ciudad
 * @property int $id_departamento
 * @property int $id_estado
 * @property int $idofoferta_laboral
 * @property int $id_pais
 *
 * @package App\Models
 */
class OfUbicacion extends Model
{
	protected $table = 'of_ubicacion';
	protected $primaryKey = 'idofoferta_ubicacion';
	public $incrementing = false;
	public $timestamps = false;

	protected $casts = [
		'idofoferta_ubicacion' => 'int',
		'fecha_creacion' => 'datetime',
		'fecha_modificacion' => 'datetime',
		'id_ciudad' => 'int',
		'id_departamento' => 'int',
		'id_estado' => 'int',
		'idofoferta_laboral' => 'int',
		'id_pais' => 'int'
	];

	protected $fillable = [
		'direccion',
		'fecha_creacion',
		'fecha_modificacion',
		'usuario_creacion',
		'usuario_modificacion',
		'id_ciudad',
		'id_departamento',
		'id_estado',
		'idofoferta_laboral',
		'id_pais'
	];

	public function ofertaLaboral()
    {
        return $this->belongsTo(OfOfertaLaboral::class, 'oferta_laboral_id');
    }
}
