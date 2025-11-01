<?php

/**
 * Created by Reliese Model.
 */

namespace App\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;

/**
 * Class HvCanUbicacion
 * 
 * @property int $idhvcan_ubicacion
 * @property string|null $direccion
 * @property Carbon|null $fecha_creacion
 * @property Carbon|null $fecha_modificacion
 * @property int $principal
 * @property string|null $usuario_creacion
 * @property string|null $usuario_modificacion
 * @property int $id_candidato
 * @property int $id_ciudad_nacimiento
 * @property int $id_ciudad_residencia
 * @property int|null $id_departamento_nacimiento
 * @property int|null $id_departamento_residencia
 * @property int $id_estado
 * @property int $id_pais_nacimiento
 * @property int $id_pais_residencia
 *
 * @package App\Models
 */
class HvCanUbicacion extends Model
{
	protected $table = 'hv_can_ubicacion';
	protected $primaryKey = 'idhvcan_ubicacion';
	public $timestamps = false;

	protected $casts = [
		'fecha_creacion' => 'datetime',
		'fecha_modificacion' => 'datetime',
		'principal' => 'int',
		'id_candidato' => 'int',
		'id_ciudad_nacimiento' => 'int',
		'id_ciudad_residencia' => 'int',
		'id_departamento_nacimiento' => 'int',
		'id_departamento_residencia' => 'int',
		'id_estado' => 'int',
		'id_pais_nacimiento' => 'int',
		'id_pais_residencia' => 'int'
	];

	protected $fillable = [
		'direccion',
		'fecha_creacion',
		'fecha_modificacion',
		'principal',
		'usuario_creacion',
		'usuario_modificacion',
		'id_candidato',
		'id_ciudad_nacimiento',
		'id_ciudad_residencia',
		'id_departamento_nacimiento',
		'id_departamento_residencia',
		'id_estado',
		'id_pais_nacimiento',
		'id_pais_residencia'
	];

	public function candidato()
	{
		return $this->belongsTo(HvCandidato::class, 'id_candidato');
	}

	public function paisNacimiento()
	{
		return $this->belongsTo(Pais::class, 'id_pais_nacimiento');
	}

	public function paisResidencia()
	{
		return $this->belongsTo(Pais::class, 'id_pais_residencia');
	}

	public function departamentoNacimiento()
	{
		return $this->belongsTo(Departamento::class, 'id_departamento_nacimiento');
	}

	public function departamentoResidencia()
	{
		return $this->belongsTo(Departamento::class, 'id_departamento_residencia');
	}

	public function ciudadNacimiento()
	{
		return $this->belongsTo(Ciudad::class, 'id_ciudad_nacimiento');
	}

	public function ciudadResidencia()
	{
		return $this->belongsTo(Ciudad::class, 'id_ciudad_residencia');
	}

}
