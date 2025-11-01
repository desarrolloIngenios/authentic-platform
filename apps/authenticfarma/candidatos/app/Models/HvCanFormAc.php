<?php

/**
 * Created by Reliese Model.
 */

namespace App\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;

/**
 * Class HvCanFormAc
 * 
 * @property int $idhvcan_form_ac
 * @property Carbon|null $fecha_creacion
 * @property Carbon|null $fecha_fin
 * @property Carbon|null $fecha_inicio
 * @property Carbon|null $fecha_modificacion
 * @property string|null $institucion
 * @property string|null $titulo
 * @property string|null $usuario_creacion
 * @property string|null $usuario_modificacion
 * @property int $id_candidato
 * @property int $id_estado
 * @property int $id_nivel_educacion
 * @property int $id_pais
 *
 * @package App\Models
 */
class HvCanFormAc extends Model
{
	protected $table = 'hv_can_form_ac';
	protected $primaryKey = 'idhvcan_form_ac';
	public $timestamps = false;

	protected $casts = [
		'fecha_creacion' => 'datetime',
		'fecha_fin' => 'datetime',
		'fecha_inicio' => 'datetime',
		'fecha_modificacion' => 'datetime',
		'id_candidato' => 'int',
		'id_estado' => 'int',
		'id_nivel_educacion' => 'int',
		'id_pais' => 'int'
	];

	protected $fillable = [
		'fecha_creacion',
		'fecha_fin',
		'fecha_inicio',
		'fecha_modificacion',
		'institucion',
		'titulo',
		'usuario_creacion',
		'usuario_modificacion',
		'id_candidato',
		'id_estado',
		'id_nivel_educacion',
		'id_pais'
	];
	public function candidato()
	{
		return $this->belongsTo(HvCandidato::class, 'id_candidato');
	}
	public function nivel_educacion()
	{
		return $this->belongsTo(NivelEducacion::class, 'id_nivel_educacion');
	}
	public function pais()
	{
		return $this->belongsTo(Pais::class, 'id_pais');
	}
}
