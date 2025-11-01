<?php

/**
 * Created by Reliese Model.
 */

namespace App\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;

/**
 * Class HvCanTelefono
 * 
 * @property int $idhvcan_telefono
 * @property Carbon|null $fecha_creacion
 * @property Carbon|null $fecha_modificacion
 * @property string|null $numero_telefono
 * @property string|null $otro_numero_telefono
 * @property int $principal
 * @property string|null $usuario_creacion
 * @property string|null $usuario_modificacion
 * @property int $id_candidato
 * @property int $id_estado
 *
 * @package App\Models
 */
class HvCanTelefono extends Model
{
	protected $table = 'hv_can_telefono';
	protected $primaryKey = 'idhvcan_telefono';
	public $timestamps = false;

	protected $casts = [
		'fecha_creacion' => 'datetime',
		'fecha_modificacion' => 'datetime',
		'principal' => 'int',
		'id_candidato' => 'int',
		'id_estado' => 'int'
	];

	protected $fillable = [
		'fecha_creacion',
		'fecha_modificacion',
		'numero_telefono',
		'otro_numero_telefono',
		'principal',
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
