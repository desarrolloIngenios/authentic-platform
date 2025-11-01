<?php

/**
 * Created by Reliese Model.
 */

namespace App\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;

/**
 * Class HvCanIdioma
 * 
 * @property int $idhvcan_idioma
 * @property bool|null $certificado
 * @property string|null $detalle
 * @property Carbon|null $fecha_creacion
 * @property Carbon|null $fecha_modificacion
 * @property string|null $usuario_creacion
 * @property string|null $usuario_modificacion
 * @property int $id_candidato
 * @property int $id_estado
 * @property int $id_idioma
 * @property int $id_nivel_idioma
 *
 * @package App\Models
 */
class HvCanIdioma extends Model
{
	protected $table = 'hv_can_idioma';
	protected $primaryKey = 'idhvcan_idioma';
	public $timestamps = false;

	protected $casts = [
		'certificado' => 'bool',
		'fecha_creacion' => 'datetime',
		'fecha_modificacion' => 'datetime',
		'id_candidato' => 'int',
		'id_estado' => 'int',
		'id_idioma' => 'int',
		'id_nivel_idioma' => 'int'
	];

	protected $fillable = [
		'certificado',
		'detalle',
		'fecha_creacion',
		'fecha_modificacion',
		'usuario_creacion',
		'usuario_modificacion',
		'id_candidato',
		'id_estado',
		'id_idioma',
		'id_nivel_idioma'
	];

	public function candidato()
	{
		return $this->belongsTo(HvCandidato::class, 'id_candidato');
	}
	public function idioma()
	{
		return $this->belongsTo(Idioma::class, 'id_idioma');
	}
	public function nivelIdioma()
	{
		return $this->belongsTo(NivelIdioma::class, 'id_nivel_idioma');
	}
}
