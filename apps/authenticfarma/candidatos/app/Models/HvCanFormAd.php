<?php

/**
 * Created by Reliese Model.
 */

namespace App\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;

/**
 * Class HvCanFormAd
 * 
 * @property int $idhvcan_form_ad
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
 *
 * @package App\Models
 */
class HvCanFormAd extends Model
{
	protected $table = 'hv_can_form_ad';
	protected $primaryKey = 'idhvcan_form_ad';
	public $timestamps = false;

	protected $casts = [
		'fecha_creacion' => 'datetime',
		'fecha_fin' => 'datetime',
		'fecha_inicio' => 'datetime',
		'fecha_modificacion' => 'datetime',
		'id_candidato' => 'int',
		'id_estado' => 'int'
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
		'id_estado'
	];

	public function candidato()
	{
		return $this->belongsTo(HvCandidato::class, 'id_candidato');
	}
}
