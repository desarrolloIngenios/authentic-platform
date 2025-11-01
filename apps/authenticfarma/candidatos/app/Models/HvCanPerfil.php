<?php

/**
 * Created by Reliese Model.
 */

namespace App\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;

/**
 * Class HvCanPerfil
 * 
 * @property int $idhvcan_perfil
 * @property string|null $descripcion_perfil
 * @property Carbon|null $fecha_creacion
 * @property Carbon|null $fecha_modificacion
 * @property string|null $usuario_creacion
 * @property string|null $usuario_modificacion
 * @property int $id_candidato
 * @property int $id_estado
 *
 * @package App\Models
 */
class HvCanPerfil extends Model
{
	protected $table = 'hv_can_perfil';
	protected $primaryKey = 'idhvcan_perfil';
	public $timestamps = false;

	protected $casts = [
		'fecha_creacion' => 'datetime',
		'fecha_modificacion' => 'datetime',
		'id_candidato' => 'int',
		'id_estado' => 'int'
	];

	protected $fillable = [
		'descripcion_perfil',
		'fecha_creacion',
		'fecha_modificacion',
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
