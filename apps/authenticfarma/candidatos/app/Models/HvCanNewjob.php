<?php

/**
 * Created by Reliese Model.
 */

namespace App\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;

/**
 * Class HvCanNewjob
 * 
 * @property int $idhvcan_new_job
 * @property Carbon|null $fecha_creacion
 * @property Carbon|null $fecha_modificacion
 * @property string|null $nombre_cargo
 * @property bool|null $pregunta1
 * @property bool|null $pregunta2
 * @property bool|null $pregunta3
 * @property string|null $texto1
 * @property string|null $texto2
 * @property string|null $texto3
 * @property string|null $texto4
 * @property string|null $texto5
 * @property string|null $texto6
 * @property string|null $texto7
 * @property string|null $usuario_creacion
 * @property string|null $usuario_modificacion
 * @property int $id_candidato
 * @property int $id_estado
 * @property int $id_rango_salario
 * @property int $id_tipo_trabajo
 * @property bool|null $is_buscando_ofertas
 * @property bool|null $is_visible_reclutadores
 *
 * @package App\Models
 */
class HvCanNewjob extends Model
{
	protected $table = 'hv_can_newjob';
	protected $primaryKey = 'idhvcan_new_job';
	public $timestamps = false;

	protected $casts = [
		'fecha_creacion' => 'datetime',
		'fecha_modificacion' => 'datetime',
		'pregunta1' => 'bool',
		'pregunta2' => 'bool',
		'pregunta3' => 'bool',
		'id_candidato' => 'int',
		'id_estado' => 'int',
		'id_rango_salario' => 'int',
		'id_tipo_trabajo' => 'int',
		'is_buscando_ofertas' => 'bool',
		'is_visible_reclutadores' => 'bool'
	];

	protected $fillable = [
		'fecha_creacion',
		'fecha_modificacion',
		'nombre_cargo',
		'pregunta1',
		'pregunta2',
		'pregunta3',
		'texto1',
		'texto2',
		'texto3',
		'texto4',
		'texto5',
		'texto6',
		'texto7',
		'usuario_creacion',
		'usuario_modificacion',
		'id_candidato',
		'id_estado',
		'id_rango_salario',
		'id_tipo_trabajo',
		'is_buscando_ofertas',
		'is_visible_reclutadores'
	];

	/**
	 * Obtiene el candidato asociado al nuevo trabajo
	 */
	public function candidato()
	{
		return $this->belongsTo(HvCandidato::class, 'id_candidato');
	}

	/**
	 * Obtiene el rango salarial asociado
	 */
	public function rangoSalario()
	{
		return $this->belongsTo(RangoSalario::class, 'id_rango_salario');
	}

	/**
	 * Obtiene el tipo de trabajo asociado
	 */
	public function tipoTrabajo()
	{
		return $this->belongsTo(TipoTrabajo::class, 'id_tipo_trabajo');
	}
}
