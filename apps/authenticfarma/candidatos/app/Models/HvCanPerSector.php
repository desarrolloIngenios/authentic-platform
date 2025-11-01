<?php

/**
 * Created by Reliese Model.
 */

namespace App\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;

/**
 * Class HvCanPerSector
 * 
 * @property int $idhvcan_per_sector
 * @property Carbon|null $fecha_creacion
 * @property Carbon|null $fecha_modificacion
 * @property string|null $usuario_creacion
 * @property string|null $usuario_modificacion
 * @property int $id_candidato
 * @property int $id_estado
 * @property int $id_sector
 *
 * @package App\Models
 */
class HvCanPerSector extends Model
{
	protected $table = 'hv_can_per_sector';
	protected $primaryKey = 'idhvcan_per_sector';
	public $timestamps = false;

	protected $casts = [
		'fecha_creacion' => 'datetime',
		'fecha_modificacion' => 'datetime',
		'id_candidato' => 'int',
		'id_estado' => 'int',
		'id_sector' => 'int'
	];

	protected $fillable = [
		'fecha_creacion',
		'fecha_modificacion',
		'usuario_creacion',
		'usuario_modificacion',
		'id_candidato',
		'id_estado',
		'id_sector'
	];

	public function candidato()
	{
		return $this->belongsTo(HvCandidato::class, 'id_candidato');
	}

	public function sector()
	{
		return $this->belongsTo(Sector::class, 'id_sector');
	}
}
