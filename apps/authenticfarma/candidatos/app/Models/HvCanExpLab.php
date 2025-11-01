<?php

/**
 * Created by Reliese Model.
 */

namespace App\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;
use App\Models\HvCandidato;
use App\Models\Area;
use App\Models\Estado;
use App\Models\Pais;
use App\Models\Sector;
use App\Models\TipoCargo;

/**
 * Class HvCanExpLab
 * 
 * @property int $idhvcan_exp_laboral
 * @property string $descripcion_cargo
 * @property string $empresa
 * @property Carbon|null $fecha_creacion
 * @property Carbon|null $fecha_fin
 * @property Carbon|null $fecha_inicio
 * @property Carbon|null $fecha_modificacion
 * @property string $nombre_cargo
 * @property string|null $usuario_creacion
 * @property string|null $usuario_modificacion
 * @property int $id_area
 * @property int $id_candidato
 * @property int $id_estado
 * @property int $id_pais
 * @property int $id_sector
 * @property int $id_tipo_cargo
 *
 * @package App\Models
 */
class HvCanExpLab extends Model
{
	protected $table = 'hv_can_exp_lab';
	protected $primaryKey = 'idhvcan_exp_laboral';
	public $timestamps = false;

	protected $casts = [
		'fecha_creacion' => 'datetime',
		'fecha_fin' => 'datetime',
		'fecha_inicio' => 'datetime',
		'fecha_modificacion' => 'datetime',
		'id_area' => 'int',
		'id_candidato' => 'int',
		'id_estado' => 'int',
		'id_pais' => 'int',
		'id_sector' => 'int',
		'id_tipo_cargo' => 'int'
	];

	protected $fillable = [
		'descripcion_cargo',
		'empresa',
		'fecha_creacion',
		'fecha_fin',
		'fecha_inicio',
		'fecha_modificacion',
		'nombre_cargo',
		'usuario_creacion',
		'usuario_modificacion',
		'id_area',
		'id_candidato',
		'id_estado',
		'id_pais',
		'id_sector',
		'id_tipo_cargo'
	];

	public function candidato()
	{
		return $this->belongsTo(HvCandidato::class, 'id_candidato');
	}

	public function area()
	{
		return $this->belongsTo(Area::class, 'id_area');
	}

	public function pais()
	{
		return $this->belongsTo(Pais::class, 'id_pais');
	}

	public function sector()
	{
		return $this->belongsTo(Sector::class, 'id_sector');
	}

	public function tipoCargo()
	{
		return $this->belongsTo(TipoCargo::class, 'id_tipo_cargo');
	}
}
