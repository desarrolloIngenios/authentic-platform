<?php

/**
 * Created by Reliese Model.
 */

namespace App\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;

/**
 * Class RangoSalario
 * 
 * @property int $id
 * @property Carbon|null $fecha_creacion
 * @property Carbon|null $fecha_modificacion
 * @property float|null $maximo
 * @property float|null $minimo
 * @property string|null $usuario_creacion
 * @property string|null $usuario_modificacion
 * @property int $id_estado
 *
 * @package App\Models
 */
class RangoSalario extends Model
{
	protected $table = 'rango_salario';
	public $incrementing = false;
	public $timestamps = false;

	protected $casts = [
		'id' => 'int',
		'fecha_creacion' => 'datetime',
		'fecha_modificacion' => 'datetime',
		'maximo' => 'float',
		'minimo' => 'float',
		'id_estado' => 'int'
	];

	protected $fillable = [
		'fecha_creacion',
		'fecha_modificacion',
		'maximo',
		'minimo',
		'usuario_creacion',
		'usuario_modificacion',
		'id_estado'
	];
	public function ofertasLaborales()
	{
		return $this->hasMany(OfOfertaLaboral::class, 'id_rango_salario');
	}

	public function nuevosTrabajos()
	{
		return $this->hasMany(HvCanNewjob::class, 'id_rango_salario');
	}
}
