<?php

/**
 * Created by Reliese Model.
 */

namespace App\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;

/**
 * Class HvCanPerArea
 * 
 * @property int $idhvcan_per_area
 * @property Carbon|null $fecha_creacion
 * @property Carbon|null $fecha_modificacion
 * @property string|null $usuario_creacion
 * @property string|null $usuario_modificacion
 * @property int $id_area
 * @property int $id_candidato
 * @property int $id_estado
 *
 * @package App\Models
 */
class HvCanPerArea extends Model
{
	protected $table = 'hv_can_per_area';
	protected $primaryKey = 'idhvcan_per_area';
	public $timestamps = false;

	protected $casts = [
		'fecha_creacion' => 'datetime',
		'fecha_modificacion' => 'datetime',
		'id_area' => 'int',
		'id_candidato' => 'int',
		'id_estado' => 'int'
	];

	protected $fillable = [
		'fecha_creacion',
		'fecha_modificacion',
		'usuario_creacion',
		'usuario_modificacion',
		'id_area',
		'id_candidato',
		'id_estado'
	];

	/**
	 * Obtiene el candidato asociado
	 */
	public function candidato()
	{
		return $this->belongsTo(HvCandidato::class, 'id_candidato');
	}

	/**
	 * Obtiene el área asociada
	 */
	public function area()
	{
		return $this->belongsTo(Area::class, 'id_area');
	}
}
