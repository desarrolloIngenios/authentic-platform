<?php

/**
 * Created by Reliese Model.
 */

namespace App\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;
use App\Models\HvCandidato;


/**
 * Class HvCanCorreo
 * 
 * @property int $idhvcan_correo
 * @property string|null $email
 * @property Carbon|null $fecha_creacion
 * @property Carbon|null $fecha_modificacion
 * @property int $principal
 * @property string|null $usuario_creacion
 * @property string|null $usuario_modificacion
 * @property int $id_candidato
 * @property int $id_estado
 *
 * @package App\Models
 */
class HvCanCorreo extends Model
{
	protected $table = 'hv_can_correo';
	protected $primaryKey = 'idhvcan_correo';
	public $timestamps = false;

	protected $casts = [
		'fecha_creacion' => 'datetime',
		'fecha_modificacion' => 'datetime',
		'principal' => 'int',
		'id_candidato' => 'int',
		'id_estado' => 'int'
	];

	protected $fillable = [
		'email',
		'fecha_creacion',
		'fecha_modificacion',
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
