<?php

/**
 * Created by Reliese Model.
 */

namespace App\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;

/**
 * Class OfRequerimiento
 * 
 * @property int $idofoferta_requerimiento
 * @property int|null $cantidad_vacantes
 * @property int|null $experienciaanios
 * @property Carbon|null $fecha_creacion
 * @property Carbon|null $fecha_modificacion
 * @property string|null $usuario_creacion
 * @property string|null $usuario_modificacion
 * @property int $id_cargo
 * @property int $id_ciudad
 * @property int $id_departamento
 * @property int $id_estado
 * @property int $id_horario_tipo_constrato
 * @property int $id_nivel_educacion
 * @property int $idofoferta_laboral
 * @property int $id_pais
 * @property int $id_rango_salario
 * @property int $id_sector
 * @property int $id_tipo_trabajo
 *
 * @package App\Models
 */
class OfRequerimiento extends Model
{
	protected $table = 'of_requerimiento';
	protected $primaryKey = 'idofoferta_requerimiento';
	public $incrementing = false;
	public $timestamps = false;

	protected $casts = [
		'idofoferta_requerimiento' => 'int',
		'cantidad_vacantes' => 'int',
		'experienciaanios' => 'int',
		'fecha_creacion' => 'datetime',
		'fecha_modificacion' => 'datetime',
		'id_cargo' => 'int',
		'id_ciudad' => 'int',
		'id_departamento' => 'int',
		'id_estado' => 'int',
		'id_horario_tipo_constrato' => 'int',
		'id_nivel_educacion' => 'int',
		'idofoferta_laboral' => 'int',
		'id_pais' => 'int',
		'id_rango_salario' => 'int',
		'id_sector' => 'int',
		'id_tipo_trabajo' => 'int'
	];

	protected $fillable = [
		'cantidad_vacantes',
		'experienciaanios',
		'fecha_creacion',
		'fecha_modificacion',
		'usuario_creacion',
		'usuario_modificacion',
		'id_cargo',
		'id_ciudad',
		'id_departamento',
		'id_estado',
		'id_horario_tipo_constrato',
		'id_nivel_educacion',
		'idofoferta_laboral',
		'id_pais',
		'id_rango_salario',
		'id_sector',
		'id_tipo_trabajo'
	];

	public function ofertaLaboral()
    {
        return $this->belongsTo(OfOfertaLaboral::class, 'oferta_laboral_id');
    }
}
